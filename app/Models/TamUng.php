<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamUng extends Model
{
    use HasFactory;

    // Khai báo tên bảng (vì tên bảng không theo chuẩn tiếng Anh số nhiều mặc định)
    protected $table = 'tam_ungs';

    // Các trường cho phép thêm/sửa hàng loạt (Mass Assignment)
    protected $fillable = [
        'NhanVienId',
        'NguoiDuyetId',
        'SoTien',
        'HanMuc',
        'TrangThai',
        'GhiChu',
        'Lydo',
    ];

    /**
     * Mối quan hệ: Tạm ứng thuộc về 1 Nhân viên (Người tạo)
     */
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId', 'id');
    }

    /**
     * Mối quan hệ: Tạm ứng được duyệt bởi 1 Nhân viên (Người duyệt)
     */
    public function nguoiDuyet()
    {
        return $this->belongsTo(NhanVien::class, 'NguoiDuyetId', 'id');
    }
}