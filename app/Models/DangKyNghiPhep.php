<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasUnitScoping;

class DangKyNghiPhep extends Model
{
    use HasUnitScoping;
    protected $table = 'dang_ky_nghi_pheps';

    protected $fillable = [
        'NhanVienId',
        'NguoiDuyetId',
        'LoaiNghiPhepId',
        'TuNgay',
        'DenNgay',
        'SoNgayNghi',
        'LyDo',
        'TrangThai', // 0 là từ chối, 1 là đã duyệt, 2 là đang chờ
        'Dem', // cái này đếm số lần submit, trường hợp người dùng gửi đơn bị từ chối nhiều lần
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'NguoiDuyetId' => 'integer',
        'LoaiNghiPhepId' => 'integer',
        'TuNgay' => 'date',
        'DenNgay' => 'date',
        'SoNgayNghi' => 'decimal:1',
        'TrangThai' => 'integer',
        'Dem' => 'integer',
    ];

    /**
     * Relationship: Đăng ký nghỉ phép của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Relationship: Người duyệt đơn nghỉ phép
     */
    public function nguoiDuyet()
    {
        return $this->belongsTo(NhanVien::class, 'NguoiDuyetId');
    }

    /**
     * Relationship: Loại nghỉ phép
     */
    public function loaiNghiPhep()
    {
        return $this->belongsTo(LoaiNghiPhep::class, 'LoaiNghiPhepId');
    }

    /**
     * Check if leave request is rejected
     */
    public function isRejected()
    {
        return $this->TrangThai === 0;
    }

    /**
     * Check if leave request is approved
     */
    public function isApproved()
    {
        return $this->TrangThai === 1;
    }

    /**
     * Check if leave request is pending
     */
    public function isPending()
    {
        return $this->TrangThai === 2;
    }

    /**
     * Calculate number of leave days
     */
    public function calculateLeaveDays()
    {
        if (!$this->TuNgay || !$this->DenNgay) {
            return 0;
        }

        $start = Carbon::parse($this->TuNgay);
        $end = Carbon::parse($this->DenNgay);

        // Add 1 because both start and end dates are included
        return $start->diffInDays($end) + 1;
    }

    /**
     * Scope: Approved leave requests
     */
    public function scopeDaDuyet($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Scope: Pending leave requests
     */
    public function scopeDangCho($query)
    {
        return $query->where('TrangThai', 2);
    }

    /**
     * Scope: Rejected leave requests
     */
    public function scopeTuChoi($query)
    {
        return $query->where('TrangThai', 0);
    }

    /**
     * Scope: Leave requests for specific month
     */
    public function scopeThang($query, $month, $year)
    {
        return $query->where(function ($q) use ($month, $year) {
            $q->where(function ($subQ) use ($month, $year) {
                $subQ->whereYear('TuNgay', $year)
                    ->whereMonth('TuNgay', $month);
            })
                ->orWhere(function ($subQ) use ($month, $year) {
                    $subQ->whereYear('DenNgay', $year)
                        ->whereMonth('DenNgay', $month);
                });
        });
    }
}
