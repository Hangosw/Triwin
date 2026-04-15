@extends('layouts.app')

@section('title', 'Cài đặt - Vietnam Rubber Group')

@section('content')
<div class="page-header">
    <h1>Cài đặt</h1>
    <p>Quản lý cài đặt tài khoản và hệ thống</p>
</div>

<!-- Profile Settings -->
<div class="card">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Thông tin tài khoản</h3>
    
    <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px;">
        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop" 
             alt="Avatar" 
             style="width: 80px; height: 80px; border-radius: 50%;">
        <div>
            <button class="btn btn-primary" style="margin-bottom: 8px;">Thay đổi ảnh</button>
            <div style="font-size: 13px; color: #6b7280;">JPG, PNG. Tối đa 2MB</div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div class="form-group">
            <label class="form-label">Họ và tên</label>
            <input type="text" class="form-control" value="Admin User">
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="admin@vrg.com.vn">
        </div>
        <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" value="0901234567">
        </div>
        <div class="form-group">
            <label class="form-label">Chức vụ</label>
            <input type="text" class="form-control" value="Quản trị viên" disabled>
        </div>
    </div>
    
    <button class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Cập nhật thông tin
    </button>
</div>

<!-- Security Settings -->
<div class="card">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Bảo mật</h3>
    
    <div class="form-group">
        <label class="form-label">Mật khẩu hiện tại</label>
        <input type="password" class="form-control" placeholder="Nhập mật khẩu hiện tại">
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div class="form-group">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" placeholder="Nhập mật khẩu mới">
        </div>
        <div class="form-group">
            <label class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới">
        </div>
    </div>
    
    <button class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Đổi mật khẩu
    </button>
</div>

<!-- Notification Settings -->
<div class="card">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Thông báo</h3>
    
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border-radius: 8px;">
            <div>
                <div class="font-medium">Thông báo email</div>
                <div class="text-gray" style="font-size: 13px;">Nhận thông báo qua email</div>
            </div>
            <label style="position: relative; display: inline-block; width: 50px; height: 24px;">
                <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #0BAA4B; transition: 0.4s; border-radius: 24px;"></span>
            </label>
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border-radius: 8px;">
            <div>
                <div class="font-medium">Thông báo trên trình duyệt</div>
                <div class="text-gray" style="font-size: 13px;">Hiển thị thông báo push</div>
            </div>
            <label style="position: relative; display: inline-block; width: 50px; height: 24px;">
                <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #0BAA4B; transition: 0.4s; border-radius: 24px;"></span>
            </label>
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border-radius: 8px;">
            <div>
                <div class="font-medium">Báo cáo hàng tuần</div>
                <div class="text-gray" style="font-size: 13px;">Nhận báo cáo tổng hợp mỗi tuần</div>
            </div>
            <label style="position: relative; display: inline-block; width: 50px; height: 24px;">
                <input type="checkbox" style="opacity: 0; width: 0; height: 0;">
                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #d1d5db; transition: 0.4s; border-radius: 24px;"></span>
            </label>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="card" style="border: 1px solid #ef4444;">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #ef4444;">Vùng nguy hiểm</h3>
    
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #fef2f2; border-radius: 8px; margin-bottom: 16px;">
        <div>
            <div class="font-medium" style="color: #991b1b;">Xóa tài khoản</div>
            <div style="font-size: 13px; color: #7f1d1d;">Hành động này không thể hoàn tác</div>
        </div>
        <button class="btn" style="background-color: #ef4444; color: white;">
            Xóa tài khoản
        </button>
    </div>
</div>
@endsection

@push('scripts')
@endpush
