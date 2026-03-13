<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUnitScoping;

class DmPhongBan extends Model
{
    use HasUnitScoping;
    protected $table = 'dm_phong_bans';

    protected $fillable = [
        'DonViId',
        'Ma', // Sample: PB000
        'Ten',
    ];

    protected $casts = [
        'DonViId' => 'integer',
    ];

    /**
     * Relationship: Phòng ban thuộc một đơn vị
     */
    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'DonViId');
    }

    /**
     * Relationship: Một phòng ban có nhiều nhân viên
     */
    public function nhanViens()
    {
        return $this->hasManyThrough(
            NhanVien::class,
            TtNhanVienCongViec::class,
            'PhongBanId',
            'id',
            'id',
            'NhanVienId'
        );
    }

    /**
     * Relationship: Một phòng ban có nhiều thông tin nhân viên công việc
     */
    public function ttNhanVienCongViec()
    {
        return $this->hasMany(TtNhanVienCongViec::class, 'PhongBanId');
    }

    /**
     * Relationship: Một phòng ban có nhiều hợp đồng
     */
    public function hopDongs()
    {
        return $this->hasMany(HopDong::class, 'PhongBanId');
    }
}
