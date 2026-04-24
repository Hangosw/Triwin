<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NhanVien extends Model
{
    protected $table = 'nhan_viens';

    protected $guarded = ['id'];

    protected $casts = [
        'NgaySinh' => 'date',
        'GioiTinh' => 'integer',
        'NguoiDungId' => 'integer',
        'anh_cccd' => 'array',
        'anh_bhxh' => 'array',
    ];

    /**
     * =====================
     * Relationships
     * =====================
     */

    // Liên kết với tài khoản người dùng
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId');
    }

    // Thông tin công việc (Phòng ban, chức vụ, ngày vào làm...)
    public function ttCongViec()
    {
        return $this->hasOne(TtNhanVienCongViec::class, 'NhanVienId');
    }

    // Danh sách hợp đồng
    public function hopDongs()
    {
        return $this->hasMany(HopDong::class, 'NhanVienId');
    }

    // Danh sách bảng lương
    public function luongs()
    {
        return $this->hasMany(Luong::class, 'NhanVienId');
    }

    // Dữ liệu chấm công
    public function chamCongs()
    {
        return $this->hasMany(ChamCong::class, 'NhanVienId');
    }

    // Đăng ký nghỉ phép
    public function dangKyNghiPheps()
    {
        return $this->hasMany(DangKyNghiPhep::class, 'NhanVienId');
    }

    // Người thân / Người phụ thuộc
    public function thanNhans()
    {
        return $this->hasMany(ThanNhan::class, 'NhanVienId');
    }

    // Đăng ký Work From Home (Làm việc từ xa)
    public function workFromHomes()
    {
        return $this->hasMany(WorkFromHome::class, 'NhanVienId');
    }

    // Các khoản tạm ứng
    public function tamUngs()
    {
        return $this->hasMany(TamUng::class, 'NhanVienId');
    }

    // Quá trình công tác
    public function quaTrinhCongTacs()
    {
        return $this->hasMany(QuaTrinhCongTac, 'NhanVienId');
    }

    // Danh sách tài sản đang mượn
    public function taiSans()
    {
        return $this->hasMany(TaiSan::class, 'nhan_vien_id');
    }

    /**
     * =====================
     * Accessors & Helpers
     * =====================
     */

    // Lấy chức vụ hiện tại qua ttCongViec
    public function getChucVuAttribute()
    {
        return $this->ttCongViec?->chucVu;
    }

    // Lấy phòng ban hiện tại qua ttCongViec
    public function getPhongBanAttribute()
    {
        return $this->ttCongViec?->phongBan;
    }

    /**
     * Tính số năm công tác
     */
    public function getYearsOfService()
    {
        $ngayVaoLam = $this->ttCongViec?->NgayTuyenDung;
        if (!$ngayVaoLam) {
            return 0;
        }

        return Carbon::parse($ngayVaoLam)->diffInYears(Carbon::now());
    }

    /**
     * =====================
     * Scopes
     * =====================
     */
    // Nhân viên đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('TrangThai', 'dang_lam');
    }

    // Nhân viên khối văn phòng
    public function scopeVanPhong($query)
    {
        return $query->where('Nhom', 'van_phong');
    }

    // Nhân viên khối sản xuất (công nhân)
    public function scopeCongNhan($query)
    {
        return $query->where('Nhom', 'cong_nhan');
    }
    /**
     * Lấy hợp đồng gốc (Hợp đồng không phải là phụ lục)
     */
    public function hopDongGoc()
    {
        return $this->hasOne(HopDong::class, 'NhanVienId')
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('phu_luc_hop_dongs')
                    ->whereColumn('phu_luc_hop_dongs.HopDongPLId', 'hop_dongs.id');
            })
            ->orderBy('NgayBatDau', 'asc');
    }

    // Quản lý phép năm
    public function quanLyPhepNams()
    {
        return $this->hasMany(QuanLyPhepNam::class, 'NhanVienId');
    }
}
