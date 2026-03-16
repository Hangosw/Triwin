<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;

use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    public function DanhSachView()
    {
        return view('users.index');
    }

    public function TaoView()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('roles'));
    }

    public function Tao(Request $request)
    {
        $messages = [
            'TaiKhoan.required' => 'Tài khoản không được để trống.',
            'TaiKhoan.unique' => 'Tài khoản đã tồn tại.',
            'Email.email' => 'Email phải đúng định dạng.',
            'Email.unique' => 'Email đã được sử dụng.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ];

        $validated = $request->validate([
            'Ten' => 'nullable|string|max:255',
            'TaiKhoan' => 'required|string|max:255|unique:nguoi_dungs,TaiKhoan',
            'Email' => 'nullable|email|max:255|unique:nguoi_dungs,Email',
            'SoDienThoai' => 'nullable|string|max:20',
            'TrangThai' => 'required|in:0,1',
        ], $messages);

        try {
            $user = new NguoiDung();
            $user->Ten = $validated['Ten'] ?? null;
            $user->TaiKhoan = $validated['TaiKhoan'];
            $user->Email = $validated['Email'] ?? null;
            $user->SoDienThoai = $validated['SoDienThoai'] ?? null;
            $user->TrangThai = $validated['TrangThai'];

            if ($request->filled('password')) {
                $user->MatKhau = bcrypt($request->password);
            }

            $user->save();

            if ($request->has('roles')) {
                $user->assignRole($request->input('roles'));
            }



            return redirect()->route('nguoi-dung.danh-sach')
                ->with('success', 'Thêm người dùng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Thêm người dùng thất bại: ' . $e->getMessage());
        }
    }

    public function SuaView($id)
    {
        $user = NguoiDung::where('id', $id)->firstOrFail();
        $roles = \Spatie\Permission\Models\Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('id', 'user', 'roles', 'userRoles'));
    }

    public function DataNguoiDung()
    {
        $users = NguoiDung::whereHas('nhanVien', function ($q) {
            $q;
        })->select(['id', 'Ten', 'TaiKhoan', 'Email', 'SoDienThoai', 'TrangThai'])->get();
        return response()->json(['data' => $users]);
    }

    public function Xoa($id)
    {
        try {
            NguoiDung::destroy($id);
            return response()->json(['success' => true, 'message' => 'Xóa người dùng thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn người dùng cần xóa.'], 400);
        }

        try {
            NguoiDung::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Xóa ' . count($ids) . ' người dùng thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function CapNhat(Request $request, $id)
    {
        // Tìm người dùng
        $user = NguoiDung::findOrFail($id);

        // Validation rules
        $rules = [
            'Ten' => 'nullable|string|max:255',
            'TaiKhoan' => 'required|string|max:255|unique:nguoi_dungs,TaiKhoan,' . $id,
            'Email' => 'nullable|email|max:255|unique:nguoi_dungs,Email,' . $id,
            'SoDienThoai' => 'nullable|string|max:20',
            'TrangThai' => 'required|in:0,1',
        ];

        // Nếu có nhập mật khẩu mới
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }

        // Custom error messages
        $messages = [
            'TaiKhoan.required' => 'Tài khoản không được để trống.',
            'TaiKhoan.unique' => 'Tài khoản đã tồn tại.',
            'Email.email' => 'Email phải đúng định dạng.',
            'Email.unique' => 'Email đã được sử dụng.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu.',
            'password_confirmation.same' => 'Mật khẩu nhập lại không khớp.',
        ];

        // Validate
        $validated = $request->validate($rules, $messages);

        try {
            // Cập nhật thông tin cơ bản
            $user->Ten = $validated['Ten'] ?? $user->Ten;
            $user->TaiKhoan = $validated['TaiKhoan'];
            $user->Email = $validated['Email'] ?? null;
            $user->SoDienThoai = $validated['SoDienThoai'] ?? null;
            $user->TrangThai = $validated['TrangThai'];

            // Nếu có mật khẩu mới và khớp với nhập lại, hash và cập nhật
            if ($request->filled('password') && $request->password === $request->password_confirmation) {
                $user->MatKhau = bcrypt($request->password);
            }

            $user->save();

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles([]);
            }



            return redirect()->route('nguoi-dung.suaView', $id)
                ->with('success', 'Cập nhật thông tin người dùng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
