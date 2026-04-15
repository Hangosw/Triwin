<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanNhan extends Model
{
    protected $table = 'nhan_vien_than_nhans';

    protected $fillable = [
        'NhanVienId',
        'HoTen',
        'NgaySinh',
        'QuanHe',
        'CCCD',
        'SoDienThoai',
        'LaGiamTruGiaCanh',
        'MaSoThue',
        'TepDinhKem',
        'TrangThai',
        'GhiChu'
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'NgaySinh' => 'date',
        'LaGiamTruGiaCanh' => 'boolean',
    ];

    /**
     * Người phụ thuộc thuộc về một nhân viên cụ thể
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }
}
