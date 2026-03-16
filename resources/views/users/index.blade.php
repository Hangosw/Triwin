@extends('layouts.app')

@push('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0BAA4B'
            });
        @endif
    </script>
@endpush

@section('title', 'Quản lý người dùng - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý người dùng</h1>
        <p>Danh sách tất cả người dùng trong hệ thống</p>
    </div>

    <!-- Actions Bar -->
    <div class="card">
        <div class="action-bar">
            <div class="search-bar">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, email..." id="customSearch">
            </div>
            <div class="action-buttons">
                <button id="btnDeleteSelected" class="btn btn-danger"
                    style="display: none; background-color: #dc2626; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                <a href="{{ route('nguoi-dung.tao') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm người dùng
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th class="stt-checkbox-col">
                            <span class="stt-text">STT</span>
                            <input type="checkbox" id="selectAll" class="dt-checkboxes">
                        </th>
                        <th>Họ tên</th>
                        <th>Tài khoản</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#usersTable').DataTable({
                processing: true,
                serverSide: false, // Client-side logic as per NguoiDungController@DataNguoiDung
                ajax: "{{ route('nguoi-dung.data') }}",
                columns: [
                    {
                        data: null,
                        className: 'stt-checkbox-col',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return `
                                                <span class="stt-text">${meta.row + 1}</span>
                                                <input type="checkbox" class="user-checkbox dt-checkboxes" value="${row.id}">
                                            `;
                        }
                    },
                    { data: 'Ten', render: function (data) { return data || '<span class="text-muted">Chưa cập nhật</span>'; } },
                    { data: 'TaiKhoan' },
                    { data: 'Email' },
                    { data: 'SoDienThoai', render: function (data) { return data || '--'; } },
                    {
                        data: 'TrangThai',
                        render: function (data) {
                            if (data == 1) {
                                return '<span class="badge badge-success">Đang hoạt động</span>';
                            }
                            return '<span class="badge badge-gray">Ngưng hoạt động</span>';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function (data) {
                            return `
                                                <div style="display: flex; gap: 8px;">
                                                    <a href="/nguoi-dung/sua/${data}" class="btn-icon text-primary" title="Sửa">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                    <button type="button" class="btn-icon text-danger btn-delete" data-id="${data}" title="Xóa" style="background: none; border: none; cursor: pointer; color: #dc2626;">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            `;
                        }
                    }
                ],
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
                dom: 'rtip', // Hide default search box
            });

            // Custom Search
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Select All Logic
            $('#selectAll').on('change', function () {
                $('.user-checkbox').prop('checked', this.checked);
                updateDeleteButton();
            });

            $(document).on('change', '.user-checkbox', function () {
                const allChecked = $('.user-checkbox:checked').length === $('.user-checkbox').length;
                $('#selectAll').prop('checked', allChecked);
                updateDeleteButton();
            });

            function updateDeleteButton() {
                const selectedCount = $('.user-checkbox:checked').length;
                if (selectedCount > 0) {
                    $('#btnDeleteSelected').show();
                    $('#selectedCount').text(selectedCount);
                } else {
                    $('#btnDeleteSelected').hide();
                }
            }

            // Single Delete
            $(document).on('click', '.btn-delete', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Dữ liệu sẽ không thể khôi phục!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/nguoi-dung/xoa/${id}`, {
                            _token: '{{ csrf_token() }}'
                        }, function (res) {
                            if (res.success) {
                                table.ajax.reload();
                                Swal.fire('Đã xóa!', res.message, 'success');
                            }
                        });
                    }
                });
            });

            // Bulk Delete
            $('#btnDeleteSelected').on('click', function () {
                const selectedIds = [];
                $('.user-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                Swal.fire({
                    title: 'Xóa các mục đã chọn?',
                    text: `Bạn đang chọn xóa ${selectedIds.length} người dùng.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Xóa ngay',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('nguoi-dung.xoa-nhieu') }}", {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        }, function (res) {
                            if (res.success) {
                                table.ajax.reload();
                                $('#selectAll').prop('checked', false);
                                updateDeleteButton();
                                Swal.fire('Thành công!', res.message, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
