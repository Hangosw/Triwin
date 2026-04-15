@extends('layouts.app')

@section('title', 'Quản lý hợp đồng - Vietnam Rubber Group')

@push('styles')
    <style>
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 30px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6b7280;
            margin: 0;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .action-bar {
            padding: 20px 24px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .table-container {
            padding: 24px;
        }

        .table {
            width: 100%;
            margin: 0;
        }

        .table thead th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 2px solid #e5e7eb;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #1f2937;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        .date-range {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .date-label {
            font-size: 12px;
            color: #6b7280;
        }

        .date-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            gap: 6px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .salary-amount {
            font-weight: 600;
            color: #0BAA4B;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin: 0 8px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px !important;
            margin: 0 2px;
            border-radius: 6px !important;
            border: 1px solid #d1d5db !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0BAA4B !important;
            color: white !important;
            border-color: #0BAA4B !important;
        }

        /* Dark Theme Overrides */
        body.dark-theme .page-header h1,
        body.dark-theme .page-header p {
            color: #e8eaf0;
        }

        body.dark-theme .table tbody td {
            color: #e8eaf0;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .table tbody tr:hover {
            background-color: #21263a;
        }

        body.dark-theme .date-label {
            color: #8b93a8;
        }

        body.dark-theme .date-value {
            color: #e8eaf0;
        }

        body.dark-theme .action-bar {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .dataTables_wrapper .dataTables_length select,
        body.dark-theme .dataTables_wrapper .dataTables_filter input,
        body.dark-theme .form-control {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .table thead th {
            background-color: #21263a !important;
            color: #c3c8da !important;
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .dataTables_wrapper .dataTables_info {
            color: #8b93a8 !important;
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
@endpush

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Quản lý hợp đồng</h1>
                <p>Danh sách hợp đồng lao động</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card filter-bar-container">
        <div class="action-bar">
            <div class="filter-group">
                {{-- Loại hợp đồng --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Loại hợp đồng</label>
                    <div class="dropdown custom-filter-dropdown" data-default="Tất cả loại">
                        <input type="hidden" name="loai" id="filterLoai" value="">
                        <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="dropdown-text" style="color: #6c757d;">Tất cả loại</span>
                            <span class="dropdown-icon">
                                <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                            </span>
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 220px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN LOẠI HỢP ĐỒNG</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                <button type="button" class="btn btn-sm btn-primary filter-btn fw-bold shadow-sm" data-val="" data-label="Tất cả loại" onclick="applyFilterAJAX('filterLoai', this)" style="background-color: #3b82f6; color: #fff; text-align: left;">Tất cả loại</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="TV" data-label="Thử việc" onclick="applyFilterAJAX('filterLoai', this)" style="text-align: left;">Thử việc</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="XN" data-label="Xác định thời hạn" onclick="applyFilterAJAX('filterLoai', this)" style="text-align: left;">Xác định thời hạn</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="KXDH" data-label="Không xác định thời hạn" onclick="applyFilterAJAX('filterLoai', this)" style="text-align: left;">Không xác định thời hạn</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="KHOAN" data-label="Khoán việc" onclick="applyFilterAJAX('filterLoai', this)" style="text-align: left;">Khoán việc</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="PT" data-label="Part-Time" onclick="applyFilterAJAX('filterLoai', this)" style="text-align: left;">Part-Time</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Trạng thái</label>
                    <div class="dropdown custom-filter-dropdown" data-default="Tất cả trạng thái">
                        <input type="hidden" name="trang_thai" id="filterTrangThai" value="1">
                        <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="dropdown-text" style="color: #6c757d;">Có hiệu lực</span>
                            <span class="dropdown-icon">
                                <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                            </span>
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN TRẠNG THÁI</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="" data-label="Tất cả trạng thái" onclick="applyFilterAJAX('filterTrangThai', this)" style="text-align: left;">Tất cả trạng thái</button>
                                <button type="button" class="btn btn-sm btn-primary filter-btn fw-bold shadow-sm" data-val="1" data-label="Có hiệu lực" onclick="applyFilterAJAX('filterTrangThai', this)" style="background-color: #3b82f6; color: #fff; text-align: left;">Có hiệu lực</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="0" data-label="Hết hiệu lực" onclick="applyFilterAJAX('filterTrangThai', this)" style="text-align: left;">Hết hiệu lực</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="2" data-label="Bị hủy/Thanh lý" onclick="applyFilterAJAX('filterTrangThai', this)" style="text-align: left;">Bị hủy/Thanh lý</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nút xóa bộ lọc --}}
                <div class="form-group" style="margin-bottom: 0; display: none;" id="clearAllFiltersBtn">
                    <button type="button" class="btn-clear-filter" onclick="resetAllFiltersAJAX()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Xóa bộ lọc
                    </button>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                    <i class="bi bi-trash me-2"></i>Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('hop-dong.taoView') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm hợp đồng</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="table-container">
            <table class="table" id="contractsTable">
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
                        <th>Loại hợp đồng</th>
                        <th>Thời hạn</th>
                        <th>Mức lương</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#contractsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('hop-dong.data') }}',
                    data: function (d) {
                        d.loai = $('#filterLoai').val();
                        d.trang_thai = $('#filterTrangThai').val();
                        d.expiring_soon = new URLSearchParams(window.location.search).get('expiring_soon');
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
                                                <input type="checkbox" class="contract-checkbox" value="${row.id}" style="cursor: pointer; width: 16px; height: 16px;">
                                            </div>
                                        </div>
                                    `;
                        }
                    },
                    {
                        data: null, // Change to null to access the whole row
                        render: function (data, type, row) {
                            const name = row.nhan_vien?.Ten || row.TenNhanVien || 'N/A';
                            const no = row.SoHopDong || 'N/A';
                            const avatar = row.nhan_vien?.AnhDaiDien ? `/${row.nhan_vien.AnhDaiDien}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(name.split(' - ')[0])}&background=0F5132&color=fff&size=128`;
                            return `
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <img src="${avatar}" alt="${name}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <div>
                                                <div style="font-weight: 500;">${name}</div>
                                                <div style="font-size: 12px;" class="date-label">Số HĐ: ${no}</div>
                                            </div>
                                        </div>
                                    `;
                        }
                    },
                    {
                        data: 'Loai',
                        render: function (data, type, row) {
                            if (!data && !row.loai_hop_dong) return 'N/A';

                            // 1. Determine Label (Relationship Name > Mapper > Raw Data)
                            let label = row.loai_hop_dong?.TenLoai || data;
                            let badgeClass = 'badge-info';

                            // 2. Normalize based on Code (Handle legacy 'thu_viec' and modern 'TV')
                            const code = (data || '').toLowerCase();

                            if (code === 'thu_viec' || code === 'tv') {
                                label = 'Thử việc';
                                badgeClass = 'badge-warning';
                            } else if (code.includes('xac_dinh_thoi_han') || code.includes('xn')) {
                                label = 'Số xác định thời hạn';
                                if (row.loai_hop_dong?.TenLoai) label = row.loai_hop_dong.TenLoai.replace('Hợp đồng lao động ', '');
                                badgeClass = 'badge-success';
                            } else if (code === 'chinh_thuc_khong_xac_dinh_thoi_han' || code === 'kxdh') {
                                label = 'Không xác định thời hạn';
                                badgeClass = 'badge-success';
                            } else if (code === 'khoan_viec' || code === 'khoan') {
                                label = 'Khoán việc';
                                badgeClass = 'badge-info';
                            } else if (code === 'thoi_vu') {
                                label = 'Thời vụ';
                                badgeClass = 'badge-secondary';
                            } else if (code.startsWith('nda')) {
                                label = 'NDA';
                                badgeClass = 'badge-dark';
                            }

                            return `<span class="badge ${badgeClass}">${label}</span>`;
                        }
                    },
                    {
                        data: 'NgayBatDau',
                        render: function (data, type, row) {
                            const start = data ? new Date(data).toLocaleDateString('vi-VN') : 'N/A';
                            const end = row.NgayKetThuc ? new Date(row.NgayKetThuc).toLocaleDateString('vi-VN') : 'Không xác định';
                            return `
                                        <div class="date-range">
                                            <div><span class="date-label">Bắt đầu:</span> <span class="date-value">${start}</span></div>
                                            <div><span class="date-label">Kết thúc:</span> <span class="date-value">${end}</span></div>
                                        </div>
                                    `;
                        }
                    },
                    {
                        data: 'TongLuong',
                        render: function (data) {
                            return `<span class="salary-amount">${new Intl.NumberFormat('vi-VN').format(data || 0)} VNĐ</span>`;
                        }
                    },
                    {
                        data: 'TrangThai',
                        render: function (data, type, row) {
                            if (data == 1) {
                                if (row.NgayKetThuc) {
                                    const today = new Date();
                                    today.setHours(0, 0, 0, 0);
                                    const endDate = new Date(row.NgayKetThuc);
                                    endDate.setHours(0, 0, 0, 0);
                                    const diffTime = endDate - today;
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                                    if (diffDays >= 0 && diffDays <= 25) {
                                        const renewUrl = `{{ route('hop-dong.renew', ':id') }}`.replace(':id', row.id);
                                        return `
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span class="badge badge-warning">Sắp hết hạn</span>
                                                    <a href="${renewUrl}" title="Tái kí">
                                                        <i class="bi bi-arrow-repeat text-warning" style="font-size: 18px; font-weight: bold; cursor: pointer;"></i>
                                                    </a>
                                                </div>
                                            `;
                                    }
                                }
                                return '<span class="badge badge-success">Có hiệu lực</span>';
                            }
                            if (data == 0) return '<span class="badge badge-danger">Hết hiệu lực</span>';
                            return '<span class="badge badge-warning">Bị hủy/Thanh lý</span>';
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
                    "oPaginate": { "sFirst": "Đầu", "sPrevious": "Trước", "sNext": "Tiếp", "sLast": "Cuối" }
                },
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [[0, 'desc']],
                drawCallback: function () {
                    $('#contractsTable tbody tr').css('cursor', 'pointer').off('click').on('click', function (e) {
                        if ($(e.target).hasClass('contract-checkbox') || $(e.target).closest('a, button').length) return;
                        const data = table.row(this).data();
                        window.location.href = `/hop-dong/info/${data.id}`;
                    });
                    updateSelectedCount();
                }
            });

            $('#selectAll').on('click', function () {
                $('.contract-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });

            $(document).on('change', '.contract-checkbox', function () {
                updateSelectedCount();
                $('#selectAll').prop('checked', $('.contract-checkbox:checked').length === $('.contract-checkbox').length);
            });

            $('#filterLoai, #filterTrangThai').on('change', function () {
                table.ajax.reload();
            });

            function updateSelectedCount() {
                const count = $('.contract-checkbox:checked').length;
                $('#selectedCount').text(count);
                $('#deleteSelectedBtn').toggle(count > 0);
            }

            $('#deleteSelectedBtn').on('click', function () {
                const ids = $('.contract-checkbox:checked').map(function () { return $(this).val(); }).get();
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: `Bạn có chắc chắn muốn xóa ${ids.length} hợp đồng đã chọn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: '{{ route("hop-dong.xoa-nhieu") }}',
                            method: 'POST',
                            data: { _token: '{{ csrf_token() }}', ids: ids },
                            success: function (res) {
                                Swal.fire('Thành công!', res.message, 'success').then(() => table.ajax.reload());
                            }
                        });
                    }
                });
            });

            checkClearAllBtn();
        });

        // Function to apply UI changes when a filter is clicked
        window.applyFilterAJAX = function (inputId, btnEl) {
            const val = btnEl.dataset.val;
            const label = btnEl.dataset.label;
            const dropdown = $(btnEl).closest('.custom-filter-dropdown');
            const defaultText = dropdown.data('default');

            // 1. Update Hidden Input & Trigger Change (so DataTables reloads)
            const input = document.getElementById(inputId);
            input.value = val;
            $(input).trigger('change');

            // 2. Update toggle text
            const textSpan = dropdown.find('.dropdown-text');
            textSpan.text(label);

            // 3. Update toggle icon & text color
            const iconSpan = dropdown.find('.dropdown-icon');
            const isDefault = (val === '' || (inputId === 'filterTrangThai' && val === '1'));
            
            const isDark = $('body').hasClass('dark-theme');
            const activeColor = isDark ? '#e8eaf0' : '#212529';
            const mutedColor = isDark ? '#8b93a8' : '#6c757d';

            if (isDefault) {
                textSpan.css('color', val === '' ? mutedColor : activeColor);
                iconSpan.html('<i class="bi bi-chevron-down ms-2" style="font-size: 14px; color: ' + mutedColor + ';"></i>');
            } else {
                textSpan.css('color', activeColor);
                const closeIconColor = isDark ? '#f87171' : '#dc2626'; // Subtle red in dark, bright red in light for X
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
            const defaultVal = (inputId === 'filterTrangThai' ? '1' : '');
            const defaultBtn = dropdown.find(`.filter-btn[data-val="${defaultVal}"]`);
            if (defaultBtn.length) {
                applyFilterAJAX(inputId, defaultBtn[0]);
            }
        };

        // Function to clear ALL filters
        window.resetAllFiltersAJAX = function () {
            resetFilterAJAX('filterLoai');
            // By default, Trang Thai is '1' (Có hiệu lực), wait.. if user wants to reset, do we reset to Default ('1') or All ('')? 
            // In the original, default list shows '1'. We reset Trang Thai to '1'.
            const filterTrangThaiBtnDefault = $('#filterTrangThai').closest('.custom-filter-dropdown').find('.filter-btn[data-val="1"]');
            if (filterTrangThaiBtnDefault.length) {
                applyFilterAJAX('filterTrangThai', filterTrangThaiBtnDefault[0]);
            }
        };

        // Function to show/hide the Clear All button depending on filter state
        function checkClearAllBtn() {
            const valLoai = $('#filterLoai').val();
            const valTrangThai = $('#filterTrangThai').val();
            if (valLoai !== '' || valTrangThai !== '1') {
                $('#clearAllFiltersBtn').show();
            } else {
                $('#clearAllFiltersBtn').hide();
            }
        }
    </script>
@endpush