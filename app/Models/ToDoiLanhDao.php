<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoiLanhDao extends Model
{
    protected $table = 'to_doi_lanh_dao';

    protected $fillable = [
        'ToDoiId',
        'NhanVienId',
        'VaiTro',
        'NgayBatDau',
        'NgayKetThuc'
    ];

    protected $casts = [
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'VaiTro' => 'integer',
    ];

    /**
     * Thuộc về tổ đội nào
     */
    public function toDoi()
    {
        return $this->belongsTo(DmToDoi::class, 'ToDoiId');
    }

    /**
     * Nhân viên giữ vai trò lãnh đạo
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Scope lấy những người đang đương nhiệm
     */
    public function scopeActive($query)
    {
        return $query->whereNull('NgayKetThuc');
    }
}
