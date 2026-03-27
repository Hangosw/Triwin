@extends('layouts.app')

@section('title', 'Quản lý nhân viên - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý nhân viên</h1>
        <p>Danh sách tất cả nhân viên trong công ty</p>
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
                @can('Thêm Nhân Viên')
                    <a href="{{ route('nhan-vien.taoView') }}" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Thêm nhân viên
                    </a>
                @endcan
                @can('Thêm Nhân Viên')
                    <a href="{{ route('nhan-vien.importView') }}" class="btn btn-success"
                        style="background-color: #10b981; border-color: #10b981; color: white;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import nhân viên
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="employeesTable">
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
                        <th>Thông tin nhân viên</th>
                        <th>Liên hệ</th>
                        <th>Công việc</th>
                        <th>Loại NV</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <style>
            .employee-name-link {
                color: #0BAA4B;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s;
            }

            .employee-name-link:hover {
                color: #088c3d;
                text-decoration: underline;
            }

            .table tbody tr {
                cursor: pointer;
            }

            .table tbody tr:hover {
                background-color: #f9fafb;
            }

            /* Checkbox styling */
            .employee-checkbox {
                cursor: pointer;
                width: 16px;
                height: 16px;
            }
        </style>

        <script>
            $(document).ready(function () {
                const table = $('#employeesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('nhan-vien.data') }}',
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row, meta) {
                                return `
                                                                                                                                    <div style="text-align: center;">
                                                                                                                                        <div><strong>${meta.row + meta.settings._iDisplayStart + 1}</strong></div>
                                                                                                                                        <div style="margin-top: 4px;">
                                                                                                                                            <input type="checkbox" class="employee-checkbox" value="${row.id}">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `;
                            }
                        },
                        {
                            data: 'Ten',
                            render: function (data, type, row) {
                                const avatar = row.AnhDaiDien ? `/${row.AnhDaiDien}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(data)}&background=0F5132&color=fff&size=128`;
                                const ngaySinh = new Date(row.NgaySinh).toLocaleDateString('vi-VN');
                                const gioiTinh = row.GioiTinh == 1 ? 'Nam' : 'Nữ';

                                return `
                                                                                                                                    <div style="display: flex; align-items: center; gap: 16px;">
                                                                                                                                        <img src="${avatar}" alt="${data}" class="avatar" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                                                                                                                        <div>
                                                                                                                                            <a href="/nhan-vien/info/${row.id}" class="employee-name-link">${data}</a>
                                                                                                                                            <div class="text-gray" style="font-size: 14px; margin-top: 4px;">Ngày sinh: ${ngaySinh}</div>
                                                                                                                                            <div class="text-gray" style="font-size: 14px;">Giới tính: ${gioiTinh}</div>
                                                                                                                                            <div style="font-size: 14px; color: #9ca3af; margin-top: 2px;">CCCD: ${row.SoCCCD || 'N/A'}</div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                const phone = row.SoDienThoai || 'Chưa cập nhật';
                                const email = row.Email || 'Chưa cập nhật';
                                const address = row.DiaChi || 'Chưa cập nhật';

                                return `
                                                                                                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                                                                                                <div style="font-size: 14px;"><i class="bi bi-telephone-fill" style="color: #0BAA4B;"></i> ${phone}</div>
                                                                                                                                <div style="font-size: 14px;"><i class="bi bi-envelope-fill" style="color: #0BAA4B;"></i> ${email}</div>
                                                                                                                                <div style="font-size: 14px; color: #6b7280;"><i class="bi bi-house-fill" style="color: #6b7280;"></i> ${address}</div>
                                                                                                                            </div>
                                                                                                                        `;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                const phongBan = row.tt_cong_viec?.phong_ban?.Ten || 'Chưa phân công';
                                const chucVu = row.tt_cong_viec?.chuc_vu?.Ten || 'Chưa có';

                                return `
                                                                                                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                                                                                                <div class="font-medium" style="font-size: 14px;">${phongBan}</div>
                                                                                                                                <div class="text-gray" style="font-size: 14px;">${chucVu}</div>
                                                                                                                            </div>
                                                                                                                        `;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                const loaiNV = row.tt_cong_viec?.LoaiNhanVien;
                                if (loaiNV === 1) {
                                    return '<span class="badge badge-info">Văn phòng</span>';
                                } else {
                                    return '<span class="badge badge-warning">Công nhân</span>';
                                }
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                // Since we don't have nguoiDung relationship, show all as active
                                return '<span class="badge badge-success">Đang làm việc</span>';
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
                    pageLength: 10,
                    order: [[1, 'asc']],
                    drawCallback: function () {
                        // Make rows clickable
                        $('#employeesTable tbody tr').off('click').on('click', function (e) {
                            // Don't navigate if clicking checkbox
                            if ($(e.target).hasClass('employee-checkbox') || $(e.target).is('a')) {
                                return;
                            }
                            const data = table.row(this).data();
                            window.location.href = `/nhan-vien/info/${data.id}`;
                        });

                        updateSelectedCount();
                    }
                });

                let isSelectAllPages = false;

                // Select all checkbox
                $('#selectAll').on('click', function () {
                    const isChecked = $(this).prop('checked');
                    $('.employee-checkbox').prop('checked', isChecked);
                    isSelectAllPages = isChecked;
                    
                    if (isChecked) {
                        $('#selectedCount').text('Tất cả bản ghi đang lọc');
                        $('#deleteSelected').show();
                    } else {
                        updateSelectedCount();
                    }
                });

                // Individual checkbox
                $(document).on('change', '.employee-checkbox', function () {
                    if (!$(this).prop('checked')) {
                        isSelectAllPages = false;
                        $('#selectAll').prop('checked', false);
                    }
                    updateSelectedCount();
                });

                // Update selected count
                function updateSelectedCount() {
                    const count = $('.employee-checkbox:checked').length;
                    if (!isSelectAllPages) {
                        $('#selectedCount').text(count);
                        $('#deleteSelected').toggle(count > 0);
                    }
                }

                // Delete selected
                $('#deleteSelected').on('click', function () {
                    const selectedIds = $('.employee-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();

                    const executeDelete = (ids) => {
                        if (ids.length === 0) return;

                        Swal.fire({
                            title: 'Xác nhận xóa?',
                            text: `Bạn có chắc muốn xóa ${ids.length} nhân viên đã chọn? Hành động này sẽ áp dụng cho tất cả dữ liệu được chọn trên mọi trang.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Call delete API
                                fetch('{{ route('nhan-vien.xoa-nhieu') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    body: JSON.stringify({ ids: ids })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Đã xóa!', data.message, 'success');
                                        table.ajax.reload();
                                        $('#selectAll').prop('checked', false);
                                        isSelectAllPages = false;
                                        $('#deleteSelected').hide();
                                    } else {
                                        Swal.fire('Lỗi!', data.message, 'error');
                                    }
                                });
                            }
                        });
                    };

                    if (isSelectAllPages) {
                        Swal.fire({
                            title: 'Đang tải dữ liệu...',
                            text: 'Hệ thống đang thu thập danh sách nhân viên cần xóa',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        
                        let params = table.ajax.params();
                        params.length = -1;
                        
                        $.ajax({
                            url: '{{ route('nhan-vien.data') }}',
                            data: params,
                            success: function(res) {
                                Swal.close();
                                const allIds = res.data.map(item => item.id);
                                executeDelete(allIds);
                            },
                            error: function() {
                                Swal.fire('Lỗi', 'Không thể lấy dữ liệu', 'error');
                            }
                        });
                    } else {
                        executeDelete(selectedIds);
                    }
                });
            });
        </script>
    @endpush
@endsection
