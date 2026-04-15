<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$affected = DB::table('hop_dongs')
    ->where('SoHopDong', 'like', '#%')
    ->get();

foreach ($affected as $row) {
    $newSo = substr($row->SoHopDong, 1);
    DB::table('hop_dongs')->where('id', $row->id)->update(['SoHopDong' => $newSo]);
    echo "Updated ID {$row->id}: {$row->SoHopDong} -> {$newSo}\n";
}

echo "Done cleaning contracts.\n";
