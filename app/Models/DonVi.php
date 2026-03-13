<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUnitScoping;

class DonVi extends Model
{
    use HasUnitScoping;
    protected $table = 'don_vis';

    protected $fillable = [
        'Ma', // Sample: DV000
        'Ten',
        'DiaChi',
    ];

    /**
     * Relationship: Một đơn vị có nhiều phòng ban
     */
    public function phongBans()
    {
        return $this->hasMany(DmPhongBan::class, 'DonViId');
    }

    /**
     * Relationship: Một đơn vị có nhiều hợp đồng
     */
    public function hopDongs()
    {
        return $this->hasMany(HopDong::class, 'DonViId');
    }

    /**
     * Relationship: Một đơn vị có nhiều quá trình công tác
     */
    public function quaTrinhCongTacs()
    {
        return $this->hasMany(QuaTrinhCongTac::class, 'DonViId');
    }
}
