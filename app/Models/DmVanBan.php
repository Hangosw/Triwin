<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DmVanBan extends Model
{
    // Khai báo tên bảng vì Laravel mặc định sẽ tìm bảng 'dm_van_bans'
    protected $table = 'dm_van_bans';

    protected $fillable = [
        'ten_loai',
        'mo_ta'
    ];

    /**
     * Mối quan hệ 1-N: Một loại văn bản có thể có nhiều bản ghi Văn Thư
     * Ví dụ: Loại "Hợp đồng" có 100 cái văn bản bên bảng van_thus
     */
    public function vanThus(): HasMany
    {
        return $this->hasMany(VanThu::class, 'loai_van_ban_id', 'id');
    }
}
