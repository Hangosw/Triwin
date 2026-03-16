<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TangCa;

class DmTangCa extends Model
{
    // Khai báo tên bảng vì chúng ta dùng tiền tố dm_
    protected $table = 'dm_tang_cas';

    protected $fillable = [
        'MaLoai',    // Ví dụ: TC_NGAYTHUONG, TC_NGAYNGHI, TC_NGAYLE
        'TenLoai',   // Ví dụ: Tăng ca ngày thường
        'HeSo',      // Ví dụ: 1.5, 2.0, 3.0
        'GhiChu'
    ];

    /**
     * Một loại tăng ca có thể xuất hiện trong nhiều Phiếu tăng ca của nhân viên
     */
    public function tangCas(): HasMany
    {
        // Giả sử bạn có bảng phieu_tang_cas lưu chi tiết từng lần OT
        return $this->hasMany(TangCa::class, 'LoaiTangCaId', 'id');
    }
}
