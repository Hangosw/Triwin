<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ], [
            'name.required' => 'Vui lòng nhập tên role.',
            'name.unique' => 'Tên role đã tồn tại.'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Tạo Role thành công!');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        // Lấy danh sách tên permission của role này
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ], [
            'name.required' => 'Vui lòng nhập tên role.',
            'name.unique' => 'Tên role đã tồn tại.'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Cập nhật Role thành công!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name == 'System Admin') {
            return redirect()->route('roles.index')->with('error', 'Không thể xóa Role System Admin hệ thống!');
        }
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Xóa Role thành công!');
    }
}
