<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Khởi tạo các permissions bằng tiếng Việt
        $permissions = [
            // Quản lý người dùng
            'Quản lý người dùng',

            // Nhân viên
            'Xem nhân viên',
            'Thêm nhân viên',
            'Sửa nhân viên',
            'Xóa nhân viên',

            // Đơn vị, Phòng ban, Chức vụ
            'Quản lý tổ chức',

            // Hợp đồng
            'Xem hợp đồng',
            'Quản lý hợp đồng',

            // Chấm công
            'Xem chấm công',
            'Quản lý chấm công',

            // Tăng ca & Nghỉ phép
            'Xem tăng ca nghỉ phép',
            'Quản lý tăng ca nghỉ phép',
            'Duyệt yêu cầu',

            // Lương
            'Xem lương',
            'Quản lý lương',
            'Tính lương',

            // Văn thư
            'Xem văn thư',
            'Quản lý văn thư',

            // Công tác
            'Xem công tác',
            'Quản lý công tác',

            // Hệ thống
            'Quản lý hệ thống',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo Roles và gán permissions

        // 1. Super Admin (Toàn quyền hệ thống)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super Admin sẽ được Gate::before cho phép mọi thứ, 
        // nhưng vẫn gán full permissions cho chắc chắn và hiển thị UI.
        $superAdmin->syncPermissions(Permission::all());

        // 2. Nhân viên (Quyền cơ bản)
        $employee = Role::firstOrCreate(['name' => 'Nhân viên']);
        $employee->syncPermissions([
            'Xem nhân viên',
            'Xem chấm công',
            'Xem tăng ca nghỉ phép',
            'Xem lương',
            'Xem công tác',
        ]);

        // Gán Role Super Admin cho người dùng ID 1 nếu tồn tại (để demo)
        $adminUser = \App\Models\NguoiDung::find(1);
        if ($adminUser) {
            $adminUser->assignRole('Super Admin');
        }
    }
}
