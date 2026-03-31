<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhuLucHopDong extends Model
{
    protected $table = 'phu_luc_hop_dongs';
    protected $fillable = ['HopDongId', 'ten_phu_luc', 'ngay_ky', 'TrangThai'];
    // public $timestamps = true; // Default is true, so I'll just remove the false override

    // Quan hệ với Hợp đồng chính
    public function hopDong()
    {
        return $this->belongsTo(HopDong::class, 'HopDongId');
    }

    public function dieuKhoans()
    {
        return $this->belongsToMany(DmPlHopDong::class, 'chi_tiet_phu_luc', 'PhuLucId', 'DieuKhoanId')
            ->withPivot('so_tien');
    }

    /**
     * Relationship: Chữ ký số
     */
    public function kySo()
    {
        return $this->morphOne(HopDongKySo::class, 'signable');
    }
}
