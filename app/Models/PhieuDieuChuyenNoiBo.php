<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PhieuDieuChuyenNoiBo extends Model
{
    
    protected $table = 'phieu_dieu_chuyen_noi_bo';

    protected $fillable = [
        'NhanVienId',
        'NguoiYeuCauId',

        'PhongBanMoiId',
        'ChucVuMoiId',
        'NgayDuKien',
        'LyDo',
        'CoThayDoiLuong',
        'GhiChuLanhDao',
        'TrangThai',
        'DaTaoHopDong',
    ];

    protected $casts = [
        'NgayDuKien' => 'date',
    ];

    // Nhân viên được điều chuyển
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    // Người tạo yêu cầu
    public function nguoiYeuCau(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'NguoiYeuCauId');
    }



    // Phòng ban mới
    public function phongBanMoi(): BelongsTo
    {
        return $this->belongsTo(DmPhongBan::class, 'PhongBanMoiId');
    }

    // Chức vụ mới
    public function chucVuMoi(): BelongsTo
    {
        return $this->belongsTo(DmChucVu::class, 'ChucVuMoiId');
    }
}
