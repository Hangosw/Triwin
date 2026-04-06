<?php

namespace App\Http\Controllers;

use App\Models\DmChucVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChucVuController extends Controller
{
    public function DanhSachView()
    {
        $chucVus = DmChucVu::withCount('nhanViens')->get();
        return view('positions.index', compact('chucVus'));
    }

    public function TaoView()
    {
        return view('positions.add');
    }

    public function Tao(Request $request)
    {
        // Sanitize PhuCapChucVu: remove non-numeric characters (thousand separators)
        if ($request->has('PhuCapChucVu')) {
            $value = preg_replace('/[^0-9]/', '', $request->PhuCapChucVu);
            $request->merge(['PhuCapChucVu' => $value]);
        }

        $validated = $request->validate([
            'Ten' => 'required|string|max:255',
            'Loai' => 'required|in:0,1',
            'PhuCapChucVu' => 'nullable|numeric|min:0',
        ], [
            'Ten.required' => 'Tên chức vụ không được để trống.',
            'Loai.required' => 'Vui lòng chọn loại chức vụ.',
            'PhuCapChucVu.numeric' => 'Phụ cấp phải là số.',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Generate Ma: CV + (count + 1) padded to 3 digits
                $count = DmChucVu::count();
                $nextNumber = $count + 1;
                $ma = 'CV' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                // Ensure uniqueness (in case of deletions or manual edits in DB)
                while (DmChucVu::where('Ma', $ma)->exists()) {
                    $nextNumber++;
                    $ma = 'CV' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                }

                $validated['Ma'] = $ma;

                $chucVu = DmChucVu::create($validated);
                \App\Services\SystemLogService::log('Tạo mới', 'DmChucVu', $chucVu->id, "Thêm chức vụ mới: {$chucVu->Ten}");

                return redirect()->route('chuc-vu.danh-sach')
                    ->with('success', 'Thêm chức vụ thành công! Mã chức vụ mới: ' . $ma);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function InfoView($id)
    {
        $chucVu = DmChucVu::with(['nhanViens' => function($query) {
            $query->with(['ttCongViec.phongBan', 'nguoiDung']);
        }])->withCount('nhanViens')->findOrFail($id);
        return view('positions.info', compact('chucVu'));
    }

    public function SuaView($id)
    {
        $chucVu = DmChucVu::findOrFail($id);
        return view('positions.edit', compact('chucVu'));
    }

    public function CapNhat(Request $request, $id)
    {
        $chucVu = DmChucVu::findOrFail($id);

        // Sanitize PhuCapChucVu: remove non-numeric characters (thousand separators)
        if ($request->has('PhuCapChucVu')) {
            $value = preg_replace('/[^0-9]/', '', $request->PhuCapChucVu);
            $request->merge(['PhuCapChucVu' => $value]);
        }

        $validated = $request->validate([
            'Ten' => 'required|string|max:255',
            'Loai' => 'required|in:0,1',
            'PhuCapChucVu' => 'nullable|numeric|min:0',
        ], [
            'Ten.required' => 'Tên chức vụ không được để trống.',
            'Loai.required' => 'Vui lòng chọn loại chức vụ.',
            'PhuCapChucVu.numeric' => 'Phụ cấp phải là số.',
        ]);

        try {
            $oldData = $chucVu->toArray();
            $chucVu->update($validated);
            $newData = $chucVu->fresh()->toArray();
            \App\Services\SystemLogService::log('Cập nhật', 'DmChucVu', $chucVu->id, "Cập nhật chức vụ: {$chucVu->Ten}", $oldData, $newData);

            return redirect()->route('chuc-vu.danh-sach')
                ->with('success', 'Cập nhật chức vụ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function Xoa($id)
    {
        try {
            $chucVu = DmChucVu::findOrFail($id);

            // Kiểm tra xem chức vụ có nhân viên hay không trước khi xóa
            if ($chucVu->nhanViens()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa chức vụ "' . $chucVu->Ten . '" do đang có nhân viên.']);
            }

            $tenCV = $chucVu->Ten;
            $chucVu->delete();
            \App\Services\SystemLogService::log('Xóa', 'DmChucVu', $id, "Xóa chức vụ: {$tenCV}");

            return response()->json(['success' => true, 'message' => 'Xóa chức vụ thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn chức vụ cần xóa.'], 400);
        }

        try {
            $chucVus = DmChucVu::whereIn('id', $ids)->get();
            $inUseNames = [];

            foreach ($chucVus as $cv) {
                if ($cv->nhanViens()->count() > 0) {
                    $inUseNames[] = $cv->Ten;
                }
            }

            if (!empty($inUseNames)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa các chức vụ sau do đang có nhân viên: ' . implode(', ', $inUseNames)
                ]);
            }

            $tenChucVus = $chucVus->pluck('Ten')->implode(', ');
            DmChucVu::whereIn('id', $ids)->delete();

            \App\Services\SystemLogService::log('Xóa', 'DmChucVu', null, "Xóa nhiều chức vụ: {$tenChucVus}");

            return response()->json(['success' => true, 'message' => 'Xóa ' . count($ids) . ' chức vụ thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
