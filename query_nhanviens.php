<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::table('nhan_viens')->where('Ma', 'TEST_ADMIN')->delete();

$nv = \App\Models\NhanVien::where('NguoiDungId', 1)->first();
if ($nv) {
    echo "Eloquent Found: ID: {$nv->id} | Ma: {$nv->Ma} | Ten: {$nv->Ten}\n";
} else {
    echo "Eloquent NOT Found for NguoiDungId 1\n";
    $raw = DB::table('nhan_viens')->where('NguoiDungId', 1)->first();
    if ($raw) {
        echo "Raw DB Found: ID: {$raw->id} | Ma: {$raw->Ma} | Ten: {$raw->Ten}\n";
    }
}
