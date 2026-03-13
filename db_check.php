<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;

$out = "";
$users = NguoiDung::with('nhanVien')->get();
foreach ($users as $user) {
    $out .= "User: {$user->TaiKhoan} | ID: {$user->id} | Employee: " . ($user->nhanVien ? $user->nhanVien->Ten : 'NONE') . " | Email: {$user->Email} | Phone: {$user->SoDienThoai}\n";
}
file_put_contents('db_check_out.txt', $out);
echo "Output written to db_check_out.txt\n";
