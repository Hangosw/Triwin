@extends('layouts.app')



@section('title', 'Thêm người dùng mới - Vietnam Rubber Group')

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
            const unitSpecs = $('input[name="don_vis[]"]');
            const roleSpecs = $('.role-checkbox');
            const rolesContainer = $('#roles-container');
            const roleHint = $('#role-hint');
            const roleLabels = $('.role-label');

            function updateRolesState() {
                const anyUnitSelected = $('input[name="don_vis[]"]:checked').length > 0;

                if (anyUnitSelected) {
                    roleSpecs.prop('disabled', false);
                    rolesContainer.css({
                        'background-color': 'transparent',
                        'opacity': '1'
                    });
                    roleLabels.css('cursor', 'pointer');
                    roleHint.hide();
                } else {
                    roleSpecs.prop('disabled', true).prop('checked', false);
                    rolesContainer.css({
                        'background-color': '#f3f4f6',
                        'opacity': '0.6'
                    });
                    roleLabels.css('cursor', 'not-allowed');
                    roleHint.show();
                }
            }

            unitSpecs.on('change', updateRolesState);

            // Initial check
            updateRolesState();
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
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="Ten" class="form-control" placeholder="Nhập họ và tên (Ví dụ: Nguyễn Văn A)">
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
                    <label class="form-label">Phân quyền (Roles) <span id="role-hint"
                            style="font-size: 11px; color: #dc2626; font-weight: normal; margin-left: 8px;">(Vui lòng chọn
                            Đơn vị quản lý trước)</span></label>
                    <div id="roles-container"
                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; border: 1px solid #d1d5db; padding: 12px; border-radius: 8px; background-color: #f3f4f6; opacity: 0.6;">
                        @foreach($roles as $role)
                            <label style="display:flex; align-items:center; gap:8px; cursor: not-allowed;" class="role-label">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="role-checkbox" disabled>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Đơn vị quản lý (Bắt buộc khi có Phân quyền)</label>
                    <p style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Nếu gán quyền cho người dùng, bạn bắt
                        buộc phải chọn ít nhất một đơn vị mà họ được phép quản lý/thao tác.</p>
                    <div
                        style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; border: 1px solid #d1d5db; padding: 16px; border-radius: 8px; background: #f9fafb;">
                        @foreach($donVis as $dv)
                            <label
                                style="display:flex; align-items:center; gap:8px; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.2s;"
                                onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="don_vis[]" value="{{ $dv->id }}">
                                <span style="font-size: 13px;">{{ $dv->Ten }} ({{ $dv->Ma }})</span>
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