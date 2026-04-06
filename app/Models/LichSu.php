<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSu extends Model
{
    use HasFactory;

    protected $table = 'lich_sus';
    protected $primaryKey = 'Id';

    // The database table uses 'CreatedAt' and 'UpdatedAt' instead of snake_case defaults
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    protected $fillable = [
        'NhanVienId',
        'HanhDong',
        'DoiTuongLoai',
        'DoiTuongId',
        'MoTa',
        'DuLieuCu',
        'DuLieuMoi',
        'IpDiaChi',
        'ThietBi'
    ];

    /**
     * Set the current values for old/new data. 
     * Handles json conversion automatically.
     */
    protected $casts = [
        'DuLieuCu' => 'array',
        'DuLieuMoi' => 'array',
        'CreatedAt' => 'datetime',
        'UpdatedAt' => 'datetime',
    ];

    /**
     * Relationship with NguoiDung (User)
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NhanVienId', 'id');
    }
}
