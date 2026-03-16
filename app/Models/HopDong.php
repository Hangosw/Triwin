<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HopDong extends Model
{
    
    protected $table = 'hop_dongs';

    protected $fillable = [
        'NhanVienId',
        'NguoiKyId',

        'PhongBanId',
        'ChucVuId',
        'SoHopDong', // [Số thứ tự]/[Năm]/[Mã Loại Hợp Đồng]-[Mã Đơn Vị]
        'Loai', // chinh_thuc, khoan_viec, thu_viec
        'NgayBatDau',
        'NgayKetThuc', // after NgayBatDau
        'File',
        'TrangThai', // 0: hết hạn, 1: còn hiệu lực, 2: bị hủy/bị thanh lý
        // luong
        'LuongCoBan',
        'PhuCapChucVu',
        'PhuCapTrachNhiem',
        'PhuCapDocHai',
        'PhuCapThamNien',
        'PhuCapKhuVuc',
        'PhuCapAnTrua',
        'PhuCapXangXe',
        'PhuCapDienThoai',
        'PhuCapKhac',
        'TongLuong',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'NguoiKyId' => 'integer',

        'PhongBanId' => 'integer',
        'ChucVuId' => 'integer',
        'LuongThoaThuan' => 'decimal:2',
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'TrangThai' => 'integer',
        'TongLuong' => 'decimal:2',
    ];

    /**
     * Relationship: Hợp đồng của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Relationship: Người ký hợp đồng
     */
    public function nguoiKy()
    {
        return $this->belongsTo(NhanVien::class, 'NguoiKyId');
    }



    /**
     * Relationship: Hợp đồng thuộc phòng ban
     */
    public function phongBan()
    {
        return $this->belongsTo(DmPhongBan::class, 'PhongBanId');
    }

    /**
     * Relationship: Hợp đồng với chức vụ
     */
    public function chucVu()
    {
        return $this->belongsTo(DmChucVu::class, 'ChucVuId');
    }

    /**
     * Relationship: Loại hợp đồng
     */
    public function loaiHopDong()
    {
        return $this->belongsTo(DmLoaiHopDong::class, 'Loai', 'MaLoai');
    }

    /**
     * Check if contract is expired
     */
    public function isExpired()
    {
        return $this->TrangThai === 0;
    }

    /**
     * Check if contract is valid
     */
    public function isValid()
    {
        return $this->TrangThai === 1;
    }

    /**
     * Check if contract is cancelled
     */
    public function isCancelled()
    {
        return $this->TrangThai === 2;
    }

    /**
     * Check if contract is permanent
     */
    public function isChinhThuc()
    {
        return $this->Loai === 'chinh_thuc';
    }

    /**
     * Scope: Valid contracts only
     */
    public function scopeConHieuLuc($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Scope: Expired contracts
     */
    public function scopeHetHan($query)
    {
        return $query->where('TrangThai', 0);
    }
}
