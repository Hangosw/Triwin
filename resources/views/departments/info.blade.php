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

    <!-- Employee List Card -->
    <div class="card" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 600;">Danh sách nhân viên thuộc phòng</h3>
            <div class="search-bar" style="width: 300px;">
                <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên..." id="empSearch">
            </div>
        </div>
        
        <div class="table-container">
            <table id="employeeTable" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="50">STT</th>
                        <th>Mã NV</th>
                        <th>Họ tên</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phongBan->nhanViens as $index => $nv)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="font-medium" style="color: #0BAA4B;">{{ $nv->Ma }}</span></td>
                            <td>{{ $nv->Ten }}</td>
                            <td>{{ $nv->chucVu->Ten ?? 'Chưa cập nhật' }}</td>
                            <td>
                                @if(($nv->nguoiDung->TrangThai ?? 1) == 1)
                                    <span class="badge badge-success">Đang hoạt động</span>
                                @else
                                    <span class="badge badge-danger">Ngưng hoạt động</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }
    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const empTable = $('#employeeTable').DataTable({
                language: {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Hiển thị _MENU_ dòng",
                    "sZeroRecords": "Không tìm thấy nhân viên nào",
                    "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                dom: 'rtip',
            });

            $('#empSearch').on('keyup', function () {
                empTable.search(this.value).draw();
            });
        });

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
