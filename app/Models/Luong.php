<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Luong extends Model
{
    
    protected $table = 'luongs';

    protected $fillable = [
        'NhanVienId',
        'LoaiLuong',
        'Luong',      // Đây nên là lương thực nhận cuối cùng (Net)
        'ThoiGian',
        'TrangThai',

        // --- CÁC CỘT BỔ SUNG ---

        // 1. Thu nhập (Earnings)
        'LuongCoBan',    // Tính từ Hệ số (bảng bac_luongs) * Lương cơ sở
        'LuongTangCa',   // Tổng tiền từ bảng tang_cas đã duyệt
        'PhuCap',        // Tổng các loại phụ cấp (ăn trưa, điện thoại...)
        'KhenThuong',    // Thưởng đột xuất

        // 2. Khấu trừ (Deductions)
        'KhauTruBaoHiem', // Tổng tiền 10.5% (BHXH, BHYT, BHTN) nhân viên đóng
        'ThueTNCN',       // Thuế thu nhập cá nhân sau khi trừ gia cảnh
        'KyLuat',         // Tiền phạt hoặc trừ lương
        'TamUng',         // Số tiền nhân viên đã ứng trong tháng

        // 3. Thông tin đối soát
        'SoNgayCong',     // Số công thực tế (đặc biệt quan trọng cho LoaiLuong = 1)
        'SoNguoiPhuThuoc', // Lưu số người tại thời điểm chốt lương để giải trình thuế
        'GhiChu',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'LoaiLuong' => 'integer',
        'Luong' => 'decimal:2',
        'ThoiGian' => 'date',
        'TrangThai' => 'integer',
    ];

    /**
     * Relationship: Lương của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Check if salary is paid
     */
    public function isPaid()
    {
        return $this->TrangThai === 1;
    }

    /**
     * Check if salary is unpaid
     */
    public function isUnpaid()
    {
        return $this->TrangThai === 0;
    }

    /**
     * Check if office worker salary
     */
    public function isVanPhong()
    {
        return $this->LoaiLuong === 0;
    }

    /**
     * Check if worker salary
     */
    public function isCongNhan()
    {
        return $this->LoaiLuong === 1;
    }

    /**
     * Check if contractor salary
     */
    public function isCongTacVien()
    {
        return $this->LoaiLuong === 2;
    }

    /**
     * Scope: Unpaid salaries
     */
    public function scopeChuaTra($query)
    {
        return $query->where('TrangThai', 0);
    }

    /**
     * Scope: Paid salaries
     */
    public function scopeDaTra($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Scope: Salaries for specific month/year
     */
    public function scopeThang($query, $month, $year)
    {
        return $query->whereYear('ThoiGian', $year)
            ->whereMonth('ThoiGian', $month);
    }
}
