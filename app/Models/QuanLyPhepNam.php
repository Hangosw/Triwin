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
        
        if ($activeContract && isset($activeContract->NgayPhepNam)) {
            $tongPhep = (float) $activeContract->NgayPhepNam;
        } else {
            // Nếu không có hợp đồng, lấy theo cấu hình thâm niên cũ (hoặc mặc định 12)
            $config = CauHinhPhepNam::getCurrentConfig();
            $soNgayCoBan = (float) \App\Models\SystemConfig::getValue('annual_leave_days', 12);
            
            if (!$config) {
                $config = CauHinhPhepNam::create([
                    'SoNgayCoBan' => $soNgayCoBan,
                    'NamThamNien' => 5,
                    'NgayCongThem' => 1
                ]);
            }

            $ngayTuyenDung = $nhanVien->ttCongViec->NgayTuyenDung;
            $soNamCongTac = 0;
            if ($ngayTuyenDung) {
                $dtTuyenDung = $ngayTuyenDung instanceof \Carbon\Carbon ? $ngayTuyenDung : Carbon::parse((string) $ngayTuyenDung);
                $soNamCongTac = $dtTuyenDung->diffInYears(Carbon::now());
            }
            $tongPhep = (float) $config->calculateTotalLeave($soNamCongTac);
        }

        // Tính số phép khả dụng tại thời điểm khởi tạo
        $now = Carbon::now();
        $startMonth = 1;
        $ngayTuyenDung = $nhanVien->ttCongViec->NgayTuyenDung;
        $joinDate = $ngayTuyenDung ? ($ngayTuyenDung instanceof Carbon ? $ngayTuyenDung : Carbon::parse($ngayTuyenDung)) : null;

        if ($joinDate && $joinDate->year == $nam) {
            $startMonth = $joinDate->month;
        }
        $monthsWorked = max(0, $now->month - $startMonth + 1);
        $monthsWorked = min(12, $monthsWorked);
        
        // Nếu là 12 ngày/năm thì là đúng 1 ngày/tháng, 
        // Nếu nhiều hơn 12 thì ta vẫn khởi tạo tỷ lệ (Tổng/12) cho chính xác ban đầu
        $accrued = ($tongPhep / 12) * $monthsWorked;

        return self::updateOrCreate(
            ['NhanVienId' => $nhanVienId, 'Nam' => $nam],
            [
                'TongPhepDuocNghi' => $tongPhep,
                'KhaDung' => round($accrued - (float) (self::where(['NhanVienId' => $nhanVienId, 'Nam' => $nam])->first()->DaNghi ?? 0), 1),
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
