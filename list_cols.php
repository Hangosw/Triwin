<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['nhan_viens', 'tt_nhan_vien_cong_viecs'];
foreach ($tables as $t) {
    echo "--- Table: $t ---\n";
    $columns = DB::select("DESCRIBE $t");
    foreach ($columns as $c) {
        $extra = "";
        if ($c->Null === 'NO') $extra .= " NOT_NULL";
        if ($c->Default !== null) $extra .= " DEFAULT({$c->Default})";
        echo "{$c->Field} | {$c->Type} | $extra\n";
    }
}
