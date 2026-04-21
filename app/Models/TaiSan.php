<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiSan extends Model
{
    use HasFactory;

    protected $table = 'tai_sans';

    protected $fillable = [
        'ten_tai_san',
        'hinh_anh',
        'ngay_nhap_kho',
        'nhan_vien_id',
        'ngay_muon',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_nhap_kho' => 'date',
        'ngay_muon' => 'date',
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }
}
