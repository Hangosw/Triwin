<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class NguoiDung extends Authenticatable
{
    use Notifiable, HasRoles;

    protected static function booted()
    {
        static::saved(function ($user) {
            $nhanVien = $user->nhanVien;
            if ($nhanVien) {
                $updates = [];
                if ($user->wasChanged('Ten') && $nhanVien->Ten !== $user->Ten) {
                    $updates['Ten'] = $user->Ten;
                }
                if ($user->wasChanged('Email') && $nhanVien->Email !== $user->Email) {
                    $updates['Email'] = $user->Email;
                }
                if ($user->wasChanged('SoDienThoai') && $nhanVien->SoDienThoai !== $user->SoDienThoai) {
                    $updates['SoDienThoai'] = $user->SoDienThoai;
                }

                if (!empty($updates)) {
                    $nhanVien->update($updates);
                }
            }
        });
    }

    protected $table = 'nguoi_dungs';

    protected $fillable = [
        'Ten',
        'TaiKhoan',
        'MatKhau',
        'SoDienThoai',
        'Email',
        'TrangThai', // 0 là không hoạt động, 1 là hoạt động
    ];

    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];

    protected $casts = [
        'TrangThai' => 'integer',
    ];

    // Override password field for authentication
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    /**
     * Scope: Active users only
     */
    public function scopeActive($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->TrangThai === 1;
    }

    /**
     * Relationship: Mỗi người dùng gắn với một nhân viên
     */


    public function nhanVien()
    {
        return $this->hasOne(NhanVien::class, 'NguoiDungId');
    }
}
