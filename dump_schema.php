<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['nhan_viens', 'tt_nhan_vien_cong_viecs'];
foreach ($tables as $t) {
    echo "--- $t ---\n";
    $columns = DB::select("DESCRIBE $t");
    foreach ($columns as $c) {
        printf("%-20s | %-15s | %-5s | %-5s\n", $c->Field, $c->Type, $c->Null, $c->Key);
    }
}
