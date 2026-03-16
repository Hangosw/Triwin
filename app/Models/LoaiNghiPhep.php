<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiNghiPhep extends Model
{
    protected $table = 'loai_nghi_pheps';

    protected $fillable = [
        'Ten',
        'HuongLuong',
        'CoHanMuc', // 0 là không có giới hạn số ngày, 1 là có giới hạn số ngày
        'HanMucToiDa',
        'TrangThai',
    ];

    protected $casts = [
        'HuongLuong' => 'decimal:2',
        'CoHanMuc' => 'integer',
    ];

    /**
     * Relationship: Một loại nghỉ phép có nhiều đơn nghỉ phép
     */
    public function dangKyNghiPheps()
    {
        return $this->hasMany(DangKyNghiPhep::class, 'LoaiNghiPhepId');
    }

    /**
     * Check if leave type has limit
     */
    public function hasLimit()
    {
        return $this->CoHanMuc === 1;
    }

    /**
     * Check if leave type is paid
     */
    public function isPaid()
    {
        return $this->HuongLuong > 0;
    }

    /**
     * Get salary percentage
     */
    public function getSalaryPercentage()
    {
        return $this->HuongLuong;
    }
}
