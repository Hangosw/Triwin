<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuanLyPhepNam extends Model
{
    protected $table = 'quan_ly_phep_nams';

    protected $fillable = [
        'NhanVienId',
        'Nam',
        'TongPhepDuocNghi', // Phép cơ bản + Ngày cộng thêm dựa trên thâm niên + phép năm ngoái chuyển sang
        'DaNghi',
        'ConLai',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'Nam' => 'integer',
        'TongPhepDuocNghi' => 'decimal:1',
        'DaNghi' => 'decimal:1',
        'ConLai' => 'decimal:1',
    ];

    /**
     * Relationship: Quản lý phép của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Update leave balance after taking leave
     */
    public function deductLeave($days)
    {
        $this->DaNghi += $days;
        $this->ConLai = (float) $this->TongPhepDuocNghi - (float) $this->DaNghi;
        $this->save();
    }

    /**
     * Check if employee has enough leave days
     */
    public function hasEnoughLeave($days)
    {
        return $this->ConLai >= $days;
    }

    /**
     * Khởi tạo bảng phép năm cho nhân viên dựa trên thâm niên
     */
    public static function khoiTaoPhepNam($nhanVienId, $nam)
    {
        $nhanVien = NhanVien::find($nhanVienId);
        if (!$nhanVien || !$nhanVien->ttCongViec) {
            return null;
        }

        $config = CauHinhPhepNam::getCurrentConfig();
        $soNgayCoBan = (float) \App\Models\SystemConfig::getValue('annual_leave_days', 12);
        
        if (!$config) {
            // Nếu chưa có cấu hình bổ sung, tạo mặc định dựa trên SystemConfig
            $config = CauHinhPhepNam::create([
                'SoNgayCoBan' => $soNgayCoBan,
                'NamThamNien' => 5,
                'NgayCongThem' => 1
            ]);
        } else {
            // Đảm bảo SoNgayCoBan trong config khớp với SystemConfig nếu muốn đồng bộ
            $config->SoNgayCoBan = $soNgayCoBan;
        }

        $ngayTuyenDung = $nhanVien->ttCongViec->NgayTuyenDung;
        $soNamCongTac = 0;
        if ($ngayTuyenDung) {
            // Đảm bảo là instance của Carbon, nếu là string thì parse
            $dtTuyenDung = $ngayTuyenDung instanceof \Carbon\Carbon ? $ngayTuyenDung : Carbon::parse((string) $ngayTuyenDung);
            $soNamCongTac = $dtTuyenDung->diffInYears(Carbon::now());
        }

        $tongPhep = (float) $config->calculateTotalLeave($soNamCongTac);

        return self::updateOrCreate(
            ['NhanVienId' => $nhanVienId, 'Nam' => $nam],
            [
                'TongPhepDuocNghi' => $tongPhep,
                'ConLai' => $tongPhep - (float) (self::where(['NhanVienId' => $nhanVienId, 'Nam' => $nam])->first()->DaNghi ?? 0)
            ]
        );
    }

    /**
     * Get current year leave balance for employee
     */
    public static function getCurrentForEmployee($nhanVienId)
    {
        $currentYear = now()->year;
        return self::where('NhanVienId', $nhanVienId)
            ->where('Nam', $currentYear)
            ->first();
    }

    /**
     * Scope: Current year leave balances
     */
    public function scopeNamHienTai($query)
    {
        return $query->where('Nam', now()->year);
    }
}
