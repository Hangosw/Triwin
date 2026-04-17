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
    <div class="page-header">
        <h1>Thêm người dùng mới</h1>
        <p>Tạo tài khoản người dùng mới trong hệ thống</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('nguoi-dung.tao') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Họ và tên <span style="color: red;">*</span></label>
                    <input type="text" name="Ten" class="form-control" placeholder="Nhập họ và tên (Ví dụ: Nguyễn Văn A)" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tài khoản <span style="color: red;">*</span></label>
                    <input type="text" name="TaiKhoan" class="form-control" placeholder="Nhập tài khoản" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" placeholder="Nhập địa chỉ email">
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu <span style="color: red;">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select name="TrangThai" class="form-control">
                        <option selected value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Phân quyền (Roles)</label>
                    <div id="roles-container"
                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; border: 1px solid #d1d5db; padding: 12px; border-radius: 8px; background-color: #f3f4f6; opacity: 0.6;">
                        @foreach($roles as $role)
                            <label style="display:flex; align-items:center; gap:8px; cursor: pointer;" class="role-label">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="role-checkbox">
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>



            </div>


            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Lưu người dùng
                </button>
                <a href="{{ route('nguoi-dung.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
@endsection
