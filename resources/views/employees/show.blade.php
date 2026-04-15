@extends('layouts.app')

@section('title', 'Chi tiết nhân viên - Vietnam Rubber Group')

@push('styles')
    <style>
        .detail-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .detail-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0BAA4B;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        /* Basic Info Redesign */
        .basic-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .info-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-card-header i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f0fdf4;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .info-card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .info-card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px 24px;
        }

        .info-card-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-card-item.full-width {
            grid-column: 1 / -1;
        }

        .info-card-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .info-card-value {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        body.dark-theme .info-card {
            background: #1a1d27;
            border-color: #2e3349;
            box-shadow: none;
        }

        body.dark-theme .info-card-header {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .info-card-header i {
            background: rgba(22, 163, 74, 0.1);
        }

        body.dark-theme .info-card-header h3 {
            color: #e2e8f0;
        }

        body.dark-theme .info-card-value {
            color: #cbd5e1;
        }

        @media (max-width: 991px) {
            .basic-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .profile-overview {
                flex-direction: column;
                text-align: center;
                gap: 16px !important;
            }

            .profile-overview .badge {
                margin: 0 auto;
            }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
        }

        .profile-info h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .profile-meta {
            display: flex;
            gap: 24px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
        }

        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
            margin-top: 24px;
            overflow-x: auto;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            font-size: 15px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .action-buttons-header {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        /* Standardize Section Headers */
        .detail-section h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0BAA4B;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #ecfdf5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-section h2 i {
            font-size: 1.25rem;
        }

        /* Standardize Tables */
        .premium-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
        }

        .premium-table thead {
            background-color: #f8fafc;
        }

        .premium-table th {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        .premium-table td {
            padding: 16px;
            vertical-align: middle;
            color: #334155;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tr:hover {
            background-color: #f8fafc;
        }

        /* Relatives Section Styling */
        .relatives-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .relatives-table thead {
            background-color: #f8fafc;
        }

        .relatives-table th {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 16px;
            border-bottom: 2px solid #edf2f7;
        }

        .relatives-table td {
            padding: 16px;
            vertical-align: middle;
            color: #1f2937;
            font-size: 14px;
            border-bottom: 1px solid #edf2f7;
        }

        .relatives-table tr:hover {
            background-color: #f9fafb;
        }

        .badge-relationship {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-bo-me {
            background: #E0F2FE;
            color: #0369A1;
        }

        .badge-vo-chong {
            background: #FCE7F3;
            color: #9D174D;
        }

        .badge-con {
            background: #DCFCE7;
            color: #088c3d;
        }

        .badge-khac {
            background: #F3F4F6;
            color: #374151;
        }

        /* Premium Modal Styling */
        .modal-premium {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-premium .modal-header {
            background: linear-gradient(135deg, #0BAA4B 0%, #088c3d 100%);
            color: white;
            border-bottom: none;
            padding: 24px;
        }

        .modal-premium .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-premium .modal-body {
            padding: 32px;
        }

        .form-icon-group {
            position: relative;
        }

        .form-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
        }

        .form-icon-input {
            padding-left: 40px !important;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            transition: all 0.2s;
        }

        .form-icon-input:focus {
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1);
        }

        /* Select2 inside form-icon-group support */
        .form-icon-group .select2-container--default .select2-selection--single {
            padding-left: 32px !important;
            height: 42px !important;
            display: flex;
            align-items: center;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background-color: transparent;
        }

        .form-icon-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            color: #1f2937;
        }

        .form-icon-group .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        body.dark-theme .form-icon-group .select2-container--default .select2-selection--single {
            background-color: #13161f !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .form-icon-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e8eaf0 !important;
        }

        .toggle-switch-group {
            background: #f9fafb;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toggle-switch-group:hover {
            border-color: #0BAA4B;
            background: #f0fdf4;
        }

        .action-icon-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
            background: #fee2e2;
            color: #dc2626;
        }

        .action-icon-btn:hover {
            background: #ef4444;
            color: white;
        }

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
                text-align: center;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons-header {
                justify-content: center;
            }

            .action-buttons-header .btn {
                font-size: 14px !important;
                padding: 10px 20px !important;
                gap: 8px !important;
                width: auto !important;
                flex: none !important;
            }

            .action-buttons-header .btn i {
                font-size: 18px !important;
            }
        }

        /* Dark Mode Overrides */
        body.dark-theme .detail-section {
            background: #1a1d27;
            border: 1px solid #2e3349;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        body.dark-theme .detail-section h2 {
            border-bottom-color: #2e3349;
            color: #0BAA4B;
        }

        body.dark-theme .detail-label {
            color: #8b93a8;
        }

        body.dark-theme .detail-value {
            color: #e8eaf0;
        }

        body.dark-theme .tabs {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .tab {
            color: #6b7492;
        }

        body.dark-theme .tab:hover {
            color: #4ade80;
        }

        body.dark-theme .tab.active {
            color: #4ade80;
            border-bottom-color: #0BAA4B;
        }

        body.dark-theme .premium-table,
        body.dark-theme .relatives-table {
            border-color: #2e3349;
            box-shadow: none;
        }

        body.dark-theme .premium-table thead,
        body.dark-theme .relatives-table thead {
            background-color: #21263a;
        }

        body.dark-theme .premium-table th,
        body.dark-theme .relatives-table th {
            color: #6b7492;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .premium-table td,
        body.dark-theme .relatives-table td {
            color: #c3c8da;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .premium-table tr:hover,
        body.dark-theme .relatives-table tr:hover {
            background-color: #21263a;
        }

        /* Badges in Dark Mode */
        body.dark-theme .badge-bo-me {
            background: rgba(14, 165, 233, 0.15);
            color: #7dd3fc;
        }

        body.dark-theme .badge-vo-chong {
            background: rgba(236, 72, 153, 0.15);
            color: #f9a8d4;
        }

        body.dark-theme .badge-con {
            background: rgba(11, 170, 75, 0.15);
            color: #4ade80;
        }

        body.dark-theme .badge-khac {
            background: #2e3349;
            color: #94a3b8;
        }

        /* Form elements for Filters in Tab Attendance */
        body.dark-theme #attendanceDateDisplay {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .empty-state-cell {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        body.dark-theme .empty-state-cell div {
            color: #6b7492 !important;
        }

        body.dark-theme .empty-state-cell i {
            color: #2e3349 !important;
        }

        body.dark-theme .text-primary-hr {
            color: #4ade80 !important;
        }

        /* Generic Badge overrides for Dark Mode */
        body.dark-theme .badge-secondary {
            background: #2e3349 !important;
            color: #6b7492 !important;
            border: 1px solid #3b4261 !important;
        }

        body.dark-theme .badge-success {
            background: rgba(11, 170, 75, 0.15) !important;
            color: #4ade80 !important;
            border: 1px solid rgba(11, 170, 75, 0.3) !important;
        }

        body.dark-theme .badge-info {
            background: rgba(14, 165, 233, 0.15) !important;
            color: #7dd3fc !important;
            border: 1px solid rgba(14, 165, 233, 0.3) !important;
        }

        body.dark-theme .badge-warning {
            background: rgba(251, 146, 60, 0.15) !important;
            color: #fb923c !important;
            border: 1px solid rgba(251, 146, 60, 0.3) !important;
        }

        body.dark-theme button[onclick="loadAttendancePrevMonth()"] {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #c3c8da !important;
        }

        body.dark-theme #attendanceStats>div {
            background: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme #attendanceStats #statTotal,
        body.dark-theme #attendanceStats #statOnTime {
            color: #4ade80 !important;
        }

        body.dark-theme #attendanceStats #statLate {
            color: #fb923c !important;
        }

        body.dark-theme #attendanceStats #statEarly {
            color: #60a5fa !important;
        }

        body.dark-theme #attendanceTableWrap thead tr {
            background-color: #21263a !important;
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme #attendanceTableWrap td {
            border-bottom-color: #13161f !important;
        }

        body.dark-theme #attendanceTableWrap tr:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        /* Adjust Back & Edit buttons in profile header */
        body.dark-theme .btn-secondary {
            background: #21263a;
            border-color: #2e3349;
            color: #c3c8da;
        }

        body.dark-theme .profile-info .btn-light {
            background: #1a1d27 !important;
            color: #4ade80 !important;
            border: 1px solid #2e3349 !important;
        }

        /* Modal & Form Dark Mode */
        body.dark-theme .modal-content {
            background-color: #1a1d27;
            color: #e8eaf0;
            border: 1px solid #2e3349;
        }

        body.dark-theme .modal-header {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .modal-footer {
            background-color: #21263a !important;
            border-top-color: #2e3349 !important;
        }

        body.dark-theme .form-label {
            color: #c3c8da !important;
        }

        body.dark-theme .form-control,
        body.dark-theme .form-select {
            background-color: #13161f !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-control::placeholder {
            color: #4b5563 !important;
        }

        body.dark-theme .input-group-text {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #6b7492 !important;
        }

        body.dark-theme .form-icon {
            color: #4b5563 !important;
        }

        body.dark-theme .toggle-switch-group {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .toggle-switch-group:hover {
            background-color: #2e3349 !important;
        }

        body.dark-theme .toggle-title {
            color: #e8eaf0 !important;
        }

        body.dark-theme .toggle-subtitle {
            color: #6b7492 !important;
        }

        /* Salary Slip Modal specific */
        body.dark-theme .modal-slip-container {
            background-color: #1a1d27 !important;
            border: 1px solid #2e3349 !important;
        }

        body.dark-theme .modal-slip-body {
            background-color: #13161f !important;
        }

        body.dark-theme .modal-slip-header {
            border-bottom: none !important;
        }

        .document-image-link:hover img {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border-color: #0BAA4B !important;
        }
    </style>
@endpush

@section('content')
    <!-- Back Button -->
    @can('Xem Nhân Viên')
        <div style="margin-bottom: 24px;">
            <a href="{{ route('nhan-vien.danh-sach') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách
            </a>
        </div>
    @endcan


    <!-- Tabs Navigation -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab(event, 'basic')">
            <i class="bi bi-person-lines-fill"></i> Thông tin
        </button>
        <button class="tab" onclick="switchTab(event, 'work')">
            <i class="bi bi-briefcase-fill"></i> Công việc
        </button>
        <button class="tab" onclick="switchTab(event, 'relatives')">
            <i class="bi bi-people-fill"></i> Người phụ thuộc
        </button>
        <button class="tab" onclick="switchTab(event, 'salary')">
            <i class="bi bi-cash-coin"></i> Lương
        </button>
        <button class="tab" onclick="switchTab(event, 'contracts')">
            <i class="bi bi-file-earmark-text-fill"></i> Hợp đồng
            @if($employee->hopDongs->isNotEmpty())
                <span class="badge"
                    style="background:#0BAA4B;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">
                    {{ $employee->hopDongs->count() }}
                </span>
            @endif
            <button class="tab" onclick="switchTab(event, 'attendance')" data-tab="attendance">
                <i class="bi bi-clock-history"></i> Chấm công
            </button>
            <button class="tab" onclick="switchTab(event, 'asset')">
                <i class="bi bi-pc-display"></i> Tài sản
            </button>
    </div>

    {{-- Tab Contents --}}
    @include('employees.partials.tab_basic')
    @include('employees.partials.tab_work')
    @include('employees.partials.tab_relatives')
    @include('employees.partials.tab_salary')
    @include('employees.partials.tab_contracts')
    @include('employees.partials.tab_attendance')
    @include('employees.partials.tab_asset')

    {{-- Modals --}}
    @include('employees.partials.modal_add_relative')
    @include('employees.partials.modal_salary_slip')

    @push('scripts')
        <script>
            function switchTab(event, tabName) {
                // Disable active state for all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });

                // Disable active state for all tab buttons
                document.querySelectorAll('.tab').forEach(tab => {
                    tab.classList.remove('active');
                });

                // Activate selected content
                const targetContent = document.getElementById('tab-' + tabName);
                if (targetContent) {
                    targetContent.classList.add('active');
                }

                // Activate clicked button
                if (event && event.currentTarget) {
                    event.currentTarget.classList.add('active');
                } else {
                    // Find button by tabName if no event (for auto-switch)
                    const btn = document.querySelector(`.tab[onclick*="'${tabName}'"]`);
                    if (btn) btn.classList.add('active');
                }

                // Update hash without scrolling
                if (history.pushState) {
                    history.pushState(null, null, '#' + tabName);
                } else {
                    location.hash = tabName;
                }
            }

            // Auto-restore tab from hash on page load
            window.addEventListener('DOMContentLoaded', () => {
                const hash = window.location.hash.substring(1);
                if (hash && document.getElementById('tab-' + hash)) {
                    switchTab(null, hash);
                }
            });

            document.getElementById('addRelativeForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnHtml = submitBtn.innerHTML;

                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';

                const formData = new FormData(form);
                fetch('{{ route("nguoi-phu-thuoc.tao") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                borderRadius: '0.5rem',
                            }).then(() => {
                                window.location.hash = 'relatives';
                                location.reload();
                            });
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                            Swal.fire('Lỗi!', data.message || 'Đã có lỗi xảy ra', 'error');
                        }
                    })
                    .catch(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                        Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ', 'error');
                    });
            });

            function deleteRelative(id) {
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn không thể hoàn tác hành động này!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/nguoi-phu-thuoc/xoa/${id}`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Đã xóa!', data.message, 'success').then(() => {
                                        window.location.hash = 'relatives';
                                        location.reload();
                                    });
                                }
                            });
                    }
                });
            }

            function approveRelative(id) {
                Swal.fire({
                    title: 'Duyệt người phụ thuộc?',
                    text: 'Xác nhận thông tin người thân này đã hợp lệ. Bạn có thể nhập ghi chú thêm (không bắt buộc):',
                    icon: 'question',
                    input: 'textarea',
                    inputPlaceholder: 'Nhập ghi chú tại đây...',
                    showCancelButton: true,
                    confirmButtonColor: '#0BAA4B',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Duyệt ngay',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/nguoi-phu-thuoc/duyet/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ GhiChu: result.value })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Thành công!', data.message, 'success').then(() => {
                                        window.location.hash = 'relatives';
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Lỗi!', data.message, 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ', 'error');
                            });
                    }
                });
            }

            function rejectRelative(id) {
                Swal.fire({
                    title: 'Từ chối người phụ thuộc?',
                    text: 'Vui lòng nhập lý do từ chối để nhân viên được biết:',
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Lý do từ chối (bắt buộc)...',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Bạn phải nhập lý do từ chối!';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Từ chối',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/nguoi-phu-thuoc/tu-choi/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ GhiChu: result.value })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Đã từ chối!', data.message, 'success').then(() => {
                                        window.location.hash = 'relatives';
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Lỗi!', data.message, 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ', 'error');
                            });
                    }
                });
            }

            // ===== MODAL PHIẾU LƯƠNG =====
            const slipModal = document.getElementById('slipModal');
            const slipContent = document.getElementById('slipContent');
            const btnPrint = document.getElementById('btnPrintSlip');

            const LOADING_HTML = `
                                                                        <div style="text-align:center;padding:48px;color:#6b7280;">
                                                                            <div style="font-size:36px;margin-bottom:10px;">⏳</div>
                                                                            <div style="font-size:14px;">Đang tải phiếu lương...</div>
                                                                        </div>`;

            function openSlipModal(nvId, thang, nam) {
                slipContent.innerHTML = LOADING_HTML;
                slipModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                fetch(`/salary/slip/${nvId}?thang=${thang}&nam=${nam}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.text();
                    })
                    .then(html => { slipContent.innerHTML = html; })
                    .catch(err => {
                        slipContent.innerHTML = `
                                                                                <div style="text-align:center;padding:48px;color:#dc2626;">
                                                                                    <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
                                                                                    <div>Không thể tải phiếu lương.<br><small style="color:#9ca3af;">${err.message}</small></div>
                                                                                </div>`;
                    });
            }

            window.closeSlipModal = function () {
                slipModal.style.display = 'none';
                document.body.style.overflow = '';
                slipContent.innerHTML = LOADING_HTML;
            };

            // Click backdrop để đóng
            slipModal.addEventListener('click', function (e) {
                if (e.target === slipModal) window.closeSlipModal();
            });

            // ESC để đóng
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && slipModal.style.display === 'flex') {
                    window.closeSlipModal();
                }
            });

            // Gắn sự kiện cho tất cả nút phiếu lương
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-show-slip');
                if (btn) {
                    const nvId = btn.dataset.nvId;
                    const thang = btn.dataset.thang;
                    const nam = btn.dataset.nam;
                    openSlipModal(nvId, thang, nam);
                }
            });

            // Nút In phiếu
            btnPrint.addEventListener('click', function () {
                const printWin = window.open('', '_blank', 'width=950,height=700');
                printWin.document.write(`
                                                                            <!DOCTYPE html><html><head>
                                                                            <meta charset="UTF-8">
                                                                            <title>Phiếu Lương</title>
                                                                            <style>
                                                                                body { font-family: Arial, sans-serif; font-size:13px; margin:20px; }
                                                                                @media print { body { margin: 0; } }
                                                                            </style>
                                                                            <\/head><body>${slipContent.innerHTML}<\/body><\/html>`);
                printWin.document.close();
                printWin.focus();
                setTimeout(() => { printWin.print(); }, 500);
            });
            // ===== DATATABLE CHO PHIẾU LƯƠNG =====
            $(document).ready(function () {
                if ($('#salarySlipsTable').length) {
                    const slipsTable = $('#salarySlipsTable').DataTable({
                        pageLength: 5,
                        lengthMenu: [5, 10, 25, 50],
                        language: {
                            "sProcessing": "Đang xử lý...",
                            "sLengthMenu": "Hiển thị _MENU_ mục",
                            "sZeroRecords": "Không tìm thấy dữ liệu",
                            "sInfo": "Đang xem _START_ đến _END_ (Tổng _TOTAL_)",
                            "sInfoEmpty": "Không có dữ liệu",
                            "sInfoFiltered": "(lọc từ _MAX_ mục)",
                            "sSearch": "Tìm kiếm:",
                            "oPaginate": {
                                "sFirst": "Đầu",
                                "sPrevious": "Trước",
                                "sNext": "Tiếp",
                                "sLast": "Cuối"
                            }
                        },
                        dom: 'rtip',
                        order: [] // Giữ nguyên thứ tự từ server
                    });

                    // Custom filtering logic for Month and Year
                    $.fn.dataTable.ext.search.push(
                        function (settings, data, dataIndex) {
                            if (settings.nTable.id !== 'salarySlipsTable') return true;

                            const row = $(slipsTable.row(dataIndex).node());
                            const m = row.attr('data-month');
                            const y = row.attr('data-year');
                            const targetM = $('#monthFilter').val();
                            const targetY = $('#yearFilter').val();

                            if ((targetM === "" || m === targetM) && (targetY === "" || y === targetY)) {
                                return true;
                            }
                            return false;
                        }
                    );

                    $('#monthFilter, #yearFilter').on('change', function () {
                        slipsTable.draw();
                    });
                }
            });
        </script>
    @endpush
@endsection