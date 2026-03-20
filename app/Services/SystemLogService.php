<?php

namespace App\Services;

use App\Models\LichSu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemLogService
{
    /**
     * Ghi log hành động vào hệ thống.
     *
     * @param string $hanhDong Tên hành động (VD: Đăng nhập, Thêm mới, Cập nhật...)
     * @param string|null $doiTuongLoai Loại đối tượng (VD: NhanVien, HopDong...)
     * @param int|string|null $doiTuongId ID của đối tượng bị thay đổi
     * @param string|null $moTa Mô tả chi tiết hành động
     * @param mixed $duLieuCu Dữ liệu trước khi đổi (mảng hoặc object)
     * @param mixed $duLieuMoi Dữ liệu sau khi đổi (mảng hoặc object)
     * @return LichSu|null Trả về instance của log vừa tạo, hoặc null nếu lỗi
     */
    public static function log(
        string $hanhDong,
        $doiTuongLoai = null,
        $doiTuongId = null,
        string $moTa = null,
        $duLieuCu = null,
        $duLieuMoi = null
    ) {
        try {
            $user = Auth::user();
            $nhanVienId = null;

            // Lưu ID của người dùng (NguoiDungId) vào cột NhanVienId để thống nhất (bảo gồm cả Admin không có NhanVienId)
            $nhanVienId = $user ? $user->id : null;

            return LichSu::create([
                'NhanVienId' => $nhanVienId,
                'HanhDong' => $hanhDong,
                'DoiTuongLoai' => $doiTuongLoai,
                'DoiTuongId' => $doiTuongId,
                'MoTa' => $moTa,
                'DuLieuCu' => $duLieuCu,
                'DuLieuMoi' => $duLieuMoi,
                'IpDiaChi' => Request::ip(),
                'ThietBi' => substr(Request::userAgent() ?? '', 0, 255), // Giới hạn độ dài UserAgent
            ]);
        } catch (\Exception $e) {
            // Log lỗi hệ thống ra file thay vì làm gián đoạn luồng chính
            \Log::error('Lỗi lưu SystemLog: ' . $e->getMessage());
            return null;
        }
    }
}
