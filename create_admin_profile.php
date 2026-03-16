<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NguoiDung;
use App\Models\NhanVien;
use App\Models\TtNhanVienCongViec;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    $user = NguoiDung::find(1);
    if (!$user) {
        echo "User 1 not found.\n";
        exit;
    }

    $emp = NhanVien::where('NguoiDungId', 1)->first();
    if (!$emp) {
        $emp = new NhanVien();
        $emp->Ma = 'NV_ADMIN';
        $emp->Ten = 'Quản trị viên';
        $emp->NguoiDungId = 1;
        $emp->GioiTinh = 1;
        $emp->NgaySinh = '1990-01-01';
        $emp->Email = 'admin@triwin.vn';
        $emp->save();
        echo "Created NhanVien for admin.\n";
    }

    $tt = TtNhanVienCongViec::where('NhanVienId', $emp->id)->first();
    if (!$tt) {
        $tt = new TtNhanVienCongViec();
        $tt->NhanVienId = $emp->id;
        $tt->LoaiNhanVien = 1; // Văn phòng
        // Loại bỏ các cột không có trong $fillable
        $tt->NgayTuyenDung = date('Y-m-d');
        $tt->save();
        echo "Created TtNhanVienCongViec for admin.\n";
    }

    DB::commit();
    echo "Successfully linked admin user to employee records.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
