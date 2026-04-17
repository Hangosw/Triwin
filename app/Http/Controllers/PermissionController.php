<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::withCount('roles')->get();
        
        // Specific groups requested by the user
        $configGroups = [
            'Nhân viên'      => ['nhân viên', 'thông tin'],
            'Người dùng'     => ['người dùng'],
            'Hợp đồng'       => ['hợp đồng'],
            'Chấm công'      => ['chấm công'],
            'Work From Home' => ['wfh', 'work from home'],
            'Nghỉ phép'      => ['nghỉ phép', 'phép'],
            'Lương'          => ['lương'],
            'Công tác'       => ['công tác'],
        ];

        $groupedPermissions = [];
        $assignedIds = [];

        foreach ($configGroups as $label => $keywords) {
            $matched = $permissions->filter(function($p) use ($keywords, $assignedIds) {
                if (in_array($p->id, $assignedIds)) return false;
                $nameLower = mb_strtolower($p->name);
                foreach ($keywords as $kw) {
                    if (str_contains($nameLower, mb_strtolower($kw))) return true;
                }
                return false;
            });

            if ($matched->isNotEmpty()) {
                $groupedPermissions[$label] = $matched;
                $assignedIds = array_merge($assignedIds, $matched->pluck('id')->toArray());
            }
        }

        // Add remaining permissions to 'Khác'
        $remaining = $permissions->filter(fn($p) => !in_array($p->id, $assignedIds));
        if ($remaining->isNotEmpty()) {
            $groupedPermissions['Khác'] = $remaining;
        }

        return view('permissions.index', compact('groupedPermissions'));
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
