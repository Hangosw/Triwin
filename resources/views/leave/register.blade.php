@extends('layouts.app')

@section('title', 'Đăng ký nghỉ phép')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-green: #0BAA4B;
            --primary-gradient: linear-gradient(135deg, #0BAA4B 0%, #059669 100%);
            --secondary-green: #D1E7DD;
            --text-main: #111827;
            --text-muted: #6b7280;
            --surface: #ffffff;
            --bg-main: #f9fafb;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .registration-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .step-card {
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: var(--shadow-md);
            padding: 32px;
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .step-number {
            width: 36px;
            height: 36px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .step-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control:not(textarea), .select2-container--default .select2-selection--single {
            width: 100% !important;
            height: 48px !important; /* Force same height */
            padding: 10px 16px !important;
            border-radius: 12px !important;
            border: 1.5px solid #e2e8f0 !important;
            font-size: 15px !important;
            transition: var(--transition);
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
        }

        textarea.form-control {
            border-radius: 12px !important;
            border: 1.5px solid #e2e8f0 !important;
            background: #f8fafc !important;
            padding: 12px 16px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 28px !important;
            color: var(--text-main) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 18px;
            padding-right: 44px;
        }

        .form-control:focus, .select2-container--default.select2-container--focus .select2-selection--single {
            outline: none !important;
            border-color: var(--primary-green) !important;
            background: white !important;
            box-shadow: 0 0 0 4px rgba(11, 170, 75, 0.1) !important;
        }

        /* Session Table Styles */
        .session-table-container {
            overflow-x: auto;
            margin-top: 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }

        .session-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 600px;
        }

        .session-table th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .session-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }

        .session-row-label {
            background: #f8fafc;
            font-weight: 700;
            color: #475569;
            text-align: left !important;
            width: 120px;
            position: sticky;
            left: 0;
            z-index: 1;
        }

        .session-checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .session-checkbox {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: var(--primary-green);
        }

        .date-col-header {
            min-width: 100px;
        }

        .date-name {
            font-size: 14px;
            display: block;
        }

        .date-subtext {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            display: block;
            margin-top: 2px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            transition: var(--transition);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(11, 170, 75, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 170, 75, 0.35);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .summary-box {
            background: #ecfdf5;
            border: 1px solid #d1e7dd;
            padding: 24px;
            border-radius: 20px;
            margin-top: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-item .label {
            font-size: 14px;
            color: #065f46;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .summary-item .value {
            font-size: 24px;
            font-weight: 800;
            color: #047857;
        }

        /* Multi-selection styles */
        .select-group {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .quick-select-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: white;
            transition: var(--transition);
        }

        .quick-select-btn:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        /* Dark Mode Overrides */
        body.dark-theme {
            --surface: #1a1d2d;
            --text-main: #e8eaf0;
            --text-muted: #8b93a8;
        }

        body.dark-theme #dateRange {
            color: #e8eaf0 !important;
        }

        body.dark-theme .step-card {
            background: #1a1d2d;
            border-color: #2e3349;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        body.dark-theme .step-title {
            color: #e8eaf0;
        }

        body.dark-theme .form-label {
            color: #c3c8da;
        }

        body.dark-theme .form-control:not(textarea), 
        body.dark-theme .select2-container--default .select2-selection--single,
        body.dark-theme textarea.form-control {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-control:focus, 
        body.dark-theme .select2-container--default.select2-container--focus .select2-selection--single {
            background: #1a1d2d !important;
            border-color: var(--primary-green) !important;
        }

        body.dark-theme .session-table-container {
            border-color: #2e3349;
        }

        body.dark-theme .session-table {
            background: #1a1d2d;
        }

        body.dark-theme .session-table th,
        body.dark-theme .session-row-label {
            background: #21263a;
            color: #c3c8da;
            border-color: #2e3349;
        }

        body.dark-theme .session-table td {
            border-color: #2e3349;
            color: #e1e1e1;
        }

        body.dark-theme .quick-select-btn {
            background: #21263a;
            border-color: #2e3349;
            color: #c3c8da;
        }

        body.dark-theme .summary-box {
            background: rgba(11, 170, 75, 0.1);
            border-color: rgba(11, 170, 75, 0.2);
        }

        body.dark-theme .summary-item .label {
            color: #10b981;
        }

        body.dark-theme .summary-item .value {
            color: #34d399;
        }

        body.dark-theme #splitLeaveSection {
            background: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        body.dark-theme #splitMessage {
            color: #fbbf24;
        }

        body.dark-theme .btn-secondary {
            background: #2e3349;
            color: #c3c8da;
        }

        body.dark-theme .btn-secondary:hover {
            background: #39405a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="registration-container">
        <div class="page-header" style="margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1 style="font-size: 32px; font-weight: 850; letter-spacing: -0.03em; margin: 0;">{{ isset($isAdmin) && $isAdmin ? 'Admin Đăng ký nghỉ phép' : 'Đăng ký nghỉ phép' }}</h1>
                <p style="font-size: 16px; color: var(--text-muted); margin-top: 4px;">{{ isset($isAdmin) && $isAdmin ? 'Đăng ký nghỉ phép hộ nhân viên' : 'Chọn thời gian và các buổi nghỉ chi tiết' }}</p>
            </div>
            <a href="{{ isset($isAdmin) && $isAdmin ? route('nghi-phep.danh-sach') : route('nghi-phep.ca-nhan') }}" class="btn btn-secondary" style="padding: 10px 20px; font-size: 14px;">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <form id="leaveRegistrationForm" onsubmit="submitForm(event)">
            @csrf
            
            <!-- Step 1: Chọn loại và thời gian -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <h2 class="step-title">Nhân viên, Loại nghỉ & Thời gian</h2>
                </div>

                @if(isset($isAdmin) && $isAdmin)
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label">Chọn nhân viên <span style="color: #ef4444;">*</span></label>
                    <select class="form-control" name="NhanVienId" id="nhanVienSelect">
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach($nhanViens as $nv)
                            <option value="{{ $nv->id }}">{{ $nv->Ten }} - {{ $nv->ttCongViec->phongBan->Ten ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div class="form-group">
                        <label class="form-label">Loại nghỉ phép <span style="color: #ef4444;">*</span></label>
                        <select class="form-control" name="LoaiNghiPhepId" id="loaiNghiPhepSelect">
                            <option value="">-- Chọn loại nghỉ --</option>
                            @foreach($loaiNghiPheps as $type)
                                <option value="{{ $type->id }}" data-limit="{{ $leaveLimitsMap[$type->id] ?? 999 }}">
                                    {{ $type->Ten }} 
                                    @if(!(isset($isAdmin) && $isAdmin))
                                        ({{ $type->Ten == 'Nghỉ phép năm' ? 'Khả dụng' : 'Còn' }} {{ number_format($leaveLimitsMap[$type->id] ?? 999, 1) }} ngày)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Khoảng thời gian nghỉ <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-control" id="dateRange" placeholder="Chọn từ ngày - đến ngày" readonly>
                        <input type="hidden" name="TuNgay" id="tuNgayHidden">
                        <input type="hidden" name="DenNgay" id="denNgayHidden">
                    </div>
                </div>
            </div>

            <!-- Step 2: Chọn buổi nghỉ chi tiết -->
            <div class="step-card" id="step2Card" style="display: none;">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <h2 class="step-title">Chọn buổi nghỉ chi tiết</h2>
                    <div class="select-group">
                        <div class="quick-select-btn" onclick="quickSelect('all')">Chọn tất cả</div>
                        <div class="quick-select-btn" onclick="quickSelect('morning')">Chỉ sáng</div>
                        <div class="quick-select-btn" onclick="quickSelect('afternoon')">Chỉ chiều</div>
                        <div class="quick-select-btn" onclick="quickSelect('none')">Bỏ chọn</div>
                    </div>
                </div>

                <div class="session-table-container">
                    <table class="session-table" id="sessionPickerTable">
                        <thead>
                            <tr id="tableHeader">
                                <th class="session-row-label">Buổi</th>
                                <!-- Dynamic headers will be inserted here -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="morningRow">
                                <td class="session-row-label">Sáng</td>
                                <!-- Dynamic checkboxes will be inserted here -->
                            </tr>
                            <tr id="afternoonRow">
                                <td class="session-row-label">Chiều</td>
                                <!-- Dynamic checkboxes will be inserted here -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="summary-box">
                    <div class="summary-item">
                        <div class="label">Tổng số ngày nghỉ đăng ký:</div>
                        <div class="value" id="totalDaysDisplay">0.0 ngày</div>
                        <input type="hidden" name="SoNgayNghi" id="soNgayNghiInput">
                    </div>
                    <div class="summary-item" id="balanceWarning" style="display: none;">
                        <span style="color: #9a3412; font-size: 13px; font-weight: 600;">
                            <i class="bi bi-exclamation-triangle"></i> Vượt quá hạn mức!
                        </span>
                    </div>
                </div>

                <!-- Split Leave Warning & Type Selection -->
                <div id="splitLeaveSection" style="display: none; background: #fff7ed; padding: 24px; border-radius: 20px; border: 1px solid #ffedd5; margin-top: 24px;">
                    <p id="splitMessage" style="color: #9a3412; font-size: 15px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 10px; line-height: 1.5;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px; margin-top: 2px;"></i>
                        <span>Thông báo: Quỹ phép năm của bạn không đủ hoặc vượt quá giới hạn mỗi lần sử dụng của hệ thống. Phần dư sẽ được tính vào loại nghỉ thay thế.</span>
                    </p>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Chọn loại nghỉ thay thế cho phần dư <span style="color: #ef4444;">*</span></label>
                        <select class="form-control" name="SplitLoaiNghiPhepId" id="splitTypeSelect">
                            <option value="">-- Chọn loại nghỉ thay thế --</option>
                            @foreach($loaiNghiPheps as $type)
                                <option value="{{ $type->id }}">{{ $type->Ten }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 3: Lý do & Hoàn tất -->
            <div class="step-card" id="step3Card" style="display: none;">
                <div class="step-header">
                    <div class="step-number">3</div>
                    <h2 class="step-title">Lý do & Xác nhận</h2>
                </div>

                <div class="form-group">
                    <label class="form-label">Lý do nghỉ <span style="color: #ef4444;">*</span></label>
                    <textarea class="form-control" name="LyDo" rows="4" placeholder="Nhập lý do nghỉ chi tiết của bạn..."></textarea>
                </div>

                <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        Gửi đơn đăng ký nghỉ phép
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script>
        const workingSchedule = @json($workingSchedule->keyBy('Thu'));
        let leaveLimitsMap = @json($leaveLimitsMap);
        const annualLeaveLimit = {{ $annualLeaveLimit }};
        const annualLeaveId = {{ $annualLeaveId ?? 'null' }};
        const isAdmin = {{ (isset($isAdmin) && $isAdmin) ? 'true' : 'false' }};
        let selectedDates = [];

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr.localize(flatpickr.l10ns.vn);
            
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function (dates) {
                    if (dates.length === 2) {
                        selectedDates = getDatesInRange(dates[0], dates[1]);
                        generateSessionTable(selectedDates);
                        document.getElementById('tuNgayHidden').value = dates[0].toISOString().split('T')[0];
                        document.getElementById('denNgayHidden').value = dates[1].toISOString().split('T')[0];
                        document.getElementById('step2Card').style.display = 'block';
                        document.getElementById('step3Card').style.display = 'block';
                        calculateTotal();
                    } else {
                        document.getElementById('step2Card').style.display = 'none';
                        document.getElementById('step3Card').style.display = 'none';
                    }
                }
            });

            if (isAdmin) {
                document.getElementById('nhanVienSelect').addEventListener('change', function() {
                    const nhanVienId = this.value;
                    if (nhanVienId) {
                        fetch(`{{ route('nghi-phep.api.limits') }}?nhanVienId=${nhanVienId}`)
                            .then(res => res.json())
                            .then(data => {
                                leaveLimitsMap = data;
                                // Update text in loaiNghiPhepSelect
                                const typeSelect = document.getElementById('loaiNghiPhepSelect');
                                Array.from(typeSelect.options).forEach(opt => {
                                    if (opt.value && leaveLimitsMap[opt.value] !== undefined) {
                                        const originalText = opt.innerText.split('(')[0].trim();
                                        opt.innerText = `${originalText} (Còn ${parseFloat(leaveLimitsMap[opt.value]).toFixed(1)} ngày)`;
                                    }
                                });
                                calculateTotal();
                            });
                    } else {
                        leaveLimitsMap = {};
                        calculateTotal();
                    }
                });
            }
        });

        function getDatesInRange(startDate, endDate) {
            const dates = [];
            let currDate = new Date(startDate);
            while (currDate <= endDate) {
                dates.push(new Date(currDate));
                currDate.setDate(currDate.getDate() + 1);
            }
            return dates;
        }

        function generateSessionTable(dates) {
            const header = document.getElementById('tableHeader');
            const morningRow = document.getElementById('morningRow');
            const afternoonRow = document.getElementById('afternoonRow');

            // Clear previous
            header.innerHTML = '<th class="session-row-label">Buổi</th>';
            morningRow.innerHTML = '<td class="session-row-label">Sáng</td>';
            afternoonRow.innerHTML = '<td class="session-row-label">Chiều</td>';

            const daysNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];

            dates.forEach(date => {
                const dateStr = date.toISOString().split('T')[0];
                const dayOfWeek = date.getDay();
                const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);
                const isWorkingDay = workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec > 0;

                // Header
                const th = document.createElement('th');
                th.className = 'date-col-header';
                th.innerHTML = `
                    <span class="date-name">${date.getDate()}/${date.getMonth() + 1}</span>
                    <span class="date-subtext">${daysNames[dayOfWeek]}</span>
                `;
                if (!isWorkingDay) th.style.opacity = '0.5';
                header.appendChild(th);

                // Morning Checkbox
                const tdMorning = document.createElement('td');
                if (isWorkingDay) {
                    tdMorning.innerHTML = `
                        <div class="session-checkbox-wrapper">
                            <input type="checkbox" class="session-checkbox morning-chk" 
                                name="ChiTietBuoi[${dateStr}][]" value="sang" 
                                onchange="calculateTotal()" checked>
                        </div>
                    `;
                } else {
                    tdMorning.innerHTML = '<span style="color: #cbd5e1; font-size: 11px;">Nghỉ</span>';
                    tdMorning.style.background = '#f1f5f9';
                }
                morningRow.appendChild(tdMorning);

                // Afternoon Checkbox
                const tdAfternoon = document.createElement('td');
                if (isWorkingDay) {
                    tdAfternoon.innerHTML = `
                        <div class="session-checkbox-wrapper">
                            <input type="checkbox" class="session-checkbox afternoon-chk" 
                                name="ChiTietBuoi[${dateStr}][]" value="chieu" 
                                onchange="calculateTotal()" checked>
                        </div>
                    `;
                } else {
                    tdAfternoon.innerHTML = '<span style="color: #cbd5e1; font-size: 11px;">Nghỉ</span>';
                    tdAfternoon.style.background = '#f1f5f9';
                }
                afternoonRow.appendChild(tdAfternoon);
            });
        }

        function calculateTotal() {
            const checkboxes = document.querySelectorAll('.session-checkbox:checked');
            let total = 0;
            
            // Group by date to handle 0.5 or 1.0 logic
            const dateGroups = {};
            checkboxes.forEach(cb => {
                const name = cb.getAttribute('name'); // ChiTietBuoi[2024-04-01][]
                const dateMatch = name.match(/\[(.*?)\]/);
                if (dateMatch) {
                    const date = dateMatch[1];
                    dateGroups[date] = (dateGroups[date] || 0) + 1;
                }
            });

            for (const date in dateGroups) {
                const dateObj = new Date(date);
                const dayOfWeek = dateObj.getDay();
                const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);
                
                if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                    const workVal = parseFloat(workingSchedule[dbDayOfWeek].CoLamViec);
                    if (dateGroups[date] >= 2) {
                        total += workVal;
                    } else {
                        total += Math.min(workVal, 0.5);
                    }
                }
            }

            document.getElementById('totalDaysDisplay').innerText = total.toFixed(1) + ' ngày';
            document.getElementById('soNgayNghiInput').value = total;

            // Kiểm tra tách đơn (Split Leave)
            const typeSelect = document.getElementById('loaiNghiPhepSelect');
            const splitSection = document.getElementById('splitLeaveSection');
            const splitMessage = document.getElementById('splitMessage').querySelector('span');
            const splitTypeSelect = document.getElementById('splitTypeSelect');
            
            const selectedTypeId = typeSelect.value;
            const remainingBalance = leaveLimitsMap[selectedTypeId] !== undefined ? parseFloat(leaveLimitsMap[selectedTypeId]) : 999;
            
            let message = "";
            if (selectedTypeId == annualLeaveId) {
                const effectiveLimit = Math.min(remainingBalance, annualLeaveLimit);
                if (total > effectiveLimit) {
                    message = total > remainingBalance 
                        ? `Quỹ phép năm của bạn không đủ (còn ${remainingBalance} ngày). Phần dư sẽ được tính vào loại nghỉ thay thế.` 
                        : `Số ngày đăng ký vượt quá giới hạn mỗi lần dùng (${annualLeaveLimit} ngày). Phần dư sẽ được tính vào loại nghỉ thay thế.`;
                }
            } else if (remainingBalance < total && remainingBalance !== 999) {
                message = `Số ngày đăng ký vượt quá hạn mức tối đa còn lại của loại nghỉ này (${remainingBalance} ngày). Phần dư sẽ được tính vào loại nghỉ thay thế.`;
            }
            
            if (message) {
                splitMessage.innerText = message;
                splitSection.style.display = 'block';
                // Trigger select2 update if using select2
                $(splitTypeSelect).trigger('change');

                // Cập nhật danh sách loại nghỉ thay thế (loại bỏ loại hiện tại và loại hết hạn mức)
                Array.from(splitTypeSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const optBalance = leaveLimitsMap[opt.value] !== undefined ? parseFloat(leaveLimitsMap[opt.value]) : 999;
                    if (opt.value == selectedTypeId || (optBalance <= 0)) {
                        opt.disabled = true;
                        opt.style.display = 'none';
                        if (splitTypeSelect.value == opt.value) splitTypeSelect.value = "";
                    } else {
                        opt.disabled = false;
                        opt.style.display = 'block';
                    }
                });
            } else {
                splitSection.style.display = 'none';
                splitTypeSelect.value = "";
            }

            // Check limits (old warning box)
            if (total > remainingBalance && remainingBalance !== 999) {
                document.getElementById('balanceWarning').style.display = 'block';
            } else {
                document.getElementById('balanceWarning').style.display = 'none';
            }
        }

        // Thêm listener cho select loại nghỉ chính
        document.getElementById('loaiNghiPhepSelect').addEventListener('change', calculateTotal);

        function quickSelect(type) {
            const allCheckboxes = document.querySelectorAll('.session-checkbox');
            allCheckboxes.forEach(cb => {
                if (type === 'all') cb.checked = true;
                else if (type === 'none') cb.checked = false;
                else if (type === 'morning') cb.checked = cb.classList.contains('morning-chk');
                else if (type === 'afternoon') cb.checked = cb.classList.contains('afternoon-chk');
            });
            calculateTotal();
        }

        function submitForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Validation
            if (isAdmin && !document.getElementById('nhanVienSelect').value) {
                Swal.fire('Lỗi', 'Vui lòng chọn nhân viên.', 'error');
                return;
            }

            const total = parseFloat(document.getElementById('soNgayNghiInput').value);
            if (total <= 0) {
                Swal.fire('Lỗi', 'Vui lòng chọn ít nhất một buổi nghỉ.', 'error');
                return;
            }

            const splitSection = document.getElementById('splitLeaveSection');
            const splitTypeSelect = document.getElementById('splitTypeSelect');
            if (splitSection.style.display === 'block' && !splitTypeSelect.value) {
                Swal.fire('Thiếu thông tin', 'Vui lòng chọn loại nghỉ thay thế cho phần dư.', 'warning');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang gửi...';

            fetch('{{ route("nghi-phep.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: data.message,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = isAdmin ? '{{ route("nghi-phep.danh-sach") }}' : '{{ route("nghi-phep.ca-nhan") }}';
                    });
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Gửi đơn đăng ký nghỉ phép';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi', 'Có lỗi xảy ra khi gửi đơn.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Gửi đơn đăng ký nghỉ phép';
            });
        }
    </script>
@endpush
