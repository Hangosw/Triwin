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
        $validated = $request->validate([
            'Ten' => 'required|string|max:255',
            'TaiKhoan' => 'required|string|max:255|unique:nguoi_dungs,TaiKhoan',
            'Email' => 'nullable|email|max:255|unique:nguoi_dungs,Email',
            'SoDienThoai' => 'nullable|string|max:20',
            'TrangThai' => 'required|in:0,1',
        ], [
            'Ten.required' => 'Họ và tên không được để trống.',
            'TaiKhoan.required' => 'Tài khoản không được để trống.',
            'TaiKhoan.unique' => 'Tài khoản đã tồn tại.',
            'Email.email' => 'Email phải đúng định dạng.',
            'Email.unique' => 'Email đã được sử dụng.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ]);

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

            \App\Services\SystemLogService::log('Tạo mới', 'NguoiDung', $user->id, "Tạo mới người dùng: {$user->TaiKhoan}");

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
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();
        
        // Lấy tất cả quyền (trực tiếp + từ vai trò)
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        // Tạo mapping Role -> Quyền để dùng trong JS
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('users.edit', compact('id', 'user', 'roles', 'userRoles', 'permissions', 'userPermissions', 'rolePermissions'));
    }

    public function DataNguoiDung()
    {
        $users = NguoiDung::select(['id', 'Ten', 'TaiKhoan', 'Email', 'SoDienThoai', 'TrangThai'])
        ->latest('id')
        ->get();
        return response()->json(['data' => $users]);
    }

    public function Xoa($id)
    {
        try {
            if ($id == \Illuminate\Support\Facades\Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Bạn không thể tự xóa tài khoản của chính mình.'], 400);
            }

            $user = NguoiDung::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại.'], 404);
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Gỡ liên kết nhân viên (nếu có)
            \App\Models\NhanVien::where('NguoiDungId', $id)->update(['NguoiDungId' => null]);

            // 2. Gỡ quyền và vai trò (Spatie)
            $user->syncRoles([]);
            $user->syncPermissions([]);

            // 3. Nullify ID trong lịch sử hệ thống (để giữ log nhưng cho phép xóa user)
            \App\Models\LichSu::where('NhanVienId', $id)->update(['NhanVienId' => null]);

            // 4. Lưu log hành động trước khi xóa user record
            $taiKhoan = $user->TaiKhoan;
            \App\Services\SystemLogService::log('Xóa', 'NguoiDung', $id, "Xóa người dùng: {$taiKhoan}");

            // 5. Xóa record người dùng
            $user->delete();

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true, 'message' => 'Xóa người dùng thành công.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn người dùng cần xóa.'], 400);
        }

        // Loại bỏ ID của chính user đang đăng nhập
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $ids = array_filter($ids, fn($id) => $id != $currentUserId);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Bạn không thể tự xóa tài khoản của chính mình.'], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $users = NguoiDung::whereIn('id', $ids)->get();
            $tenTaiKhoans = $users->pluck('TaiKhoan')->implode(', ');

            // 1. Gỡ liên kết nhân viên
            \App\Models\NhanVien::whereIn('NguoiDungId', $ids)->update(['NguoiDungId' => null]);

            // 2. Gỡ quyền và vai trò
            foreach ($users as $user) {
                $user->syncRoles([]);
                $user->syncPermissions([]);
            }

            // 3. Nullify ID trong lịch sử
            \App\Models\LichSu::whereIn('NhanVienId', $ids)->update(['NhanVienId' => null]);

            // 4. Log hành động
            \App\Services\SystemLogService::log('Xóa', 'NguoiDung', null, "Xóa nhiều người dùng: {$tenTaiKhoans}");

            // 5. Xóa các record
            NguoiDung::whereIn('id', $ids)->delete();

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true, 'message' => 'Xóa ' . count($ids) . ' người dùng thành công.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function CapNhat(Request $request, $id)
    {
        // Tìm người dùng
        $user = NguoiDung::findOrFail($id);

        // Validation rules
        $rules = [
            'Ten' => 'required|string|max:255',
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
            'Ten.required' => 'Họ và tên không được để trống.',
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
            $oldData = $user->toArray();
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

            // Sync direct permissions (ngoài role)
            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions'));
            } else {
                $user->syncPermissions([]);
            }

            $newData = $user->fresh()->toArray();
            \App\Services\SystemLogService::log('Cập nhật', 'NguoiDung', $user->id, "Cập nhật thông tin người dùng: {$user->TaiKhoan}", $oldData, $newData);

            return redirect()->route('nguoi-dung.suaView', $id)
                ->with('success', 'Cập nhật thông tin người dùng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = NguoiDung::findOrFail($id);
            $user->TrangThai = $user->TrangThai == 1 ? 0 : 1;
            $user->save();

            $statusText = $user->TrangThai == 1 ? 'mở khóa' : 'khóa';
            \App\Services\SystemLogService::log('Cập nhật', 'NguoiDung', $user->id, "Thay đổi trạng thái người dùng: {$user->TaiKhoan} ({$statusText})");

            return response()->json([
                'success' => true,
                'message' => 'Đã ' . $statusText . ' người dùng thành công.',
                'new_status' => $user->TrangThai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
