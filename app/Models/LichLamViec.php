<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichLamViec extends Model
{
    protected $table = 'lich_lam_viecs';

    protected $fillable = [
        'NhanVienId',
        'CaId',
        'NgayLamViec',
        'IsLocked',
        'GhiChu'
    ];

    // Quan hệ với nhân viên
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    // Quan hệ với ca làm việc
    public function caLamViec()
    {
        return $this->belongsTo(DmCaLamViec::class, 'CaId');
    }
}
