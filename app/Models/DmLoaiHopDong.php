<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmLoaiHopDong extends Model
{
    protected $table = 'dm_loai_hop_dongs';

    protected $fillable = [
        'MaLoai',
        'TenLoai',
        'ThoiHanThang',
        'ThoiHanBaoTruoc',
        'CoDongBaoHiem', // 0 là không, 1 là có
        'TrangThai', // mo, khoa
    ];

    protected $casts = [
        'ThoiHanThang' => 'integer',
        'ThoiHanBaoTruoc' => 'integer',
        'CoDongBaoHiem' => 'integer',
        'TrangThai' => 'string',
    ];

    /**
     * Check if contract type has insurance
     */
    public function hasBaoHiem()
    {
        return $this->CoDongBaoHiem === 1;
    }
}
