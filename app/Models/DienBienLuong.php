<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUnitScoping;

class DienBienLuong extends Model
{
    use HasUnitScoping;
    protected $table = 'dien_bien_luongs';

    protected $fillable = [
        'NhanVienId',
        'HopDongId',
        'NgachLuongId',
        'BacLuongId',
        'PhuCapVuotKhung', // %
        'NgayHuong',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'NgachLuongId' => 'integer',
        'BacLuongId' => 'integer',
        'PhuCapVuotKhung' => 'decimal:2',
        'NgayHuong' => 'date',
    ];

    /**
     * Relationship: Diễn biến lương của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Relationship: Diễn biến lương thuộc ngạch lương
     */
    public function ngachLuong()
    {
        return $this->belongsTo(NgachLuong::class, 'NgachLuongId');
    }

    /**
     * Relationship: Diễn biến lương thuộc bậc lương
     */
    public function bacLuong()
    {
        return $this->belongsTo(BacLuong::class, 'BacLuongId');
    }

    /**
     * Get current salary progression for employee
     */
    public static function getCurrentForEmployee($nhanVienId)
    {
        return self::where('NhanVienId', $nhanVienId)
            ->where('NgayHuong', '<=', now())
            ->orderBy('NgayHuong', 'desc')
            ->first();
    }

    /**
     * Calculate total salary including allowances
     */
    public function calculateTotalSalary($mucLuongCoSo, $phuCapChucVu = 0)
    {
        $baseSalary = $this->bacLuong->HeSo * $mucLuongCoSo;
        $vuotKhung = $baseSalary * ($this->PhuCapVuotKhung / 100);
        return $baseSalary + $vuotKhung + $phuCapChucVu;
    }
}
