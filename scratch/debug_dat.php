<?php
use App\Models\NhanVien;
use App\Models\QuanLyPhepNam;
use App\Models\HopDong;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nv = NhanVien::where('Ten', 'like', '%TRẦN QUỐC ĐẠT%')->with(['ttCongViec', 'hopDongs'])->first();

if (!$nv) {
    echo "Khong tim thay nhan vien\n";
    exit;
}

echo "NV ID: " . $nv->id . "\n";
echo "Ten: " . $nv->Ten . "\n";
echo "NgayTuyenDung: " . ($nv->ttCongViec->NgayTuyenDung ?? 'NULL') . "\n";

$hd = $nv->hopDongs()->where('TrangThai', 1)->latest()->first();
if ($hd) {
    echo "Contract ID: " . $hd->id . "\n";
    echo "Contract Start: " . $hd->NgayBatDau . "\n";
    echo "NgayPhepNam: " . $hd->NgayPhepNam . "\n";
    echo "NgayPhepKhaDung: " . $hd->NgayPhepKhaDung . "\n";
} else {
    echo "Khong tim thay hop dong dang hieu luc\n";
}

$pn = QuanLyPhepNam::where('NhanVienId', $nv->id)->where('Nam', 2026)->first();
if ($pn) {
    echo "PhepNam 2026: TongPhep=" . $pn->TongPhepDuocNghi . ", KhaDung=" . $pn->KhaDung . ", DaNghi=" . $pn->DaNghi . "\n";
} else {
    echo "Khong tim thay ban ghi phep nam 2026\n";
}

// Chạy lại hàm khởi tạo để xem kết quả mới nhất
echo "--- Chay lai khoi tao ---\n";
$newPn = QuanLyPhepNam::khoiTaoPhepNam($nv->id, 2026);
echo "Result: TongPhep=" . $newPn->TongPhepDuocNghi . ", KhaDung=" . $newPn->KhaDung . "\n";
