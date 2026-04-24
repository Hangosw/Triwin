<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;

// Search for user by account name or name
$user = NguoiDung::where('Ten', 'like', '%TRƯƠNG VĂN LỢI%')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: " . $user->Ten . "\n";
echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
echo "Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n";
echo "Has 'Nhân viên': " . ($user->hasRole('Nhân viên') ? 'Yes' : 'No') . "\n";
echo "Has 'Nhân Viên': " . ($user->hasRole('Nhân Viên') ? 'Yes' : 'No') . "\n";
echo "Can 'Quản lý hệ thống': " . ($user->can('Quản lý hệ thống') ? 'Yes' : 'No') . "\n";
