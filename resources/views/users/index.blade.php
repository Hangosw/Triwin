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

@push('styles')
    <style>
        .user-name-link {
            font-weight: 500;
            color: #0BAA4B;
            text-decoration: none;
        }
        .user-name-link:hover {
            text-decoration: underline;
            color: #09933f;
        }
        .text-not-updated {
            color: #9ca3af;
            font-style: italic;
            font-size: 13px;
        }
        body.dark-theme .text-not-updated {
            color: #8b93a8;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý người dùng</h1>
        <p>Danh sách tất cả người dùng trong hệ thống</p>
    </div>

    <!-- Actions Bar -->
    <div class="card">
        <div class="action-bar">
            <div style="display: flex; gap: 16px; align-items: center; flex: 1;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, email..." id="customSearch">
                </div>
                <div id="lengthMenuContainer" class="no-select2-parent">
                    <!-- DataTables length menu will be moved here -->
                </div>
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
{{-- 
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
--}}
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
                        <th style="width: 60px;">
                            <div style="text-align: center;">
                                <div><strong>STT</strong></div>
                                <div style="margin-top: 4px;">
                                    <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                </div>
                            </div>
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
            let selectedIds = [];

            const table = $('#usersTable').DataTable({
                processing: true,
                serverSide: false, // Client-side logic as per NguoiDungController@DataNguoiDung
                ajax: "{{ route('nguoi-dung.data') }}",
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return `
                                <div style="text-align: center;">
                                    <div><strong class="stt-value"></strong></div>
                                    <div style="margin-top: 4px;">
                                        <input type="checkbox" class="user-checkbox" value="${row.id}" style="cursor: pointer;" ${selectedIds.includes(row.id) ? 'checked' : ''}>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'Ten',
                        render: function (data, type, row) {
                            if (!data) return '<span class="text-not-updated">Chưa cập nhật</span>';
                            return `<a href="/nguoi-dung/sua/${row.id}" class="user-name-link">${data}</a>`;
                        }
                    },
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
                        data: null,
                        orderable: false,
                        render: function (data, type, row) {
                            const currentUserId = {{ \Illuminate\Support\Facades\Auth::id() }};
                            const isSelf = row.id == currentUserId;

                            const statusIcon = row.TrangThai == 1 
                                ? `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>` 
                                : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>`;
                            
                            const statusTitle = row.TrangThai == 1 ? 'Khóa người dùng' : 'Mở khóa người dùng';
                            const statusClass = row.TrangThai == 1 ? 'text-warning' : 'text-success';

                            return `
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    <button type="button" class="btn-icon ${statusClass} btn-toggle-status" data-id="${row.id}" title="${statusTitle}" style="background: none; border: none; cursor: pointer;">
                                        ${statusIcon}
                                    </button>
                                    ${!isSelf ? `
                                    <button type="button" class="btn-icon text-danger btn-delete" data-id="${row.id}" title="Xóa" style="background: none; border: none; cursor: pointer; color: #dc2626;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    ` : `
                                    <span class="badge badge-info" style="font-size: 11px;">Chính bạn</span>
                                    `}
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
                responsive: true,
                autoWidth: false,
                order: [], // Respect server order (latest ID)
                dom: '<"top"l>rtip', // Enable length menu
            });

            // Move length menu to custom container
            $('.dataTables_length').detach().appendTo('#lengthMenuContainer');

            // Keep STT sequential regardless of sorting
            table.on('order.dt search.dt', function () {
                let i = 1;
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, index) {
                    $(cell).find('.stt-value').html(index + 1);
                });
            }).draw();

            // Custom Search
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Select All Logic
            $('#selectAll').on('change', function () {
                const isChecked = this.checked;
                if (isChecked) {
                    // Add all IDs from the entire dataset
                    selectedIds = table.rows().data().toArray().map(row => row.id);
                } else {
                    selectedIds = [];
                }
                
                // Update checkboxes in current view
                $('.user-checkbox').prop('checked', isChecked);
                updateDeleteButton();
            });

            $(document).on('change', '.user-checkbox', function () {
                const id = parseInt($(this).val());
                if (this.checked) {
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(itemId => itemId !== id);
                }
                
                const allData = table.rows().data().toArray();
                const allChecked = selectedIds.length === allData.length && allData.length > 0;
                $('#selectAll').prop('checked', allChecked);
                updateDeleteButton();
            });

            function updateDeleteButton() {
                const selectedCount = selectedIds.length;
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
                            } else {
                                Swal.fire('Lỗi!', res.message, 'error');
                            }
                        }).fail(function(xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra khi xóa người dùng.';
                            Swal.fire('Thất bại!', msg, 'error');
                        });
                    }
                });
            });

            // Bulk Delete
            $('#btnDeleteSelected').on('click', function () {
                if (selectedIds.length === 0) return;

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
                                selectedIds = [];
                                $('#selectAll').prop('checked', false);
                                updateDeleteButton();
                                Swal.fire('Thành công!', res.message, 'success');
                            } else {
                                Swal.fire('Lỗi!', res.message, 'error');
                            }
                        }).fail(function(xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra khi xóa danh sách người dùng.';
                            Swal.fire('Thất bại!', msg, 'error');
                        });
                    }
                });
            });

            // Toggle Status
            $(document).on('click', '.btn-toggle-status', function () {
                const id = $(this).data('id');
                const btn = $(this);
                
                $.post(`/nguoi-dung/toggle-status/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function (res) {
                    if (res.success) {
                        table.ajax.reload(null, false); // Reload without resetting paging
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    }
                });
            });
        });
    </script>
@endpush
