@extends('layouts.app')

@section('title', 'Cài đặt tài khoản')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h1>Cài đặt tài khoản</h1>
        <p>Quản lý thông tin đăng nhập và bảo mật của bạn</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d1fae5; color: #065f46; border-color: #10b981; border-radius: 12px; margin-bottom: 24px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #fee2e2; color: #991b1b; border-color: #ef4444; border-radius: 12px; margin-bottom: 24px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Section: Update Email -->
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 48px; height: 48px; background: #ecfdf5; color: #059669; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 16px;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h4 class="mb-0" style="font-weight: 700; color: #1f2937;">Thông tin tài khoản</h4>
                    </div>
                    
                    <form action="{{ route('profile.update-email') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label" style="font-weight: 600; color: #4b5563;">Địa chỉ Email đăng nhập</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->Email) }}" placeholder="example@triwin.vn">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-2 d-block">Lưu ý: Email này dùng để đăng nhập và nhận thông báo hệ thống.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: 10px; font-weight: 600;">
                            Cập nhật Email
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section: Update Password -->
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 48px; height: 48px; background: #fffbeb; color: #d97706; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 16px;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h4 class="mb-0" style="font-weight: 700; color: #1f2937;">Bảo mật & Mật khẩu</h4>
                    </div>

                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" style="font-weight: 600; color: #4b5563;">Mật khẩu hiện tại</label>
                            <div class="position-relative">
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                                       placeholder="••••••••">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" style="font-weight: 600; color: #4b5563;">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" 
                                   placeholder="Tối thiểu 6 ký tự">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label" style="font-weight: 600; color: #4b5563;">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" class="form-control" 
                                   placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: 10px; font-weight: 600; background-color: #0d9488; border: none;">
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Dark mode adjustments */
    body.dark-theme .card {
        background-color: #1a1d27 !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2) !important;
    }
    body.dark-theme h1, 
    body.dark-theme h4, 
    body.dark-theme .form-label {
        color: #e8eaf0 !important;
    }
    body.dark-theme .text-muted {
        color: #8b93a8 !important;
    }
    body.dark-theme .form-control {
        background-color: #21263a !important;
        border-color: #2e3349 !important;
        color: #e8eaf0 !important;
    }
</style>
@endsection
