<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::withCount('roles')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ], [
            'name.required' => 'Vui lòng nhập tên Quyền.',
            'name.unique' => 'Tên Quyền đã tồn tại.'
        ]);

        $name = mb_convert_case(trim($request->name), MB_CASE_TITLE, 'UTF-8');
        Permission::create(['name' => $name]);

        return redirect()->route('permissions.index')->with('success', 'Tạo Quyền thành công!');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id
        ], [
            'name.required' => 'Vui lòng nhập tên Quyền.',
            'name.unique' => 'Tên Quyền đã tồn tại.'
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = mb_convert_case(trim($request->name), MB_CASE_TITLE, 'UTF-8');
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Cập nhật Quyền thành công!');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Xóa Quyền thành công!');
    }
}
