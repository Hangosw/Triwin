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
        'KhaDung',
        'PhepUngToiDa',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'Nam' => 'integer',
        'TongPhepDuocNghi' => 'decimal:1',
        'DaNghi' => 'decimal:1',
        'ConLai' => 'decimal:1',
        'KhaDung' => 'decimal:1',
        'PhepUngToiDa' => 'decimal:1',
    ];

    protected $appends = ['PhepKhaDung'];

    /**
     * Accessor: Tính số phép khả dụng hiện tại (cộng dồn theo tháng)
     * Logic: (Tổng phép / 12) * Số tháng làm việc trong năm - Đã nghỉ
     */
    public function getPhepKhaDungAttribute()
    {
        return (float) $this->KhaDung;
    }

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
        $this->DaNghi = (float)($this->DaNghi ?? 0) + (float)$days;
        $this->ConLai = (float) $this->TongPhepDuocNghi - (float) $this->DaNghi;
        $this->KhaDung = (float)($this->KhaDung ?? 0) - (float)$days;
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
        $nhanVien = NhanVien::with(['ttCongViec', 'hopDongs'])->find($nhanVienId);
        if (!$nhanVien || !$nhanVien->ttCongViec) {
            return null;
        }

        // Ưu tiên lấy từ hợp đồng gốc
        $rootContract = $nhanVien->hopDongGoc;
        $ngayTuyenDung = $nhanVien->ttCongViec->NgayTuyenDung;
        $joinDate = $ngayTuyenDung ? ($ngayTuyenDung instanceof Carbon ? $ngayTuyenDung : Carbon::parse($ngayTuyenDung)) : null;
        
        $tongPhep = 12.0; // Mặc định
        $fullYearPhep = 12.0;

        if ($rootContract) {
            $fullYearPhep = (float) ($rootContract->NgayPhepNam ?? 12);
            $contractStartDate = Carbon::parse($rootContract->NgayBatDau);
            
            // Logic mới: Tỉ lệ theo số tháng còn lại nếu kí trong năm nay
            if ($nam == $contractStartDate->year) {
                // Số tháng tính từ tháng kí đến tháng 12
                $remainingMonths = 12 - $contractStartDate->month + 1;
                $tongPhep = round(($fullYearPhep / 12) * $remainingMonths, 1);
            } else {
                // Kí từ năm trước thì năm nay hưởng trọn vẹn
                $tongPhep = $fullYearPhep;
            }
        } else {
            // Logic cũ nếu không có hợp đồng
            $config = CauHinhPhepNam::getCurrentConfig();
            $soNgayCoBan = (float) \App\Models\SystemConfig::getValue('annual_leave_days', 12);
            
            if (!$config) {
                $config = CauHinhPhepNam::create([
                    'SoNgayCoBan' => $soNgayCoBan,
                    'NamThamNien' => 5,
                    'NgayCongThem' => 1
                ]);
            }

            $soNamCongTac = 0;
            if ($joinDate) {
                $soNamCongTac = $joinDate->diffInYears(Carbon::now());
            }
            $tongPhep = (float) $config->calculateTotalLeave($soNamCongTac);
            $fullYearPhep = $tongPhep;
        }

        // Tính số phép tích lũy (KhaDung) theo tháng
        $now = Carbon::now();
        $startMonth = 1;
        
        if ($rootContract) {
            $contractStartDate = Carbon::parse($rootContract->NgayBatDau);
            if ($contractStartDate->year == $nam) {
                $startMonth = $contractStartDate->month;
            }
        } elseif ($joinDate && $joinDate->year == $nam) {
            $startMonth = $joinDate->month;
        }

        // Số tháng đã làm việc tính đến hiện tại trong năm đang xét
        $monthsInYear = 0;
        if ($now->year > $nam) {
            $monthsInYear = 12; // Năm cũ đã qua
        } elseif ($now->year < $nam) {
            $monthsInYear = 0; // Năm tương lai chưa tới
        } else {
            // Năm hiện tại: tính từ startMonth đến tháng hiện tại
            $monthsInYear = max(0, $now->month - $startMonth + 1);
        }
        $monthsInYear = min(12, $monthsInYear);
        
        // Phép tích lũy = (Quota cả năm / 12) * Số tháng làm việc
        $accrued = ($fullYearPhep / 12) * $monthsInYear;

        // Lấy số ngày đã nghỉ (nếu đã có bản ghi)
        $existing = self::where(['NhanVienId' => $nhanVienId, 'Nam' => $nam])->first();
        $daNghi = (float) ($existing?->DaNghi ?? 0);

        // Tính PhepUngToiDa: Mặc định là số ngày phép năm tối đa trừ đi số ngày đã nghỉ thực tế
        // Hoặc có thể giới hạn bởi một cấu hình khác
        $phepUngToiDa = round($tongPhep - $accrued, 1);

        return self::updateOrCreate(
            ['NhanVienId' => $nhanVienId, 'Nam' => $nam],
            [
                'TongPhepDuocNghi' => $tongPhep,
                'KhaDung' => round($accrued - $daNghi, 1),
                'ConLai' => round($tongPhep - $daNghi, 1),
                'PhepUngToiDa' => $phepUngToiDa
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
