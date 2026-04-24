<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuanLyPhepNam;

$count = 0;
foreach(QuanLyPhepNam::all() as $phep) {
    $phep->ConLai = (float)$phep->TongPhepDuocNghi - (float)($phep->DaNghi ?? 0);
    
    // PhepUngToiDa = Tổng - (KhaDung + DaNghi)
    // Thực tế, PhepUngToiDa chính là phần chưa được tích lũy
    // Giả sử KhaDung là số dư hiện tại của phần đã tích lũy
    // Thì phần còn lại của năm có thể ứng = Tong - DaNghi - KhaDung
    $phep->PhepUngToiDa = max(0, (float)$phep->TongPhepDuocNghi - (float)($phep->DaNghi ?? 0) - max(0, (float)$phep->KhaDung));
    
    $phep->save();
    $count++;
}
echo "Fixed $count records.\n";
