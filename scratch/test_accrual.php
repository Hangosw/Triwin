<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuanLyPhepNam;
use App\Models\NhanVien;
use Carbon\Carbon;

function testAccrual($tongPhep, $joinDateStr, $currentMonth, $daNghi = 0) {
    Carbon::setTestNow(Carbon::create(2026, $currentMonth, 15));
    
    $phepNam = new QuanLyPhepNam([
        'Nam' => 2026,
        'TongPhepDuocNghi' => $tongPhep,
        'DaNghi' => $daNghi,
        'ConLai' => $tongPhep - $daNghi
    ]);

    // Mock nhanVien relationship
    $nv = new NhanVien();
    $tt = new \stdClass();
    $tt->NgayTuyenDung = $joinDateStr;
    $nv->ttCongViec = $tt;
    
    // We can't easily mock the relationship without database, so let's just test the logic inside the accessor
    // I'll replicate the logic here for verification
    
    $now = Carbon::now();
    $joinDate = $joinDateStr ? Carbon::parse($joinDateStr) : null;
    
    $startMonth = 1;
    if ($joinDate && $joinDate->year == $phepNam->Nam) {
        $startMonth = $joinDate->month;
    }

    $monthsWorked = max(0, $now->month - $startMonth + 1);
    $monthsWorked = min(12, $monthsWorked);

    $accrued = ($phepNam->TongPhepDuocNghi / 12) * $monthsWorked;
    $available = (float) $accrued - (float) $phepNam->DaNghi;
    $result = max(0.0, round($available, 1));

    echo "Total: $tongPhep, Join: $joinDateStr, Month: $currentMonth, DaNghi: $daNghi => Accrued Months: $monthsWorked, Available: $result\n";
}

echo "Testing Accrual Logic:\n";
testAccrual(12, '2020-01-01', 1);  // Expected: 1.0
testAccrual(12, '2020-01-01', 4);  // Expected: 4.0
testAccrual(12, '2026-03-01', 4);  // Joined Mar, Now Apr balance => 2 months (Mar, Apr) => 2.0
testAccrual(14, '2020-01-01', 6);  // (14/12)*6 = 7.0
testAccrual(12, '2020-01-01', 4, 1); // 4.0 - 1 = 3.0

Carbon::setTestNow(); // Reset
