<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_configs';

    const CREATED_AT = null;

    protected $fillable = [
        'key',
        'value',
        'group',
        'description'
    ];

    /**
     * Hàm Helper tĩnh (Static) để lấy giá trị cấu hình theo Key
     * Có hỗ trợ giá trị mặc định (default) nếu không tìm thấy key trong Database
     */
    public static function getValue($key, $default = null)
    {
        $config = self::where('key', $key)->first();

        return $config ? $config->value : $default;
    }
}
