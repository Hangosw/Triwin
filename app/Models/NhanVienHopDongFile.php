<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVienHopDongFile extends Model
{
    protected $table = 'nhan_vien_hop_dong_files';

    protected $fillable = [
        'NhanVienId',
        'FileName',
        'FilePath',
        'FileSize',
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->FileSize;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
