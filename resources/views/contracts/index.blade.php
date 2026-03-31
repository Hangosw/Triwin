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
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý hợp đồng</h1>
        <p>Danh sách hợp đồng lao động</p>
    </div>

    <div class="card">
        <div class="action-bar" style="border-bottom: none;">
            <div class="action-buttons">
                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
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
                <a href="{{ route('hop-dong.taoView') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm hợp đồng
                </a>
            </div>
        </div>

        <div style="padding: 0 24px 20px 24px; display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            {{-- Lọc Loại Hợp Đồng --}}
            <div class="form-group mb-0" style="min-width: 250px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Lọc Loại Hợp Đồng</label>
                <div class="dropdown custom-filter-dropdown" data-default="Tất cả loại (Mặc định)">
                    <input type="hidden" id="filterLoai" value="">
                    <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                        <span class="dropdown-text" style="color: #6c757d; font-size: 14px;">Tất cả loại (Mặc định)</span>
                        <span class="dropdown-icon">
                            <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                        </span>
                    </div>
                    <div class="dropdown-menu p-2 shadow" style="min-width: 280px; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                            <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN LOẠI HỢP ĐỒNG</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                            @php
                                $loaiOpts = [
                                    '' => 'Tất cả loại (Mặc định)',
                                    'thu_viec' => 'Thử việc',
                                    'chinh_thuc_xac_dinh_thoi_han' => 'Xác định thời hạn',
                                    'chinh_thuc_khong_xac_dinh_thoi_han' => 'Không xác định thời hạn',
                                    'khoan_viec' => 'Khoán việc',
                                    'thoi_vu' => 'Thời vụ'
                                ];
                            @endphp
                            @foreach($loaiOpts as $val => $label)
                                <button type="button" class="btn btn-sm filter-btn {{ $val === '' ? 'btn-light fw-bold text-primary active' : 'btn-light' }}" data-val="{{ $val }}" data-label="{{ $label }}" onclick="applyFilterAJAX('filterLoai', this)" style="padding: 8px 12px; font-size: 13px; border-radius: 6px; border: none; background-color: {{ $val === '' ? '#eff6ff' : '#f9fafb' }}; color: {{ $val === '' ? '#3b82f6' : '#374151' }}; display: flex; justify-content: flex-start; align-items: center; transition: all 0.2s;" onmouseover="if(!this.classList.contains('btn-primary')){ this.style.backgroundColor='#e5e7eb'; }" onmouseout="if(!this.classList.contains('btn-primary')){ this.style.backgroundColor=this.dataset.val === '' ? '#eff6ff' : '#f9fafb'; }">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Lọc Trạng Thái --}}
            <div class="form-group mb-0" style="min-width: 250px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Lọc Trạng Thái</label>
                <div class="dropdown custom-filter-dropdown" data-default="Tất cả trạng thái">
                    <input type="hidden" id="filterTrangThai" value="1">
                    <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                        <span class="dropdown-text" style="color: #212529; font-size: 14px;">Có hiệu lực (Mặc định)</span>
                        <span class="dropdown-icon">
                            <i class="bi bi-x-circle-fill text-muted ms-2" style="font-size: 12px; padding: 4px; border-radius: 50%;" onclick="event.stopPropagation(); resetFilterAJAX('filterTrangThai');" onmouseover="this.classList.replace('text-muted', 'text-danger')" onmouseout="this.classList.replace('text-danger', 'text-muted')"></i>
                        </span>
                    </div>
                    <div class="dropdown-menu p-2 shadow" style="min-width: 280px; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                            <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN TRẠNG THÁI</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                            @php
                                $ttOpts = [
                                    '' => 'Tất cả trạng thái',
                                    '1' => 'Có hiệu lực (Mặc định)',
                                    '0' => 'Hết hiệu lực',
                                    '2' => 'Bị hủy/Thanh lý'
                                ];
                            @endphp
                            @foreach($ttOpts as $val => $label)
                                <button type="button" class="btn btn-sm filter-btn {{ $val === '1' ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}" data-val="{{ $val }}" data-label="{{ $label }}" onclick="applyFilterAJAX('filterTrangThai', this)" style="padding: 8px 12px; font-size: 13px; border-radius: 6px; border: none; background-color: {{ $val === '1' ? '#3b82f6' : '#f9fafb' }}; color: {{ $val === '1' ? '#fff' : '#374151' }}; display: flex; justify-content: flex-start; align-items: center; transition: all 0.2s;" onmouseover="if(!this.classList.contains('btn-primary')){ this.style.backgroundColor='#e5e7eb'; }" onmouseout="if(!this.classList.contains('btn-primary')){ this.style.backgroundColor=this.dataset.val === '' ? '#eff6ff' : '#f9fafb'; }">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Xóa bộ lọc nhanh --}}
            <div class="form-group mb-0" id="clearAllFiltersBtn" style="display: none;">
                <button type="button" class="btn btn-outline-danger d-flex align-items-center justify-content-center" onclick="resetAllFiltersAJAX()" style="height: 38px; padding: 0 16px; font-size: 13px; font-weight: 500; gap: 6px; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fee2e2'; this.style.borderColor='#ef4444';" onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#fca5a5';">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Xóa bộ lọc
                </button>
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
                        render: function (data) {
                            if (!data) return 'N/A';
                            let label = data;
                            let badgeClass = 'badge-info';

                            if (data === 'thu_viec') { label = 'Thử việc'; badgeClass = 'badge-warning'; }
                            else if (data === 'chinh_thuc_xac_dinh_thoi_han') { label = 'Xác định thời hạn'; badgeClass = 'badge-success'; }
                            else if (data === 'chinh_thuc_khong_xac_dinh_thoi_han') { label = 'Không xác định thời hạn'; badgeClass = 'badge-success'; }
                            else if (data === 'khoan_viec') { label = 'Khoán việc'; badgeClass = 'badge-info'; }
                            else if (data === 'thoi_vu') { label = 'Thời vụ'; badgeClass = 'badge-secondary'; }

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
                order: [[1, 'asc']],
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
        window.applyFilterAJAX = function(inputId, btnEl) {
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
            textSpan.css('color', val === '' ? '#6c757d' : '#212529');
            
            // 3. Update toggle icon
            const iconSpan = dropdown.find('.dropdown-icon');
            if (val === '') {
                iconSpan.html('<i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>');
            } else {
                iconSpan.html('<i class="bi bi-x-circle-fill text-muted ms-2" style="font-size: 12px; padding: 4px; border-radius: 50%;" onclick="event.stopPropagation(); resetFilterAJAX(\''+inputId+'\');" onmouseover="this.classList.replace(\'text-muted\', \'text-danger\')" onmouseout="this.classList.replace(\'text-danger\', \'text-muted\')"></i>');
            }
            
            // 4. Update internal button styles
            dropdown.find('.filter-btn').each(function() {
                const b = $(this);
                b.removeClass('btn-primary fw-bold shadow-sm').addClass('btn-light').css({
                    'background-color': b.data('val') === '' ? '#eff6ff' : '#f9fafb',
                    'color': b.data('val') === '' ? '#3b82f6' : '#374151'
                });
            });
            
            if (val !== '') {
                $(btnEl).removeClass('btn-light').addClass('btn-primary fw-bold shadow-sm').css({
                    'background-color': '#3b82f6',
                    'color': '#fff'
                });
            } else {
                $(btnEl).addClass('fw-bold');
            }
            
            checkClearAllBtn();
        };

        // Function to clear a specific filter
        window.resetFilterAJAX = function(inputId) {
            const dropdown = $('#' + inputId).closest('.custom-filter-dropdown');
            const defaultBtn = dropdown.find('.filter-btn[data-val=""]');
            if(defaultBtn.length) {
                applyFilterAJAX(inputId, defaultBtn[0]);
            }
        };
        
        // Function to clear ALL filters
        window.resetAllFiltersAJAX = function() {
            resetFilterAJAX('filterLoai');
            // By default, Trang Thai is '1' (Có hiệu lực), wait.. if user wants to reset, do we reset to Default ('1') or All ('')? 
            // In the original, default list shows '1'. We reset Trang Thai to '1'.
            const filterTrangThaiBtnDefault = $('#filterTrangThai').closest('.custom-filter-dropdown').find('.filter-btn[data-val="1"]');
            if(filterTrangThaiBtnDefault.length) {
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
