@extends('layouts.app')

@section('title', 'Quản lý Tổ Đội - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý Tổ Đội</h1>
        <p>Danh sách tất cả tổ/đội làm việc trong các phòng ban</p>
    </div>

    <!-- Actions Bar -->
    <div class="card">
        <div class="action-bar">
            <div class="action-buttons">
                <button id="deleteSelected" class="btn btn-danger" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Lọc
                </button>
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                <a href="{{ route('to-doi.create') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm mới Tổ Đội
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: @json(session('success')),
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#0BAA4B'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi xảy ra!',
                    text: @json(session('error')),
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#dc2626'
                });
            });
        </script>
    @endif

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="todoi-table">
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
                        <th>Mã Tổ Đội</th>
                        <th>Tên Tổ Đội</th>
                        <th>Thuộc Phòng Ban</th>
                        <th>Tổ Trưởng Hiện Tại</th>
                        <th>Ghi Chú</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .todoi-name-link {
            color: #0BAA4B;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .todoi-name-link:hover {
            color: #088c3d;
            text-decoration: underline;
        }

        .table tbody tr {
            cursor: pointer;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Styling for the TruongTo cell */
        .truong-to-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background-color: #f0fdf4;
            color: #0BAA4B;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            font-weight: 500;
            font-size: 13px;
            gap: 6px;
        }

        .truong-to-badge i {
            font-size: 12px;
        }

        /* Checkbox styling */
        .todoi-checkbox {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#todoi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('to-doi.data') }}",
                    type: "GET",
                    error: function (xhr, error, thrown) {
                        console.error("DataTables Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi tải dữ liệu',
                            text: 'Không thể tải dữ liệu tổ đội. Vui lòng thử lại sau.'
                        });
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function (data, type, row, meta) {
                            return `
                                    <div style="text-align: center;">
                                        <div><strong>${meta.row + meta.settings._iDisplayStart + 1}</strong></div>
                                        <div style="margin-top: 4px;">
                                            <input type="checkbox" class="todoi-checkbox" value="${row.id}">
                                        </div>
                                    </div>
                                `;
                        }
                    },
                    { data: 'Ma', name: 'Ma' },
                    {
                        data: 'Ten',
                        name: 'Ten',
                        render: function (data, type, row) {
                            return `<a href="#" class="todoi-name-link" style="font-size: 15px;"><strong>${data}</strong></a>`;
                        }
                    },
                    {
                        data: 'PhongBan',
                        name: 'PhongBanId',
                        render: function (data, type, row) {
                            return data ? `<span style="font-weight: 500;">${data}</span>` : '<span class="text-muted italic">Chưa xác định</span>';
                        }
                    },
                    {
                        data: 'TruongTo',
                        name: 'TruongTo',
                        orderable: false, // We can't sort by TruongTo easily as it's a computed property/relationship
                        searchable: false,
                        render: function (data, type, row) {
                            if (data && data.includes('<span')) {
                                return data; // Already formatted as italic "Chưa xác định"
                            }
                            return `
                                                    <div class="truong-to-badge">
                                                        <i class="bi bi-person-badge"></i>
                                                        ${data}
                                                    </div>
                                                `;
                        }
                    },
                    { data: 'GhiChu', name: 'GhiChu' }
                ],
                order: [[1, 'asc']], // Sort by Ma asc by default
                language: {
                    processing: "<div class='spinner-border text-success' role='status'><span class='visually-hidden'>Loading...</span></div>",
                    lengthMenu: "Hiển thị _MENU_ dòng",
                    zeroRecords: "Không tìm thấy dữ liệu",
                    info: "Hiển thị _START_ đến _END_ trên tổng số _TOTAL_ dòng",
                    infoEmpty: "Đang hiển thị 0 đến 0 của 0 dòng",
                    infoFiltered: "(Lọc từ _MAX_ dòng)",
                    search: "Tìm kiếm:",
                    paginate: {
                        first: "Đầu",
                        previous: "Trước",
                        next: "Tiếp",
                        last: "Cuối"
                    },
                    emptyTable: "Chưa có dữ liệu Tổ Đội trong hệ thống"
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"top"lf>rt<"bottom"ip><"clear">', // Custom positioning
                drawCallback: function () {
                    // Make rows clickable
                    $('#todoi-table tbody tr').off('click').on('click', function (e) {
                        // Don't navigate if clicking checkbox or action button
                        if ($(e.target).hasClass('todoi-checkbox') || $(e.target).is('a') || $(e.target).closest('button').length) {
                            return;
                        }
                        const data = table.row(this).data();
                        // Example navigation (change based on your real routes)
                        // window.location.href = `/to-doi/info/${data.id}`;
                    });

                    updateSelectedCount();
                }
            });

            // Make the wrapper width 100%
            $('.dataTables_wrapper').css('width', '100%');

            // Select all checkbox
            $('#selectAll').on('click', function () {
                const isChecked = $(this).prop('checked');
                $('.todoi-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Individual checkbox
            $(document).on('change', '.todoi-checkbox', function () {
                updateSelectedCount();

                // Update select all checkbox
                const totalCheckboxes = $('.todoi-checkbox').length;
                const checkedCheckboxes = $('.todoi-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            });

            // Update selected count
            function updateSelectedCount() {
                const count = $('.todoi-checkbox:checked').length;
                $('#selectedCount').text(count);
                $('#deleteSelected').toggle(count > 0);
            }

            // Delete selected placeholder
            $('#deleteSelected').on('click', function () {
                const selectedIds = $('.todoi-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: `Bạn có chắc muốn xóa ${selectedIds.length} tổ đội đã chọn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        alert("Chức năng xóa nhiều sẽ cập nhật sau!");
                    }
                });
            });
        });
    </script>
@endpush
