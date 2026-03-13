<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUnitScoping;

class DmToDoi extends Model
{
    use HasUnitScoping;
    protected $table = 'dm_to_dois';

    protected $fillable = ['PhongBanId', 'Ma', 'Ten', 'GhiChu'];

    public function phongBan()
    {
        return $this->belongsTo(DmPhongBan::class, 'PhongBanId');
    }

    public function ttNhanVienCongViecs()
    {
        return $this->hasMany(TtNhanVienCongViec::class, 'ToDoiId');
    }

    public function lanhDaoHienTai()
    {
        return $this->hasMany(ToDoiLanhDao::class, 'ToDoiId')->whereNull('NgayKetThuc');
    }

    public function getTruongToAttribute()
    {
        $lanhDao = $this->lanhDaoHienTai()
            ->where('VaiTro', 1) // 1 là Tổ trưởng
            ->with('nhanVien')
            ->first();

        return $lanhDao ? $lanhDao->nhanVien : null;
    }
}
