<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class NhanVien extends Model
{
    protected $table = 'nhan_viens';

    protected static function booted()
    {
        static::saved(function ($nhanVien) {
            $user = $nhanVien->nguoiDung;
            if ($user) {
                $updates = [];
                if ($nhanVien->wasChanged('Ten') && $user->Ten !== $nhanVien->Ten) {
                    $updates['Ten'] = $nhanVien->Ten;
                }
                if ($nhanVien->wasChanged('Email') && $user->Email !== $nhanVien->Email) {
                    $updates['Email'] = $nhanVien->Email;
                }
                if ($nhanVien->wasChanged('SoDienThoai') && $user->SoDienThoai !== $nhanVien->SoDienThoai) {
                    $updates['SoDienThoai'] = $nhanVien->SoDienThoai;
                }

                if (!empty($updates)) {
                    $user->update($updates);
                }
            }
        });
    }

    protected $fillable = [
        'Ma',  // Mã nhân viên: NV_YY_XXXXX
        'Ten',  //
        'NguoiDungId',  //
        'Email',
        'SoDienThoai',

        'SoCCCD', //
        'NoiCap',
        'NgayCap',
        'NgaySinh',
        'GioiTinh', // 1 là nam, 0 là nữ

        'DiaChi',
        'QueQuan',
        'AnhDaiDien',
        'DanToc',
        'TonGiao',
        'QuocTich',
        'TinhTrangHonNhan',
        'TenNganHang',
        'SoTaiKhoan',
        'ChiNhanhNganHang',
        'BHXH',
        'NoiCapBHXH',
        'BHYT',
        'NoiCapBHYT',
        'Note',
    ];

    protected $casts = [
        'NgaySinh' => 'date',
        'GioiTinh' => 'integer',
    ];

    /**
     * Relationship: Nhân viên có thông tin công việc
     */
    public function ttCongViec()
    {
        return $this->hasOne(TtNhanVienCongViec::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có một tài khoản người dùng
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId');
    }

    /**
     * Accessor: Get ChucVu through ttCongViec
     */
    public function getChucVuAttribute()
    {
        return $this->ttCongViec?->chucVu;
    }

    /**
     * Accessor: Get PhongBan through ttCongViec
     */
    public function getPhongBanAttribute()
    {
        return $this->ttCongViec?->phongBan;
    }



    /**
     * Accessor: Get Nhom (LoaiNhanVien) through ttCongViec
     */
    public function getNhomAttribute()
    {
        if (!$this->ttCongViec)
            return null;
        return $this->ttCongViec->LoaiNhanVien === 1 ? 'van_phong' : 'cong_nhan';
    }

    /**
     * Relationship: Nhân viên có nhiều quá trình công tác
     */
    public function quaTrinhCongTacs()
    {
        return $this->hasMany(QuaTrinhCongTac::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có nhiều hợp đồng
     */
    public function hopDongs()
    {
        return $this->hasMany(HopDong::class, 'NhanVienId');
    }

    /**
     * Relationship: Hợp đồng do nhân viên ký (với tư cách người ký)
     */
    public function hopDongsNguoiKy()
    {
        return $this->hasMany(HopDong::class, 'NguoiKyId');
    }

    /**
     * Relationship: Nhân viên có nhiều diễn biến lương
     */
    public function dienBienLuongs()
    {
        return $this->hasMany(DienBienLuong::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có nhiều bảng lương
     */
    public function luongs()
    {
        return $this->hasMany(Luong::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có nhiều bản ghi chấm công
     */
    public function chamCongs()
    {
        return $this->hasMany(ChamCong::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có nhiều đơn tăng ca
     */
    public function tangCas()
    {
        return $this->hasMany(TangCa::class, 'NhanVienId');
    }

    /**
     * Relationship: Đơn tăng ca do nhân viên duyệt
     */
    public function tangCasDuyet()
    {
        return $this->hasMany(TangCa::class, 'NguoiDuyetId');
    }

    /**
     * Relationship: Nhân viên có quản lý phép năm
     */
    public function quanLyPhepNams()
    {
        return $this->hasMany(QuanLyPhepNam::class, 'NhanVienId');
    }

    /**
     * Relationship: Nhân viên có nhiều đơn nghỉ phép
     */
    public function dangKyNghiPheps()
    {
        return $this->hasMany(DangKyNghiPhep::class, 'NhanVienId');
    }

    /**
     * Relationship: Đơn nghỉ phép do nhân viên duyệt
     */
    public function nghiPhepsDuyet()
    {
        return $this->hasMany(DangKyNghiPhep::class, 'NguoiDuyetId');
    }

    /**
     * Check if employee is male
     */
    public function isNam()
    {
        return $this->GioiTinh === 1;
    }

    /**
     * Check if employee is office worker
     */
    public function isVanPhong()
    {
        return $this->Nhom === 'van_phong';
    }

    /**
     * Check if employee is worker
     */
    public function isCongNhan()
    {
        return $this->Nhom === 'cong_nhan';
    }

    /**
     * Calculate years of service
     */
    public function getYearsOfService()
    {
        if (!$this->NgayTuyenDung) {
            return 0;
        }
        return Carbon::parse($this->NgayTuyenDung)->diffInYears(Carbon::now());
    }

    /**
     * Scope: Office workers only
     */
    public function scopeVanPhong($query)
    {
        return $query->where('Nhom', 'van_phong');
    }

    /**
     * Scope: Workers only
     */
    public function scopeCongNhan($query)
    {
        return $query->where('Nhom', 'cong_nhan');
    }

    /**
     * Relationship: Nhân viên có nhiều thân nhân
     */
    public function thanNhans()
    {
        return $this->hasMany(ThanNhan::class, 'NhanVienId');
    }
}
