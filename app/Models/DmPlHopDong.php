<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmPlHopDong extends Model
{
    protected $table = 'dm_pl_hop_dongs';

    // Thêm 'is_bhxh' vào fillable để có thể lưu dữ liệu
    protected $fillable = ['keyvalue', 'noi_dung', 'TrangThai', 'is_bhxh'];

    public $timestamps = false;

    const STATUS_MO = 'mo';
    const STATUS_KHOA = 'khoa';

    // Cast is_bhxh về kiểu boolean để dùng true/false cho tiện
    protected $casts = [
        'is_bhxh' => 'boolean',
    ];

    /**
     * Quan hệ nhiều-nhiều với Phụ lục
     */
    public function phuLucs()
    {
        return $this->belongsToMany(PhuLucHopDong::class, 'chi_tiet_phu_luc', 'DieuKhoanId', 'PhuLucId')
            ->withPivot('so_tien'); // Đừng quên lấy thêm số tiền từ bảng trung gian
    }

    /**
     * Scope lấy các điều khoản đang "mở"
     */
    public function scopeActive($query)
    {
        return $query->where('TrangThai', self::STATUS_MO);
    }

    /**
     * Scope lọc nhóm CÓ tính BHXH
     */
    public function scopeHasBhxh($query)
    {
        return $query->where('is_bhxh', true);
    }

    /**
     * Scope lọc nhóm KHÔNG tính BHXH
     */
    public function scopeNoBhxh($query)
    {
        return $query->where('is_bhxh', false);
    }
}