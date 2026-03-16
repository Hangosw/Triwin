@extends('layouts.app')

@section('title', 'Thông tin phòng ban - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>{{ $phongBan->Ten }}</h1>
        <p>Thông tin chi tiết phòng ban</p>
    </div>

    <div class="card">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
            <div class="form-group">
                <label class="form-label">Mã phòng ban</label>
                <div style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500;">
                    {{ $phongBan->Ma }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tên phòng ban</label>
                <div style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500;">
                    {{ $phongBan->Ten }}
                </div>
            </div>



            <div class="form-group">
                <label class="form-label">Số nhân viên</label>
                <div
                    style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500; color: #0BAA4B;">
                    {{ $phongBan->nhanViens->count() }} người
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('phong-ban.suaView', $phongBan->id) }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>

            <form id="delete-form-{{ $phongBan->id }}" action="{{ route('phong-ban.xoa', $phongBan->id) }}" method="POST">
                @csrf
                <button type="button" class="btn btn-danger"
                    onclick="confirmDelete('{{ $phongBan->id }}', '{{ $phongBan->Ten }}', {{ $phongBan->nhanViens->count() }})"
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; font-weight: 500; font-size: 14px; border-radius: 8px; transition: all 0.2s; white-space: nowrap; color: white; background-color: #ef4444; border: none; cursor: pointer;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa phòng ban
                </button>
            </form>

            <a href="{{ route('phong-ban.danh-sach') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Placeholder for future content -->
    <div class="card" style="margin-top: 24px;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Danh sách nhân viên</h3>
        <p style="color: #6b7280; text-align: center; padding: 48px;">Nội dung sẽ được bổ sung sau...</p>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id, name, employeeCount) {
            if (employeeCount > 0) {
                Swal.fire({
                    title: 'Không thể xóa',
                    text: `Phòng ban "${name}" hiện tại đang có ${employeeCount} nhân viên. Vui lòng chuyển nhân viên sang bộ phận khác trước khi xóa.`,
                    icon: 'error',
                    confirmButtonColor: '#0BAA4B',
                    confirmButtonText: 'Đã hiểu'
                });
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa phòng ban <b>${name}</b>?<br>Dữ liệu không thể khôi phục sau khi xóa.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6e7881',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
