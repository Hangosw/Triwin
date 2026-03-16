<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class TangCa extends Model
{
    
    protected $table = 'tang_cas';

    protected $fillable = [
        'NhanVienId',
        'LoaiTangCaId', // Bổ sung: Liên kết tới dm_tang_cas để lấy hệ số
        'NguoiDuyetId',
        'Ngay',
        'BatDau',
        'KetThuc',
        'Tong',
        'TrangThai', // dang_cho, da_duyet, tu_choi
        'Dem',       // Đếm số lần submit, tối đa 3 lần
        'LyDo',
        'GhiChuLanhDao',
    ];

    protected $casts = [
        'NhanVienId' => 'integer',
        'LoaiTangCaId' => 'integer',
        'NguoiDuyetId' => 'integer',
        'Ngay' => 'date',
        'Tong' => 'float',
        'Dem' => 'integer',
    ];

    /**
     * Relationship: Loại tăng ca để lấy hệ số lương
     */
    public function loaiTangCa()
    {
        return $this->belongsTo(DmTangCa::class, 'LoaiTangCaId');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }

    /**
     * Kiểm tra xem còn quyền được submit lại hay không (Tối đa 3 lần)
     */
    public function canResubmit()
    {
        return $this->Dem < 3 && $this->TrangThai === 'tu_choi';
    }

    /**
     * Tự động tính tổng giờ khi lưu (thay vì gọi hàm lẻ)
     * Bạn có thể gọi cái này trong Observer hoặc Controller trước khi save
     */
    public function calculateTotal()
    {
        if ($this->BatDau && $this->KetThuc) {
            $start = Carbon::parse($this->BatDau);
            $end = Carbon::parse($this->KetThuc);

            // Xử lý trường hợp tăng ca xuyên đêm (ví dụ từ 22h đến 2h sáng hôm sau)
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $this->Tong = round($start->diffInMinutes($end) / 60, 2);
        }
    }

    // Các helper check trạng thái của bạn giữ nguyên vì rất tốt
    public function isRejected()
    {
        return $this->TrangThai === 'tu_choi';
    }
    public function isApproved()
    {
        return $this->TrangThai === 'da_duyet';
    }
    public function isPending()
    {
        return $this->TrangThai === 'dang_cho';
    }
}
