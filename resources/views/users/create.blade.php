@extends('layouts.app')



@section('title', 'Thêm người dùng mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('scripts')
    <script>
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc2626'
            });
        @endif

        // Display validation errors if any
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Lỗi Validation!',
                html: `
                                                                                                <ul style="text-align: left;">
                                                                                                    @foreach($errors->all() as $error)
                                                                                                        <li>{{ $error }}</li>
                                                                                                    @endforeach
                                                                                                </ul>
                                                                                            `,
                confirmButtonColor: '#dc2626'
            });
        @endif

        $(document).ready(function () {
            // Simplified: all roles enabled as Unit scoping is removed
            $('.role-checkbox').prop('disabled', false);
            $('#roles-container').css({
                'background-color': 'transparent',
                'opacity': '1'
            });
            $('.role-label').css('cursor', 'pointer');
            $('#role-hint').hide();
        });
    </script>
@endpush

@section('content')
    <div class="page-header mb-4">
        <h1>Thêm người dùng mới</h1>
        <p>Tạo tài khoản người dùng mới trong hệ thống</p>
    </div>

    <style>
        .roles-container {
            border: 1px solid var(--border-color);
            padding: 15px;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.02);
        }
        body.dark-theme .roles-container {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .role-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 5px 0;
            cursor: pointer;
        }
        .role-item input { margin-top: 4px; flex-shrink: 0; }
        .role-item span { font-size: 14px; word-break: break-word; }
    </style>

    <div class="card p-4 mb-5">
        <form action="{{ route('nguoi-dung.tao') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Họ và tên --}}
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="Ten" class="form-control" placeholder="Nguyễn Văn A" required>
                    </div>
                </div>

                {{-- Tài khoản --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Tài khoản <span class="text-danger">*</span></label>
                        <input type="text" name="TaiKhoan" class="form-control" placeholder="Nhập tên tài khoản" required autofocus>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="Email" class="form-control" placeholder="example@triwin.vn">
                    </div>
                </div>

                {{-- Số điện thoại --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control" placeholder="0901234567">
                    </div>
                </div>

                {{-- Mật khẩu --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="TrangThai" class="form-select">
                            <option selected value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                </div>

                {{-- Phân quyền --}}
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label fw-bold">Phân quyền (Roles)</label>
                        <div class="roles-container">
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="role-item">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-check-input role-checkbox">
                                            <span>{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    Lưu người dùng
                </button>
                <a href="{{ route('nguoi-dung.danh-sach') }}" class="btn btn-secondary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
@endsection
