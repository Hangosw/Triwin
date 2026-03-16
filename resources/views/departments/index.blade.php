@extends('layouts.app')

@section('title', 'Quản lý phòng ban - Triwin')

@section('content')
    <div class="page-header">
        <h1>Quản lý phòng ban</h1>
        <p>Danh sách các phòng ban trong công ty</p>
    </div>

    <div class="card">
        <div class="action-bar" style="margin-bottom: 24px; display: flex; justify-content: flex-end;">
            <a href="{{ route('phong-ban.taoView') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm phòng ban
            </a>
        </div>

        <div class="table-responsive">
            <table id="departmentsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Mã phòng ban</th>
                        <th>Tên phòng ban</th>
                        <th>Số nhân viên</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phongBans ?? [] as $index => $phongBan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="font-medium" style="color: #0BAA4B;">{{ $phongBan->Ma }}</span></td>
                            <td>{{ $phongBan->Ten }}</td>
                            <td>{{ $phongBan->nhanViens->count() ?? 0 }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('phong-ban.suaView', $phongBan->id) }}" class="btn-icon" title="Chỉnh sửa" style="color: #0BAA4B;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('phong-ban.xoa', $phongBan->id) }}" method="POST" style="display: inline;" class="delete-dept-form">
                                        @csrf
                                        <button type="button" class="btn-icon btn-delete-dept" title="Xóa" style="color: #ef4444; background: none; border: 1px solid #e2e8f0; cursor: pointer;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
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
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: #64748b;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
    }
    .btn-icon:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
    }
    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_length select {
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-left: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#departmentsTable').DataTable({
            language: {
                "sProcessing": "Đang xử lý...",
                "sLengthMenu": "Hiển thị _MENU_ dòng",
                "sZeroRecords": "Không tìm thấy dữ liệu",
                "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                "sSearch": "Tìm kiếm:",
                "oPaginate": {
                    "sFirst": "Đầu",
                    "sPrevious": "Trước",
                    "sNext": "Tiếp",
                    "sLast": "Cuối"
                }
            },
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [4] }
            ]
        });

        // Confirm delete with Swal.fire
        $(document).on('click', '.btn-delete-dept', function() {
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa phòng ban này? (Lưu ý: Chỉ xóa được khi phòng ban không có nhân viên)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
