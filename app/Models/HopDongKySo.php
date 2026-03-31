<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HopDongKySo extends Model
{
    protected $table = 'hop_dong_ky_sos';

    protected $fillable = [
        'signable_id',
        'signable_type',
        'nhan_vien_id',
        'nguoi_dai_dien_id',
        'chu_ky_nhan_vien',
        'ngay_ky_nhan_vien',
        'chu_ky_dai_dien',
        'ngay_ky_dai_dien',
    ];

    protected $casts = [
        'ngay_ky_nhan_vien' => 'datetime',
        'ngay_ky_dai_dien' => 'datetime',
    ];

    /**
     * Get the parent signable model (HopDong or PhuLuc).
     */
    public function signable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship to the employee signing.
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    /**
     * Relationship to the representative signing.
     */
    public function nguoiDaiDien()
    {
        return $this->belongsTo(NhanVien::class, 'nguoi_dai_dien_id');
    }
}
