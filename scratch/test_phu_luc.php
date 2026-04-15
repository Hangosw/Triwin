<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\PhuLucHopDong;
use Illuminate\Support\Facades\DB;
use App\Models\NhanVien;
use App\Services\LuongService;

// Test 1: Check chi_tiet_phu_luc table
echo "=== Test chi_tiet_phu_luc ===\n";
try {
    $count = DB::table('chi_tiet_phu_luc')->count();
    echo "chi_tiet_phu_luc count: $count\n";
    $tong = DB::table('chi_tiet_phu_luc')->sum('so_tien');
    echo "chi_tiet_phu_luc total so_tien: $tong\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test 2: Check phu_luc_hop_dongs table
echo "\n=== Test phu_luc_hop_dongs ===\n";
try {
    $pls = PhuLucHopDong::with('dieuKhoans')->take(3)->get();
    foreach ($pls as $pl) {
        echo "PhuLuc ID={$pl->id}, HopDongGocId={$pl->HopDongGocId}, ngay_ky={$pl->ngay_ky}\n";
        $sum = DB::table('chi_tiet_phu_luc')->where('PhuLucId', $pl->id)->sum('so_tien');
        echo "  -> Total so_tien: $sum\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test 3: Test tinhTongPhuCap with a real contract
echo "\n=== Test tinhTongPhuCap ===\n";
try {
    $nv = NhanVien::with(['hopDongs' => fn($q) => $q->where('TrangThai', 1)->latest(), 'thanNhans', 'ttCongViec'])->first();
    if ($nv) {
        $hopDong = $nv->hopDongs->first();
        echo "NhanVien: {$nv->Ten}, HopDong ID=" . ($hopDong ? $hopDong->id : 'none') . "\n";
        if ($hopDong) {
            $phuCap = LuongService::tinhTongPhuCap($hopDong);
            echo "tinhTongPhuCap result: $phuCap\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
