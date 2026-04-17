@extends('layouts.app')

@section('title', 'Quản lý nhân viên - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Quản lý nhân viên</h1>
        <p>Danh sách tất cả nhân viên trong công ty</p>
    </div>

    <!-- Actions Bar -->
    <div class="card filter-bar-container">
        <div class="action-bar">
            <div class="filter-group">
                {{-- Giới tính --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Giới
                        tính</label>
                    <div class="dropdown custom-filter-dropdown" data-default="Tất cả giới tính">
                        <input type="hidden" name="gioi_tinh" id="filterGioiTinh" value="">
                        <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="dropdown-text" style="color: #6c757d;">Tất cả giới tính</span>
                            <span class="dropdown-icon">
                                <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                            </span>
                        </div>
                        <div class="dropdown-menu p-2 shadow"
                            style="min-width: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN GIỚI TÍNH</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                <button type="button" class="btn btn-sm btn-primary filter-btn fw-bold shadow-sm"
                                    data-val="" data-label="Tất cả giới tính"
                                    onclick="applyFilterAJAX('filterGioiTinh', this)"
                                    style="background-color: #3b82f6; color: #fff; text-align: left;">Tất cả giới
                                    tính</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="1" data-label="Nam"
                                    onclick="applyFilterAJAX('filterGioiTinh', this)" style="text-align: left;">Nam</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="0" data-label="Nữ"
                                    onclick="applyFilterAJAX('filterGioiTinh', this)" style="text-align: left;">Nữ</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Trạng
                        thái</label>
                    <div class="dropdown custom-filter-dropdown" data-default="Đang làm việc">
                        <input type="hidden" name="trang_thai" id="filterTrangThai" value="">
                        <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="dropdown-text" style="color: #212529;">Đang làm việc</span>
                            <span class="dropdown-icon">
                                <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                            </span>
                        </div>
                        <div class="dropdown-menu p-2 shadow"
                            style="min-width: 220px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN TRẠNG THÁI</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                <button type="button" class="btn btn-sm btn-primary filter-btn fw-bold shadow-sm"
                                    data-val="" data-label="Đang làm việc"
                                    onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="background-color: #3b82f6; color: #fff; text-align: left;">Đang làm việc</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="tat_ca"
                                    data-label="Tất cả trạng thái" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Tất cả trạng thái</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="dang_lam"
                                    data-label="Làm tại công ty" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Làm tại công ty</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="nghi_thai_san"
                                    data-label="Nghỉ thai sản" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Nghỉ thai sản</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="nghi_viec"
                                    data-label="Nghỉ làm" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Nghỉ làm</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nút xóa bộ lọc --}}
                <div class="form-group" style="margin-bottom: 0; display: none;" id="clearAllFiltersBtn">
                    <button type="button" class="btn-clear-filter" onclick="resetAllFiltersAJAX()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Xóa bộ lọc
                    </button>
                </div>
            </div>

            <div class="action-buttons">
                <button id="deleteSelected" class="btn btn-danger" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                @can('Thêm Nhân Viên')
                    <a href="{{ route('nhan-vien.taoView') }}" class="btn btn-primary d-flex align-items-center gap-2">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Thêm nhân viên
                    </a>
                @endcan
                @can('Thêm Nhân Viên')
                    <a href="{{ route('nhan-vien.importView') }}" class="btn btn-success d-flex align-items-center gap-2"
                        style="background-color: #10b981; border-color: #10b981; color: white;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import
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
                        <th style="width: 250px;">Thông tin nhân viên</th>
                        <th style="width: 220px;">Liên hệ</th>
                        <th style="width: 180px;">Công việc</th>

                        <th style="width: 150px;">Trạng thái</th>
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

            .employee-checkbox {
                cursor: pointer;
                width: 16px;
                height: 16px;
            }

            /* Filter Bar Styles */
            .filter-bar-container {
                margin-bottom: 24px;
            }

            .action-bar {
                padding: 16px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
            }

            .filter-group {
                display: flex;
                gap: 12px;
                align-items: flex-end;
                flex-wrap: wrap;
            }

            .btn-clear-filter {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background-color: #fef2f2;
                color: #dc2626;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.2s;
                text-decoration: none;
                border: 1px solid #fee2e2;
            }

            .btn-clear-filter:hover {
                background-color: #fee2e2;
                color: #b91c1c;
            }

            .btn-clear-filter svg {
                width: 16px;
                height: 16px;
            }

            .custom-filter-dropdown {
                min-width: 180px;
            }

            .custom-filter-dropdown .form-control {
                cursor: pointer;
                height: 38px;
                background-color: #fff;
                padding: 0.375rem 0.75rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: 1px solid #d1d5db;
                border-radius: 8px;
            }

            body.dark-theme .custom-filter-dropdown .form-control {
                background-color: #21263a !important;
                border-color: #2e3349 !important;
                color: #e8eaf0 !important;
            }

            body.dark-theme .btn-clear-filter {
                background-color: rgba(220, 38, 38, 0.1);
                color: #ef4444;
                border-color: rgba(220, 38, 38, 0.2);
            }

            body.dark-theme .btn-clear-filter:hover {
                background-color: rgba(220, 38, 38, 0.2);
                color: #f87171;
            }
        </style>

        <script>
            $(document).ready(function () {
                const table = $('#employeesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('nhan-vien.data') }}',
                        data: function (d) {
                            d.gioi_tinh = $('#filterGioiTinh').val();
                            d.trang_thai = $('#filterTrangThai').val();
                        }
                    },
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
                                const status = row.TrangThai ?? 'dang_lam';
                                if (status === 'dang_lam') {
                                    return '<span class="badge badge-success">Làm tại công ty</span>';
                                } else if (status === 'nghi_thai_san') {
                                    return '<span class="badge badge-info">Nghỉ thai sản</span>';
                                } else if (status === 'nghi_viec') {
                                    return '<span class="badge badge-secondary">Nghỉ làm</span>';
                                }
                                return '<span class="badge badge-secondary">' + status + '</span>';
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
                            title: 'Xác nhận cho nghỉ việc?',
                            text: `Bạn có chắc muốn chuyển trạng thái ${ids.length} nhân viên đã chọn sang Nghỉ việc?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Đồng ý',
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
                                            Swal.fire('Thành công!', data.message, 'success');
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
                            text: 'Hệ thống đang thu thập danh sách nhân viên cần chuyển trạng thái',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        let params = table.ajax.params();
                        params.length = -1;

                        $.ajax({
                            url: '{{ route('nhan-vien.data') }}',
                            data: params,
                            success: function (res) {
                                Swal.close();
                                const allIds = res.data.map(item => item.id);
                                executeDelete(allIds);
                            },
                            error: function () {
                                Swal.fire('Lỗi', 'Không thể lấy dữ liệu', 'error');
                            }
                        });
                    } else {
                        executeDelete(selectedIds);
                    }
                });

                // --- Filtering Logic ---

                // Function to apply UI changes when a filter is clicked
                window.applyFilterAJAX = function (inputId, btnEl) {
                    const val = btnEl.dataset.val;
                    const label = btnEl.dataset.label;
                    const dropdown = $(btnEl).closest('.custom-filter-dropdown');

                    // 1. Update Hidden Input & Trigger Change (so DataTables reloads)
                    const input = document.getElementById(inputId);
                    input.value = val;
                    table.ajax.reload();

                    // 2. Update toggle text
                    const textSpan = dropdown.find('.dropdown-text');
                    textSpan.text(label);

                    // 3. Update toggle icon & text color
                    const iconSpan = dropdown.find('.dropdown-icon');
                    const isDefault = (val === '');

                    const isDark = $('body').hasClass('dark-theme');
                    const activeColor = isDark ? '#e8eaf0' : '#212529';
                    const mutedColor = isDark ? '#8b93a8' : '#6c757d';

                    if (isDefault) {
                        textSpan.css('color', mutedColor);
                        iconSpan.html('<i class="bi bi-chevron-down ms-2" style="font-size: 14px; color: ' + mutedColor + ';"></i>');
                    } else {
                        textSpan.css('color', activeColor);
                        const closeIconColor = isDark ? '#f87171' : '#dc2626';
                        iconSpan.html('<i class="bi bi-x-circle-fill ms-2" style="font-size: 12px; padding: 4px; border-radius: 50%; color: ' + mutedColor + ';" onclick="event.stopPropagation(); resetFilterAJAX(\'' + inputId + '\');" onmouseover="this.style.color=\'' + closeIconColor + '\'" onmouseout="this.style.color=\'' + mutedColor + '\'"></i>');
                    }

                    // 4. Update internal button styles
                    dropdown.find('.filter-btn').each(function () {
                        const b = $(this);
                        const bVal = b.data('val');
                        b.removeClass('btn-primary fw-bold shadow-sm').addClass('btn-light').css({
                            'background-color': isDark ? '#2e3349' : '#f9fafb',
                            'color': isDark ? '#c3c8da' : '#374151'
                        });

                        // Highlight choice
                        if (String(bVal) === String(val)) {
                            b.removeClass('btn-light').addClass('btn-primary fw-bold shadow-sm').css({
                                'background-color': '#3b82f6',
                                'color': '#fff'
                            });
                        }
                    });

                    checkClearAllBtn();
                };

                // Function to clear a specific filter
                window.resetFilterAJAX = function (inputId) {
                    const dropdown = $('#' + inputId).closest('.custom-filter-dropdown');
                    const defaultBtn = dropdown.find(`.filter-btn[data-val=""]`);
                    if (defaultBtn.length) {
                        applyFilterAJAX(inputId, defaultBtn[0]);
                    }
                };

                // Function to clear ALL filters
                window.resetAllFiltersAJAX = function () {
                    resetFilterAJAX('filterGioiTinh');
                    resetFilterAJAX('filterTrangThai');
                };

                // Function to show/hide the Clear All button depending on filter state
                function checkClearAllBtn() {
                    const valGioiTinh = $('#filterGioiTinh').val();
                    const valTrangThai = $('#filterTrangThai').val();
                    if (valGioiTinh !== '' || valTrangThai !== '') {
                        $('#clearAllFiltersBtn').show();
                    } else {
                        $('#clearAllFiltersBtn').hide();
                    }
                }
            });
        </script>
    @endpush
@endsection