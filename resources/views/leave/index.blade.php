@extends('layouts.app')

@section('title', 'Quản lý nghỉ phép - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .close-modal {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #1f2937;
        }

        /* Badge Cyan for Annual Leave */
        .badge-cyan {
            background-color: #cffafe;
            color: #0e7490;
        }

        /* Badge Pink for Sick Leave */
        .badge-pink {
            background-color: #fce7f3;
            color: #be185d;
        }

        /* Tab Styles */
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: -2px;
        }

        .tab:hover {
            color: #0BAA4B;
        }

        .tab.active {
            color: #0BAA4B;
            border-bottom-color: #0BAA4B;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* DataTables Custom Styles */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 8px;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 20px;
        }

        .dataTables_wrapper .paginate_button {
            padding: 6px 12px !important;
            border-radius: 6px !important;
            border: 1px solid #e5e7eb !important;
            margin-left: 4px !important;
            cursor: pointer;
        }

        .dataTables_wrapper .paginate_button.current {
            background: #0BAA4B !important;
            color: white !important;
            border-color: #0BAA4B !important;
        }

        select.form-control {
            height: 42px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }

        /* Dark Mode Overrides */
        body.dark-theme .modal-content {
            background: #1a1d2d;
            border-color: #2e3349;
        }

        body.dark-theme .modal-header,
        body.dark-theme .modal-footer {
            border-color: #2e3349;
        }

        body.dark-theme .modal-header h2 {
            color: #e8eaf0;
        }

        body.dark-theme .tabs {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .tab {
            color: #8b93a8;
        }

        body.dark-theme .tab:hover {
            color: var(--primary-green);
        }

        body.dark-theme .tab.active {
            color: var(--primary-green);
            border-bottom-color: var(--primary-green);
        }

        body.dark-theme .dataTables_wrapper .dataTables_filter input,
        body.dark-theme .dataTables_wrapper .dataTables_length select {
            background: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .dataTables_wrapper .paginate_button {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #c3c8da !important;
        }

        body.dark-theme .badge-cyan {
            background-color: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
        }

        body.dark-theme .badge-pink {
            background-color: rgba(236, 72, 153, 0.15);
            color: #f9a8d4;
        }

        body.dark-theme #bulkActionBar {
            background: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        body.dark-theme #bulkActionBar span {
            color: #34d399 !important;
        }

        body.dark-theme .form-label {
            color: #8b93a8 !important;
        }

        /* Clear Filter Button Styles */
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
            height: 42px;
        }

        .btn-clear-filter:hover {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .btn-clear-filter svg {
            width: 16px;
            height: 16px;
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
        <h1>Quản lý nghỉ phép</h1>
        <p>Theo dõi và phê duyệt đơn xin nghỉ phép</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Đơn trong tháng</div>
            <div class="value" style="color: #3b82f6;">{{ $totalInMonth }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Đơn chờ duyệt</div>
            <div class="value" style="color: #f59e0b;">{{ $pendingCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Đã phê duyệt</div>
            <div class="value" style="color: #10b981;">{{ $approvedCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Từ chối</div>
            <div class="value" style="color: #ef4444;">{{ $rejectedCount }}</div>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="card"
        style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 16px;">
        <div class="card-body"
            style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-weight: 600; color: #088c3d;">Đã chọn <span id="selectedCount">0</span> đơn</span>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-primary" onclick="bulkApprove()">
                    Duyệt các đơn đã chọn
                </button>
                <button type="button" class="btn btn-secondary" onclick="bulkReject()">
                    Từ chối các đơn đã chọn
                </button>
            </div>
        </div>
    </div>

    <!-- Filter and Action Bar -->
    <div class="card">
        <form action="{{ route('nghi-phep.danh-sach') }}" method="GET" class="action-bar" id="filterForm">
            <div style="display: flex; gap: 12px; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Phòng ban</label>
                    <select name="phong_ban_id" class="form-control" style="width: auto; margin-bottom: 0;"
                        onchange="this.form.submit()">
                        <option value="">Tất cả phòng ban</option>
                        @foreach($phongBans as $pb)
                            <option value="{{ $pb->id }}" {{ request('phong_ban_id') == $pb->id ? 'selected' : '' }}>
                                {{ $pb->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Loại phép</label>
                    <select name="loai_phep_id" class="form-control" style="width: auto; margin-bottom: 0;"
                        onchange="this.form.submit()">
                        <option value="">Tất cả loại phép</option>
                        @foreach($loaiNghiPheps as $lp)
                            <option value="{{ $lp->id }}" {{ request('loai_phep_id') == $lp->id ? 'selected' : '' }}>
                                {{ $lp->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request('phong_ban_id') || request('loai_phep_id') || request('trang_thai'))
                    <div class="form-group" style="margin-bottom: 0;">
                        <a href="{{ route('nghi-phep.danh-sach') }}" class="btn-clear-filter">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Xóa bộ lọc
                        </a>
                    </div>
                @endif
            </div>
            <div class="action-buttons">
{{-- 
                <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
--}}
                <a href="{{ route('nghi-phep.admin-dang-ky') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký nghỉ phép
                </a>
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="card">
        <div class="tabs">
            <button class="tab {{ !request('trang_thai') ? 'active' : '' }}" onclick="filterStatus('')">Tất cả
                ({{ $totalCount }})</button>
            <button class="tab {{ request('trang_thai') == '2' ? 'active' : '' }}" onclick="filterStatus('2')">Chờ duyệt
                ({{ $pendingCount }})</button>
            <button class="tab {{ request('trang_thai') == '1' ? 'active' : '' }}" onclick="filterStatus('1')">Đã duyệt
                ({{ $approvedCount }})</button>
            <button class="tab {{ request('trang_thai') == '0' ? 'active' : '' }}" onclick="filterStatus('0')">Từ chối
                ({{ $rejectedCount }})</button>
        </div>

        <!-- All Tab -->
        <div class="tab-content active" id="all-tab">
            <div class="table-container">
                <table class="table" id="leaveTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">
                                STT<br>
                                <input type="checkbox" id="selectAll" style="cursor: pointer;">
                            </th>
                            <th>Nhân viên</th>
                            <th>Phòng ban</th>
                            <th>Loại phép</th>
                            <th>Thời gian</th>
                            <th>Lý do</th>
                            <th>Trạng thái</th>
                            @can('Duyệt Nghỉ Phép')
                                <th>Hành động</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $index => $leave)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <strong>{{ $index + 1 }}</strong><br>
                                    <input type="checkbox" class="row-checkbox" value="{{ $leave->id }}"
                                        style="cursor: pointer;">
                                </td>
                                <td>
                                    @if($leave->nhanVien)
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="avatar"
                                                style="width: 40px; height: 40px; flex-shrink: 0; min-width: 40px; min-height: 40px; background: #0BAA4B; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; overflow: hidden;">
                                                @if($leave->nhanVien->AnhDaiDien)
                                                    <img src="{{ asset($leave->nhanVien->AnhDaiDien) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    {{ substr($leave->nhanVien->Ten, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="font-medium">{{ $leave->nhanVien->Ten }}</div>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="avatar" style="width: 40px; height: 40px; flex-shrink: 0; min-width: 40px; min-height: 40px; background: #9ca3af; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold;">
                                                ?
                                            </div>
                                            <div class="font-medium text-danger">Nhân viên đã xóa</div>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $leave->nhanVien?->ttCongViec?->phongBan?->Ten ?? 'N/A' }}</td>
                                <td>
                                    @if($leave->loaiNghiPhep)
                                        <span
                                            class="badge {{ $leave->loaiNghiPhep->Ten == 'Nghỉ phép năm' ? 'badge-cyan' : ($leave->loaiNghiPhep->Ten == 'Nghỉ ốm' ? 'badge-pink' : 'badge-info') }}">
                                            {{ $leave->loaiNghiPhep->Ten }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px; line-height: 1.4;">
                                        <div style="color: #8b93a8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Từ:</div>
                                        <div style="font-weight: 700; font-size: 14px; color: var(--text-primary);">
                                            {{ $leave->TuNgay->format('d/m/Y') }}
                                            @if($leave->TuBuoi != 'ca_ngay')
                                                <span style="font-weight: 500; font-size: 11px; color: #8b93a8;">({{ $leave->TuBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</span>
                                            @endif
                                        </div>
                                        
                                        <div style="color: #8b93a8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">Đến:</div>
                                        <div style="font-weight: 700; font-size: 14px; color: var(--text-primary);">
                                            {{ $leave->DenNgay->format('d/m/Y') }}
                                            @if($leave->DenBuoi != 'ca_ngay')
                                                <span style="font-weight: 500; font-size: 11px; color: #8b93a8;">({{ $leave->DenBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</span>
                                            @endif
                                        </div>

                                        <div style="margin-top: 6px; color: #10b981; font-weight: 700; font-size: 13px;">
                                            Tổng: {{ number_format((float)$leave->SoNgayNghi, 1) }} ngày
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $leave->LyDo }}</td>
                                <td>
                                    @if($leave->TrangThai === 2)
                                        <span class="badge badge-purple">Chờ duyệt</span>
                                    @elseif($leave->TrangThai === 1)
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @elseif($leave->TrangThai === 0)
                                        <span class="badge badge-danger">Từ chối</span>
                                    @endif
                                </td>
                                @can('Duyệt Nghỉ Phép')
                                    <td>
                                        @if($leave->TrangThai === 2)
                                            <div style="display: flex; gap: 8px;">
                                                <button class="btn-icon text-success" onclick="approveLeave({{ $leave->id }})"
                                                    title="Duyệt">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button class="btn-icon text-danger" onclick="rejectLeave({{ $leave->id }})"
                                                    title="Từ chối">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span style="color: #9ca3af; font-size: 14px;">-</span>
                                        @endif
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Old Leave Modal removed as we now use a dedicated page --}}

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script>
        const workingSchedule = @json($workingSchedule->keyBy('Thu'));
        const annualLeaveLimit = {{ \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5) }};
        const annualLeaveId = {{ $loaiNghiPheps->firstWhere('Ten', 'Nghỉ phép năm')->id ?? 'null' }};
        let leaveLimitsMap = {};
        let startPicker, endPicker;

        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#leaveTable').DataTable({
                "language": {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Xem _MENU_ mục",
                    "sZeroRecords": "Không tìm thấy dòng nào phù hợp",
                    "sInfo": "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang xem 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sSearch": "Tìm:",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                "order": [[4, "desc"]],
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [0 @can('Duyệt Nghỉ Phép'), 7 @endcan] }
                ]
            });

            // Logic hiển thị thanh tác vụ hàng loạt
            function updateBulkActionBar() {
                // Sử dụng table.$() để đếm trên tất cả các trang
                const selectedCount = table.$('.row-checkbox:checked').length;
                const bulkActionBar = document.getElementById('bulkActionBar');
                const selectedCountSpan = document.getElementById('selectedCount');

                if (bulkActionBar && selectedCountSpan) {
                    if (selectedCount > 0) {
                        bulkActionBar.style.display = 'block';
                        selectedCountSpan.textContent = selectedCount;
                    } else {
                        bulkActionBar.style.display = 'none';
                    }
                }
            }

            $('#selectAll').on('change', function() {
                const isChecked = this.checked;
                $(table.rows({search: 'applied'}).nodes()).find('.row-checkbox').prop('checked', isChecked);
                updateBulkActionBar();
            });

            $('#leaveTable').on('change', '.row-checkbox', function() {
                updateBulkActionBar();
                const allCheckboxes = $(table.rows({search: 'applied'}).nodes()).find('.row-checkbox');
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                const selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
                    selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                }
            });

            // Phơi bày các hàm ra window để nút bấm gọi được
            window.approveLeave = function(id) {
                confirmAction('Bạn có chắc muốn duyêt đơn này?', () => {
                    sendRequest(`/nghi-phep/duyet/${id}`);
                });
            }

            window.rejectLeave = function(id) {
                confirmAction('Bạn có chắc muốn từ chối đơn này?', () => {
                    sendRequest(`/nghi-phep/tu-choi/${id}`);
                }, 'warning');
            }

            window.bulkApprove = function() {
                const ids = getSelectedIds();
                if (ids.length === 0) return;
                
                confirmAction(`Duyệt ${ids.length} đơn đã chọn?`, () => {
                    sendRequest('{{ route("nghi-phep.bulk-duyet") }}', { ids: ids });
                });
            }

            window.bulkReject = function() {
                const ids = getSelectedIds();
                if (ids.length === 0) return;

                confirmAction(`Từ chối ${ids.length} đơn đã chọn?`, () => {
                    sendRequest('{{ route("nghi-phep.bulk-tu-choi") }}', { ids: ids });
                }, 'warning');
            }

            // Helpers
            function getSelectedIds() {
                const ids = [];
                table.$('.row-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                return ids;
            }

            function confirmAction(message, callback, icon = 'question') {
                Swal.fire({
                    title: 'Xác nhận',
                    text: message,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: icon === 'warning' ? '#ef4444' : '#0BAA4B'
                }).then((result) => {
                    if (result.isConfirmed) callback();
                });
            }

            function sendRequest(url, data = {}) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công', data.message || 'Thao tác thành công', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(() => Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error'));
            }
        });

        function filterStatus(status) {
            const url = new URL(window.location.href);
            if (status !== '') {
                url.searchParams.set('trang_thai', status);
            } else {
                url.searchParams.delete('trang_thai');
            }
            window.location.href = url.toString();
        }
    </script>
    </script>
@endpush
