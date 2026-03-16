<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::table('nhan_viens')->insert([
        'Ma' => 'TEST_ADMIN',
        'Ten' => 'Test Admin',
        'NguoiDungId' => 1,
        'GioiTinh' => 1,
        'NgaySinh' => '1990-01-01',
        'Email' => 'test@triwin.vn',
        // 'created_at' and 'updated_at' might be needed if not auto-managed by DB
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    echo "Minimal insert successful.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
