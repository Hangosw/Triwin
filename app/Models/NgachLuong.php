<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NgachLuong extends Model
{
    protected $table = 'ngach_luongs';
    
    protected $fillable = [
        'Ma', // sample: 01.003
        'Ten', // chuyên viên, nhân viên
        'Nhom', // A3, A2, A1
        'TrangThai', // 1: Active, 0: Locked
    ];

    /**
     * Relationship: Một ngạch lương có nhiều bậc lương
     */
    public function bacLuongs()
    {
        return $this->hasMany(BacLuong::class, 'NgachLuongId');
    }

    /**
     * Relationship: Một ngạch lương có nhiều diễn biến lương
     */
    public function dienBienLuongs()
    {
        return $this->hasMany(DienBienLuong::class, 'NgachLuongId');
    }
}
