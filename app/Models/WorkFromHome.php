<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkFromHome extends Model
{
    protected $table = 'work_from_homes';

    protected $fillable = [
        'NhanVienId',
        'NguoiDuyetId',
        'NgayBatDau',
        'NgayKetThuc',
        'Ngay',
        'LyDo',
        'GhiChu',
        'TrangThai',
        'NgayDuyet'
    ];

    protected $casts = [
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'NgayDuyet' => 'date',
        'Ngay' => 'decimal:2'
    ];

    /**
     * Get the employee who requested WFH.
     */
    public function nhanVien(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Get the manager who approved/rejected the request.
     */
    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'NguoiDuyetId');
    }

    /**
     * Scope for pending requests.
     */
    public function scopeDangCho($query)
    {
        return $query->where('TrangThai', 'dang_cho');
    }

    /**
     * Scope for approved requests.
     */
    public function scopeDaDuyet($query)
    {
        return $query->where('TrangThai', 'da_duyet');
    }
}
