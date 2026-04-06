<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\ThanNhan;
use App\Services\SystemLogService;
use Illuminate\Http\Request;

class ThanNhanController extends Controller
{
    /**
     * Store a newly created dependent in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'NhanVienId' => 'required|exists:nhan_viens,id',
            'HoTen' => 'required|string|max:255',
            'NgaySinh' => 'nullable|string', // Changed to string to handle dd/mm/yyyy
            'QuanHe' => 'required|in:bo_de,me_de,vo_chong,con_ruot,con_nuoi,khac',
            'CCCD' => 'nullable|string|max:20',
            'SoDienThoai' => 'nullable|string|max:20',
            'MaSoThue' => 'nullable|string|max:20',
            'TepDinhKem' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $data['LaGiamTruGiaCanh'] = $request->has('LaGiamTruGiaCanh') ? 1 : 0;
        
        // Approval Status Logic
        $user = auth()->user();
        $isAdmin = $user && $user->hasAnyRole(['Super Admin', 'System Admin']);
        $data['TrangThai'] = $isAdmin ? 1 : 0;
        
        // Convert date format from dd/mm/yyyy to yyyy-mm-dd
        if ($request->filled('NgaySinh')) {
            $dateParts = explode('/', $request->NgaySinh);
            if (count($dateParts) === 3) {
                $data['NgaySinh'] = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            }
        }

        if ($request->hasFile('TepDinhKem')) {
            $file = $request->file('TepDinhKem');
            $filename = 'thannhan_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/than_nhan'), $filename);
            $data['TepDinhKem'] = 'uploads/than_nhan/' . $filename;
        }

        $thanNhan = ThanNhan::create($data);

        // Ghi Log: Thêm mới
        SystemLogService::log(
            'Thêm mới',
            'ThanNhan',
            $thanNhan->id,
            "Thêm mới thân nhân: {$thanNhan->HoTen} cho nhân viên ID: {$thanNhan->NhanVienId}",
            null,
            $thanNhan->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Thêm thân nhân thành công!'
        ]);
    }

    /**
     * Remove the specified dependent from storage.
     */
    public function destroy($id)
    {
        $thanNhan = ThanNhan::findOrFail($id);

        // Delete file if exists
        if ($thanNhan->TepDinhKem && file_exists(public_path($thanNhan->TepDinhKem))) {
            unlink(public_path($thanNhan->TepDinhKem));
        }

        // Ghi Log trước khi xóa: Xóa dữ liệu
        SystemLogService::log(
            'Xóa',
            'ThanNhan',
            $thanNhan->id,
            "Xóa thân nhân: {$thanNhan->HoTen} (CCCD: {$thanNhan->CCCD})",
            $thanNhan->toArray(),
            null
        );

        $thanNhan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thân nhân thành công!'
        ]);
    }

    /**
     * Approve the relative entry.
     */
    public function approve($id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['Super Admin', 'System Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ], 403);
        }

        $thanNhan = ThanNhan::findOrFail($id);
        $thanNhan->update(['TrangThai' => 1]);

        // Ghi Log: Duyệt
        SystemLogService::log(
            'Duyệt',
            'ThanNhan',
            $thanNhan->id,
            "Duyệt thân nhân: {$thanNhan->HoTen}",
            ['TrangThai' => 0],
            ['TrangThai' => 1]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt thân nhân thành công!'
        ]);
    }
}
