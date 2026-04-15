<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuanLyPhepNam;
use Carbon\Carbon;

$currentYear = now()->year;
$records = QuanLyPhepNam::where('Nam', $currentYear)->get();
$count = 0;

echo "Initial Data Population for KhaDung...\n";

foreach ($records as $record) {
    if (!$record->nhanVien || !$record->nhanVien->ttCongViec) continue;

    $now = Carbon::now();
    $startMonth = 1;
    $ngayTuyenDung = $record->nhanVien->ttCongViec->NgayTuyenDung;
    $joinDate = $ngayTuyenDung ? ($ngayTuyenDung instanceof Carbon ? $ngayTuyenDung : Carbon::parse($ngayTuyenDung)) : null;

    if ($joinDate && $joinDate->year == $currentYear) {
        $startMonth = $joinDate->month;
    }
    
    $monthsWorked = max(0, $now->month - $startMonth + 1);
    $monthsWorked = min(12, $monthsWorked);
    
    // Calculate initial accrued leave
    $accrued = ($record->TongPhepDuocNghi / 12) * $monthsWorked;
    
    // Subtract used days
    $record->KhaDung = round(max(0, $accrued - (float) $record->DaNghi), 1);
    $record->save();
    
    echo "ID: {$record->id}, Employee: {$record->nhanVien->Ten}, Accrued: {$accrued}, Used: {$record->DaNghi}, Final KhaDung: {$record->KhaDung}\n";
    $count++;
}

echo "Finished. Updated {$count} records.\n";
