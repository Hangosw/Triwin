<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $out = \Illuminate\Support\Facades\Artisan::output();
    file_put_contents('migrate_out.txt', "SUCCESS:\n" . $out);
} catch (\Exception $e) {
    file_put_contents('migrate_out.txt', "ERROR CATCHED: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}

