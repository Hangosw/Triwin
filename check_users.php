<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NguoiDung;
use App\Models\NhanVien;

$output = "--- Targeted Check ---\n";
$user1 = NguoiDung::find(1);
if ($user1) {
    $output .= "User 1: Ten: '{$user1->Ten}', TaiKhoan: '{$user1->TaiKhoan}'\n";
    $emp1 = NhanVien::where('NguoiDungId', 1)->first();
    if ($emp1) {
        $output .= "Linked Employee ID: {$emp1->id}, Ten: {$emp1->Ten}\n";
    } else {
        $output .= "User 1 has NO linked employee record.\n";
    }
}

$output .= "\n--- All Users ---\n";
foreach (NguoiDung::all() as $user) {
    $output .= "ID: {$user->id}, Ten: '{$user->Ten}', TaiKhoan: '{$user->TaiKhoan}'\n";
}

$output .= "\n--- Employees with no User Link ---\n";
$unlinked = NhanVien::whereNull('NguoiDungId')->get();
foreach ($unlinked as $emp) {
    $output .= "ID: {$emp->id}, Ten: '{$emp->Ten}'\n";
}

file_put_contents('c:\Users\huy hoang dz\Desktop\Triwin\check_users_results.txt', $output);
echo "Results written to check_users_results.txt\n";
