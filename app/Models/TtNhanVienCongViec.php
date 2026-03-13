<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasUnitScoping;

class TtNhanVienCongViec extends Model
{
    use HasUnitScoping;
    protected $table = 'tt_nhan_vien_cong_viecs';

    protected $fillable = [
        'NhanVienId',        // Foreign key to nhan_viens
        'LoaiNhanVien',      // 0: công nhân, 1: văn phòng
        'DonViId',
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
        'LoaiNhanVien' => 'integer',
        'DonViId' => 'integer',
        'ChucVuId' => 'integer',
        'PhongBanId' => 'integer',
        'TrinhDoHocVan' => 'integer',
        'NgayTuyenDung' => 'date',
        'NgayVaoBienChe' => 'date',
    ];

    /**
     * =====================
     * Relationships
     * =====================
     */

    // Đơn vị công tác
    public function donVi()
    {
        return $this->belongsTo(DonVi::class, 'DonViId');
    }

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

    // Là công nhân?
    public function isCongNhan()
    {
        return $this->LoaiNhanVien === 0;
    }

    // Là nhân viên văn phòng?
    public function isVanPhong()
    {
        return $this->LoaiNhanVien === 1;
    }

    /**
     * Accessor: Text loại nhân viên
     */
    public function getLoaiNhanVienTextAttribute()
    {
        return $this->LoaiNhanVien === 1 ? 'Văn phòng' : 'Công nhân';
    }

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
