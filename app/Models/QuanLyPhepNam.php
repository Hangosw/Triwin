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
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'Nam' => 'integer',
        'TongPhepDuocNghi' => 'decimal:1',
        'DaNghi' => 'decimal:1',
        'ConLai' => 'decimal:1',
        'KhaDung' => 'decimal:1',
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
        $this->DaNghi += $days;
        $this->ConLai = (float) $this->TongPhepDuocNghi - (float) $this->DaNghi;
        $this->KhaDung -= $days;
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

        // Ưu tiên lấy từ hợp đồng đang hiệu lực
        $activeContract = $nhanVien->hopDongs()->where('TrangThai', 1)->latest()->first();
        $ngayTuyenDung = $nhanVien->ttCongViec->NgayTuyenDung;
        $joinDate = $ngayTuyenDung ? ($ngayTuyenDung instanceof Carbon ? $ngayTuyenDung : Carbon::parse($ngayTuyenDung)) : null;
        
        $tongPhep = 12.0; // Mặc định
        $fullYearPhep = 12.0;

        if ($activeContract) {
            $fullYearPhep = (float) ($activeContract->NgayPhepNam ?? 12);
            $contractStartDate = Carbon::parse($activeContract->NgayBatDau);
            
            // Nếu năm đang khởi tạo là năm bắt đầu hợp đồng (thường là năm đầu tiên)
            if ($nam == $contractStartDate->year) {
                $tongPhep = (float) ($activeContract->NgayPhepKhaDung ?? $fullYearPhep);
            } else {
                // Các năm tiếp theo tự động lấy NgayPhepNam
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
        
        if ($activeContract) {
            $contractStartDate = Carbon::parse($activeContract->NgayBatDau);
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
        $daNghi = (float) ($existing->DaNghi ?? 0);

        return self::updateOrCreate(
            ['NhanVienId' => $nhanVienId, 'Nam' => $nam],
            [
                'TongPhepDuocNghi' => $tongPhep,
                'KhaDung' => round($accrued - $daNghi, 1),
                'ConLai' => round($tongPhep - $daNghi, 1)
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
