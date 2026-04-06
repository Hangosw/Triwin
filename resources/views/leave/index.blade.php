@extends('layouts.app')

@section('title', 'Quản lý nghỉ phép - Vietnam Rubber Group')

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
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Trạng thái</label>
                    <select name="trang_thai" class="form-control" style="width: auto; margin-bottom: 0;"
                        onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="2" {{ request('trang_thai') == '2' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="0" {{ request('trang_thai') == '0' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
            </div>
            <div class="action-buttons">
                <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
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
                            <th>Hành động</th>
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
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="avatar"
                                            style="width: 40px; height: 40px; background: #0BAA4B; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold;">
                                            {{ substr($leave->nhanVien->Ten, 0, 1) }}
                                        </div>
                                        <div class="font-medium">{{ $leave->nhanVien->Ten }}</div>
                                    </div>
                                </td>
                                <td>{{ $leave->nhanVien->ttCongViec->phongBan->Ten ?? 'N/A' }}</td>
                                <td>
                                    <span
                                        class="badge {{ $leave->loaiNghiPhep->Ten == 'Nghỉ phép năm' ? 'badge-cyan' : ($leave->loaiNghiPhep->Ten == 'Nghỉ ốm' ? 'badge-pink' : 'badge-info') }}">
                                        {{ $leave->loaiNghiPhep->Ten }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 13px;">
                                        <div style="color: #6b7280;">Từ: <span class="font-medium" style="color: #1f2937;">{{ $leave->TuNgay->format('d/m/Y') }}</span> @if($leave->TuBuoi != 'ca_ngay') <small>({{ $leave->TuBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</small> @endif </div>
                                        <div style="color: #6b7280;">Đến: <span class="font-medium" style="color: #1f2937;">{{ $leave->DenNgay->format('d/m/Y') }}</span> @if($leave->DenBuoi != 'ca_ngay') <small>({{ $leave->DenBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</small> @endif </div>
                                        <div style="color: #0BAA4B; font-weight: 600;">Tổng: {{ number_format((float)$leave->SoNgayNghi, 1) }} ngày</div>
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

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr.localize(flatpickr.l10ns.vn);

            startPicker = flatpickr("#leaveFromDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function (selectedDates) {
                    if (endPicker) endPicker.set('minDate', selectedDates[0]);
                    calculateDays();
                }
            });

            endPicker = flatpickr("#leaveToDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function () {
                    calculateDays();
                }
            });

            document.getElementById('leaveType').addEventListener('change', calculateDays);
            
            // Lấy hạn mức khi chọn nhân viên
            $('#leaveEmployee').on('change', function() {
                const nhanVienId = $(this).val();
                if (nhanVienId) {
                    fetch(`{{ route('nghi-phep.api.limits') }}?nhanVienId=${nhanVienId}`)
                        .then(res => res.json())
                        .then(data => {
                            leaveLimitsMap = data;
                            calculateDays();
                        });
                } else {
                    leaveLimitsMap = {};
                    calculateDays();
                }
            });
        });

        $(document).ready(function () {
            // Initialize DataTable
            $('#leaveTable').DataTable({
                "language": {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Xem _MENU_ mục",
                    "sZeroRecords": "Không tìm thấy dòng nào phù hợp",
                    "sInfo": "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang xem 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sInfoPostFix": "",
                    "sSearch": "Tìm:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                "order": [[4, "desc"]], // Sort by Thời gian (index 4) by default
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [0, 7] } // Disable sorting for checkbox (0) and actions (7)
                ]
            });

            // Select All checkboxes
            const selectAll = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCountSpan = document.getElementById('selectedCount');

            function updateBulkActionBar() {
                const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                const count = selectedCheckboxes.length;

                if (count > 0) {
                    bulkActionBar.style.display = 'block';
                    selectedCountSpan.textContent = count;
                } else {
                    bulkActionBar.style.display = 'none';
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    rowCheckboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkActionBar();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    updateBulkActionBar();

                    // Update selectAll state
                    const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                    const someChecked = Array.from(rowCheckboxes).some(c => c.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = someChecked && !allChecked;
                });
            });
        });

        // Filter by status
        function filterStatus(status) {
            const url = new URL(window.location.href);
            if (status !== '') {
                url.searchParams.set('trang_thai', status);
            } else {
                url.searchParams.delete('trang_thai');
            }
            window.location.href = url.toString();
        }

                    document.getElementById('leaveDays').value = '';
                    return;
                }
            }
        }

                // Calculate actual working days
                let count = 0;
                let cur = new Date(fromDate);
                // Reset time to ensure comparison works correctly
                cur.setHours(0, 0, 0, 0);
                let to = new Date(toDate);
                to.setHours(0, 0, 0, 0);

                const tuBuoi = document.getElementById('leaveFromShift').value;
                const denBuoi = document.getElementById('leaveToShift').value;

                while (cur <= to) {
                    const dayOfWeek = cur.getDay();
                    const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);

                    if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                        let dayVal = 1;
                        if (cur.getTime() === fromDate.getTime() && (tuBuoi === 'sang' || tuBuoi === 'chieu')) {
                            dayVal = 0.5;
                        } else if (cur.getTime() === toDate.getTime() && (denBuoi === 'sang' || denBuoi === 'chieu')) {
                            dayVal = 0.5;
                        }
                        count += dayVal;
                    }
                    cur.setDate(cur.getDate() + 1);
                }
                
                // Trường hợp cùng ngày
                if (fromDate.getTime() === toDate.getTime()) {
                    count = 0;
                    const dayOfWeek = fromDate.getDay();
                    const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);
                    if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                        if (tuBuoi === 'sang' && denBuoi === 'sang') count = 0.5;
                        else if (tuBuoi === 'chieu' && denBuoi === 'chieu') count = 0.5;
                        else if (tuBuoi === 'sang' && denBuoi === 'chieu') count = 1.0;
                        else count = 1.0;
                    }
                }
                document.getElementById('leaveDays').value = count.toFixed(1) + ' ngày';

                // Kiểm tra hạn mức (Cảnh báo cho Admin & Split)
                const typeSelect = document.getElementById('leaveType');
                const selectedTypeId = typeSelect.value;
                const remainingBalance = leaveLimitsMap[selectedTypeId] !== undefined ? parseFloat(leaveLimitsMap[selectedTypeId]) : 999;
                
                const splitSection = document.getElementById('splitLeaveSection');
                const splitMessage = document.getElementById('splitMessage');
                const splitTypeSelect = document.getElementById('splitTypeSelect');
                let message = "";

                if (selectedTypeId == annualLeaveId) {
                    const effectiveLimit = Math.min(remainingBalance, annualLeaveLimit);
                    if (count > effectiveLimit) {
                         message = count > remainingBalance ? 'Quỹ phép năm còn lại không đủ.' : 'Vượt quá giới hạn mỗi lần dùng của hệ thống.';
                    }
                } else if (remainingBalance < count && remainingBalance !== 999) {
                    message = `Số ngày đăng ký vượt quá hạn mức tối đa còn lại (${remainingBalance} ngày).`;
                }

                if (message) {
                    splitMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + message + ' Bạn có thể chọn loại nghỉ thay thế hoặc tiếp tục (Admin).';
                    splitSection.style.display = 'block';

                    // Dynamic filtering
                    Array.from(splitTypeSelect.options).forEach(opt => {
                        if (!opt.value) return;
                        const optBalance = leaveLimitsMap[opt.value] !== undefined ? parseFloat(leaveLimitsMap[opt.value]) : 999;
                        if (opt.value == selectedTypeId || (optBalance <= 0)) {
                            opt.style.display = 'none';
                            if (splitTypeSelect.value == opt.value) splitTypeSelect.value = "";
                        } else {
                            opt.style.display = 'block';
                        }
                    });
                } else {
                    splitSection.style.display = 'none';
                }
            }
        }

        // Close modal on outside click
        window.addEventListener('click', function (e) {
            const modal = document.getElementById('leaveModal');
            if (e.target === modal) {
                if (typeof closeLeaveModal === 'function') closeLeaveModal();
            }
        });
    </script>
    </script>
@endpush
