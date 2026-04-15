<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhuLucHopDong extends Model
{
    protected $table = 'phu_luc_hop_dongs';
    protected $fillable = [
        'HopDongGocId', 
        'HopDongPLId', 
        'ten_phu_luc', 
        'ngay_ky', 
        'TrangThai'
    ];

    /**
     * Relationship: The original root contract
     */
    public function hopDongGoc()
    {
        return $this->belongsTo(HopDong::class, 'HopDongGocId');
    }

    /**
     * Relationship: The specific version/appendix contract
     */
    public function hopDongPL()
    {
        return $this->belongsTo(HopDong::class, 'HopDongPLId');
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
