@extends('layouts.app')

@push('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0BAA4B;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-row-3col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .form-row-3col {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .form-row-3col {
                grid-template-columns: 1fr;
            }
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label span.text-danger {
            margin-left: 4px;
        }

        /* Dark Mode Overrides */
        body.dark-theme .form-section {
            background-color: #1a1d27;
            border-color: #2e3349;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        body.dark-theme .form-section h2 {
            color: #e8eaf0;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .form-group label {
            color: #8b93a8;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
        }

        body.dark-theme .form-control, 
        body.dark-theme .form-select {
            background-color: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        .roles-container {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.02);
            transition: border-color 0.2s, background-color 0.2s;
        }

        body.dark-theme .roles-container {
            border-color: #2e3349;
            background-color: #21263a;
        }

        .role-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            cursor: pointer;
            font-size: 14px;
        }

        body.dark-theme .role-label span {
            color: #e8eaf0;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column-reverse;
            }
            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('title', 'Thêm người dùng mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <!-- Header -->
    <div class="mb-4">
        <h1 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 8px; letter-spacing: -0.02em; line-height: 1.2;">
            Thêm người dùng mới
        </h1>
        <p style="color: #6b7280; font-size: 16px; margin-bottom: 20px;">
            Tạo tài khoản người dùng mới trong hệ thống
        </p>
        <a href="{{ route('nguoi-dung.danh-sach') }}" 
           style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; color: #1f2937; font-weight: 600; font-size: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;"
           onmouseover="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#f9fafb';"
           onmouseout="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white';">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    <form action="{{ route('nguoi-dung.tao') }}" method="POST">
        @csrf

        <!-- Thông tin tài khoản -->
        <div class="form-section">
            <h2>Thông tin tài khoản</h2>
            <div class="form-row-3col">
                <div class="form-group">
                    <label>Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="Ten" class="form-control" placeholder="Nguyễn Văn A" required>
                </div>

                <div class="form-group">
                    <label>Tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="TaiKhoan" class="form-control" placeholder="Nhập tài khoản" required autofocus>
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="TrangThai" class="form-select">
                        <option value="1" selected>Đang hoạt động</option>
                        <option value="0">Bị Khóa</option>
                    </select>
                </div>
            </div>

            <div class="form-row-3col">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="Email" class="form-control" placeholder="example@triwin.vn">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control" placeholder="0901234567">
                </div>

                <div class="form-group">
                    <label>Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <!-- Vai trò -->
        <div class="form-section">
            <h2>Vai trò (Roles)</h2>
            <div class="roles-container">
                <div class="row g-2">
                    @foreach ($roles as $role)
                        <div class="col-12 col-sm-6 col-md-4">
                            <label class="role-label">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-check-input role-checkbox">
                                <span>{{ $role->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Mặc định người dùng sẽ không có quyền hạn nào nếu không chọn vai trò.</small>
        </div>

        <div class="form-actions">
            <a href="{{ route('nguoi-dung.danh-sach') }}" class="btn btn-light px-4 py-2" style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 500;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-success px-4 py-2" style="display: inline-flex; align-items: center; gap: 8px; background: #0baa4b; border-color: #0baa4b; color: white; font-weight: 500;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Lưu thông tin
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
             @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc2626'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi Validation!',
                    html: `
                        <ul style="text-align: left; margin-bottom: 0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#dc2626'
                });
            @endif
        });
    </script>
@endpush
