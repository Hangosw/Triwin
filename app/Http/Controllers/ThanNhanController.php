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
            "Thêm mới người phụ thuộc: {$thanNhan->HoTen} cho nhân viên ID: {$thanNhan->NhanVienId}",
            null,
            $thanNhan->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Thêm người phụ thuộc thành công!'
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
            "Xóa người phụ thuộc: {$thanNhan->HoTen} (CCCD: {$thanNhan->CCCD})",
            $thanNhan->toArray(),
            null
        );

        $thanNhan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa người phụ thuộc thành công!'
        ]);
    }

    /**
     * Approve the relative entry.
     */
    public function approve(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['Super Admin', 'System Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ], 403);
        }

        $thanNhan = ThanNhan::findOrFail($id);
        $thanNhan->update([
            'TrangThai' => 1,
            'GhiChu' => $request->GhiChu
        ]);

        // Ghi Log: Duyệt
        SystemLogService::log(
            'Duyệt',
            'ThanNhan',
            $thanNhan->id,
            "Duyệt người phụ thuộc: {$thanNhan->HoTen}. Ghi chú: {$request->GhiChu}",
            ['TrangThai' => 0],
            ['TrangThai' => 1, 'GhiChu' => $request->GhiChu]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt người phụ thuộc thành công!'
        ]);
    }

    /**
     * Reject the relative entry.
     */
    public function reject(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['Super Admin', 'System Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ], 403);
        }

        $request->validate([
            'GhiChu' => 'required|string|max:500'
        ], [
            'GhiChu.required' => 'Vui lòng nhập lý do từ chối.'
        ]);

        $thanNhan = ThanNhan::findOrFail($id);
        $thanNhan->update([
            'TrangThai' => 2,
            'GhiChu' => $request->GhiChu
        ]);

        // Ghi Log: Từ chối
        SystemLogService::log(
            'Từ chối',
            'ThanNhan',
            $thanNhan->id,
            "Từ chối người phụ thuộc: {$thanNhan->HoTen}. Lý do: {$request->GhiChu}",
            ['TrangThai' => 0],
            ['TrangThai' => 2, 'GhiChu' => $request->GhiChu]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối người phụ thuộc thành công!'
        ]);
    }
}
