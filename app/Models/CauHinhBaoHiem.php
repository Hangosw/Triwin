<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CauHinhBaoHiem extends Model
{
    protected $table = 'cau_hinh_bao_hiems';

    protected $fillable = [
        'TenLoai',
        'TiLeNhanVien',
        'TiLeCongTy',
        'NgayApDung',
        'GhiChu'
    ];

    /**
     * Lấy cấu hình bảo hiểm đang có hiệu lực tại thời điểm hiện tại
     */
    public static function getHieuLucHienTai()
    {
        return self::where('NgayApDung', '<=', now())
            ->orderBy('NgayApDung', 'desc')
            ->get();
    }
}
