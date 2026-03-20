<?php

namespace App\Http\Controllers;

use App\Models\DmPhongBan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PhongBanController extends Controller
{
    public function DanhSachView()
    {
        $phongBans = DmPhongBan::all();
        return view('departments.index', compact('phongBans'));
    }

    public function TaoView()
    {
        return view('departments.add');
    }

    public function Tao(Request $request)
    {
        $validated = $request->validate([
            'Ten' => 'required|string|max:255|unique:dm_phong_bans,Ten',
        ], [
            'Ten.required' => 'Tên phòng ban không được để trống.',
            'Ten.unique' => 'Tên phòng ban đã tồn tại.',
        ]);

        try {
            DB::beginTransaction();

            $currentCount = DmPhongBan::count();
            $nextNumber = $currentCount + 1;
            $ma = 'PB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Đảm bảo không trùng mã nếu có người dùng khác cũng đang tạo
            while (DmPhongBan::where('Ma', $ma)->lockForUpdate()->exists()) {
                $nextNumber++;
                $ma = 'PB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            $validated['Ma'] = $ma;
            $phongBan = DmPhongBan::create($validated);
            \App\Services\SystemLogService::log('Tạo mới', 'DmPhongBan', $phongBan->id, "Thêm phòng ban mới: {$phongBan->Ten}");

            DB::commit();

            return redirect()->route('phong-ban.danh-sach')
                ->with('success', 'Thêm phòng ban thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function InfoView($id)
    {
        $phongBan = DmPhongBan::findOrFail($id);
        return view('departments.info', compact('phongBan'));
    }

    public function SuaView($id)
    {
        $phongBan = DmPhongBan::findOrFail($id);
        return view('departments.edit', compact('phongBan'));
    }

    public function CapNhat(Request $request, $id)
    {
        $phongBan = DmPhongBan::findOrFail($id);

        $validated = $request->validate([
            'Ten' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dm_phong_bans', 'Ten')->ignore($phongBan->id),
            ],
        ], [
            'Ten.required' => 'Tên phòng ban không được để trống.',
            'Ten.unique' => 'Tên phòng ban đã tồn tại.',
        ]);

        try {
            $oldData = $phongBan->toArray();
            $phongBan->update($validated);
            $newData = $phongBan->fresh()->toArray();
            \App\Services\SystemLogService::log('Cập nhật', 'DmPhongBan', $phongBan->id, "Cập nhật phòng ban: {$phongBan->Ten}", $oldData, $newData);

            return redirect()->route('phong-ban.danh-sach')
                ->with('success', 'Cập nhật phòng ban thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function Xoa($id)
    {
        try {
            $phongBan = DmPhongBan::findOrFail($id);

            // Kiểm tra xem phòng ban có nhân viên hay không trước khi xóa
            if ($phongBan->nhanViens()->count() > 0) {
                return redirect()->back()->with('error', 'Không thể xóa phòng ban đang có nhân viên.');
            }

            // Kiểm tra xem có tổ đội nào thuộc phòng ban này không (nếu có model ToDoi)
            // if ($phongBan->toDois()->count() > 0) ... 

            $tenPB = $phongBan->Ten;
            $idPB = $phongBan->id;
            $phongBan->delete();
            \App\Services\SystemLogService::log('Xóa', 'DmPhongBan', $idPB, "Xóa phòng ban: {$tenPB}");

            return redirect()->route('phong-ban.danh-sach')
                ->with('success', 'Xóa phòng ban thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa phòng ban: ' . $e->getMessage());
        }
    }
}
