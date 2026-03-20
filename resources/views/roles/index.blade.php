@extends('layouts.app')

@section('title', 'Quản lý Role - HRM')

@section('content')
    <div class="page-header">
        <h1>Quản lý Nhóm quyền (Roles)</h1>
        <p>Liệt kê và thiết lập chức năng cho từng Role</p>
    </div>

    <div class="card">
        <div class="action-bar" style="margin-bottom: 20px;">
            <div></div>
            <div class="action-buttons">
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    + Thêm Role
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success"
                style="color: #0BAA4B; background-color: #d1fae5; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"
                style="color: #991b1b; background-color: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-container">
            <table id="rolesTable" class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tên Role</th>
                        <th>Số lượng User</th>
                        <th style="text-align: right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                <strong style="color: #0BAA4B;">{{ $r->name }}</strong>
                            </td>
                            <td>{{ $r->users_count }} users</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('roles.edit', $r->id) }}" class="btn-icon" title="Sửa Role"
                                        style="color:#0BAA4B;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    @if($r->name != 'System Admin')
                                        <form action="{{ route('roles.destroy', $r->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa Role này?');"
                                            style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Xóa Role" style="color:#dc2626;">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('#rolesTable').DataTable({
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
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [3] }
            ]
        });
    });
</script>
@endpush
@endsection
