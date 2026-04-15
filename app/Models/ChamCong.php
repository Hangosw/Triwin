<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class ChamCong extends Model
{
    
    protected $table = 'cham_congs';

    protected $fillable = [
        'NhanVienId',
        'Loai', // 0: Hành chính, 1: Tăng ca
        'Vao',
        'Ra',
        'TrangThai', // tre, dung_gio, ve_som
        'Cong',
        'AnhChamCong',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'Loai' => 'integer',
        'Vao' => 'datetime',
        'Ra' => 'datetime',
        'TrangThai' => 'string',
        'Cong' => 'decimal:2',
    ];

    /**
     * Relationship: Chấm công của nhân viên
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Check if attendance is on time
     */
    public function isDungGio()
    {
        return $this->TrangThai === 'dung_gio';
    }

    /**
     * Check if attendance is late
     */
    public function isTreGio()
    {
        return $this->TrangThai === 'tre';
    }

    /**
     * Check if attendance is leaving early
     */
    public function isVeSom()
    {
        return $this->TrangThai === 've_som';
    }

    /**
     * Calculate working hours
     */
    public function getWorkingHours()
    {
        if (!$this->Vao || !$this->Ra) {
            return 0;
        }
        return Carbon::parse($this->Vao)->diffInHours(Carbon::parse($this->Ra));
    }

    /**
     * Scope: On-time attendance
     */
    public function scopeDungGio($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Scope: Late attendance
     */
    public function scopeTreGio($query)
    {
        return $query->where('TrangThai', 0);
    }

    /**
     * Scope: Attendance for specific date
     */
    public function scopeNgay($query, $date)
    {
        return $query->whereDate('Vao', $date);
    }

    /**
     * Scope: Attendance for specific month
     */
    public function scopeThang($query, $month, $year)
    {
        return $query->whereYear('Vao', $year)
            ->whereMonth('Vao', $month);
    }
}
