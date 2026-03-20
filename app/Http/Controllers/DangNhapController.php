<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DangNhapController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function DangNhap()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('login.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function XuLyDangNhap(Request $request)
    {
        $request->validate([
            'TaiKhoan' => ['required', 'string'],
            'MatKhau' => ['required', 'string'],
        ], [
            'TaiKhoan.required' => 'Vui lòng nhập tài khoản.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = \App\Models\NguoiDung::where('TaiKhoan', $request->TaiKhoan)->first();

        if (!$user) {
            return back()->withErrors([
                'TaiKhoan' => 'Tài khoản không tồn tại trên hệ thống.',
            ])->withInput($request->only('TaiKhoan', 'remember'));
        }

        if ($user->TrangThai != 1) {
            return back()->withErrors([
                'TaiKhoan' => 'Tài khoản của bạn đã bị khóa hoặc ngừng hoạt động.',
            ])->withInput($request->only('TaiKhoan', 'remember'));
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->MatKhau, $user->MatKhau)) {
            return back()->withErrors([
                'MatKhau' => 'Mật khẩu không chính xác.',
            ])->withInput($request->only('TaiKhoan', 'remember'));
        }

        Auth::login($user, $request->has('remember'));

        \App\Services\SystemLogService::log('Đăng nhập', 'Hệ thống', null, 'Người dùng đăng nhập thành công');

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Đăng xuất
     */
    public function DangXuat(Request $request)
    {
        \App\Services\SystemLogService::log('Đăng xuất', 'Hệ thống', null, 'Người dùng đăng xuất khỏi hệ thống');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
