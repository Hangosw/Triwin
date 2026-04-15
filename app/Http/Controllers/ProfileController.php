<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\NguoiDung;
use App\Services\SystemLogService;

class ProfileController extends Controller
{
    /**
     * Show profile settings view.
     */
    public function SettingsView()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Update user password.
     */
    public function UpdatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->MatKhau)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không chính xác.');
        }

        // Update password
        $user->MatKhau = Hash::make($request->new_password);
        $user->save();

        SystemLogService::log('Cập nhật', 'NguoiDung', $user->id, "Người dùng [{$user->TaiKhoan}] tự đổi mật khẩu.");

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Update user email.
     */
    public function UpdateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:nguoi_dungs,Email,' . $user->id,
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
        ]);

        $oldEmail = $user->Email;
        $user->Email = $request->email;
        $user->save();

        SystemLogService::log('Cập nhật', 'NguoiDung', $user->id, "Người dùng [{$user->TaiKhoan}] tự đổi email từ [{$oldEmail}] sang [{$user->Email}].");

        return redirect()->back()->with('success', 'Cập nhật email thành công!');
    }
}
