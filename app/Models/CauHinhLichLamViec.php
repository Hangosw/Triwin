<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHinhLichLamViec extends Model
{
    protected $table = 'cau_hinh_lich_lam_viecs';

    protected $fillable = [
        'Thu',
        'CoLamViec',
        'HeSoNgayCong',
        'MoTa'
    ];

    protected $casts = [
        'CoLamViec' => 'float',
        'HeSoNgayCong' => 'float'
    ];
}
