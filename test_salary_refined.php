<?php

use App\Models\NhanVien;
use App\Services\LuongService;
use App\Models\SystemConfig;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function testCalculation($type, $standardWorkDays = null) {
    SystemConfig::updateOrCreate(['key' => 'salary_calculation_type'], ['value' => $type]);
    if ($standardWorkDays !== null) {
        SystemConfig::updateOrCreate(['key' => 'standard_work_days'], ['value' => $standardWorkDays]);
    } else {
        SystemConfig::where('key', 'standard_work_days')->delete();
    }
    
    echo "Testing with type: $type, standard_work_days: " . ($standardWorkDays ?? 'NULL (using calendar)') . "\n";
    
    // Test an office worker
    $nv = NhanVien::whereHas('ttCongViec', function($q) {
        $q->where('LoaiNhanVien', 1);
    })->first();
    
    if ($nv) {
        $result = LuongService::tinhLuong($nv, 3, 2026);
        echo "Office Worker ({$nv->Ten}): {$result['loai_nhan_vien_text']} | Income: " . number_format($result['tong_thu_nhap']) . " | Standard Days: {$result['ngay_cong_chuan']}\n";
    }

    // Test a worker
    $nvw = NhanVien::whereHas('ttCongViec', function($q) {
        $q->where('LoaiNhanVien', 0);
    })->first();
    
    if ($nvw) {
        $resultw = LuongService::tinhLuong($nvw, 3, 2026);
        echo "Worker ({$nvw->Ten}): {$resultw['loai_nhan_vien_text']} | Income: " . number_format($resultw['tong_thu_nhap']) . " | Standard Days: {$resultw['ngay_cong_chuan']}\n";
    }
    echo "-----------------------------------\n";
}

testCalculation('contract');
testCalculation('attendance');
testCalculation('attendance', 26);
testCalculation('attendance', 24);

// Restore to contract
SystemConfig::updateOrCreate(['key' => 'salary_calculation_type'], ['value' => 'contract']);
SystemConfig::where('key', 'standard_work_days')->delete();
