<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class TtNhanVienCongViec extends Model
{
    protected $table = 'tt_nhan_vien_cong_viecs';

    protected $fillable = [
        'NhanVienId',        // Foreign key to nhan_viens
        'ChucVuId',
        'PhongBanId',
        'NgayTuyenDung',
        'NgayVaoBienChe',
        'TrinhDoHocVan',
        'ChuyenNganh',
        'TrinhDoChuyenMon',
        'NgoaiNgu',
    ];

    protected $casts = [
        'ChucVuId' => 'integer',
        'PhongBanId' => 'integer',
        'NgayTuyenDung' => 'date',
        'NgayVaoBienChe' => 'date',
    ];

    /**
     * =====================
     * Relationships
     * =====================
     */

    // Chức vụ
    public function chucVu()
    {
        return $this->belongsTo(DmChucVu::class, 'ChucVuId');
    }

    // Phòng ban
    public function phongBan()
    {
        return $this->belongsTo(DmPhongBan::class, 'PhongBanId');
    }

    // Nhân viên
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * =====================
     * Helpers
     * =====================
     */

    /**
     * Tính số năm công tác
     */
    public function getSoNamCongTacAttribute()
    {
        if (!$this->NgayTuyenDung) {
            return 0;
        }

        return Carbon::parse($this->NgayTuyenDung)
            ->diffInYears(Carbon::now());
    }
}
