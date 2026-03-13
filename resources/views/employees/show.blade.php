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
            border-bottom: 2px solid #0F5132;
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
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 15px;
            color: #1f2937;
            font-weight: 400;
        }

        .profile-header {
            background: linear-gradient(135deg, #0F5132 0%, #166534 100%);
            border-radius: 8px;
            padding: 32px;
            margin-bottom: 24px;
            color: white;
        }

        .profile-content {
            display: flex;
            align-items: center;
            gap: 32px;
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
            color: #0F5132;
        }

        .tab.active {
            color: #0F5132;
            border-bottom-color: #0F5132;
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
            color: #0F5132;
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
            color: #166534;
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
            background: linear-gradient(135deg, #0F5132 0%, #166534 100%);
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
            border-color: #0F5132;
            box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1);
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
            border-color: #0F5132;
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
        }
    </style>
@endpush

@section('content')
    <!-- Back Button -->
    <div style="margin-bottom: 24px;">
        <a href="{{ route('nhan-vien.danh-sach') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại danh sách
        </a>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-content">
            @php
                $avatar = $employee->AnhDaiDien
                    ? asset($employee->AnhDaiDien)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($employee->Ten) . '&background=0F5132&color=fff&size=128';
            @endphp
            <img src="{{ $avatar }}" alt="{{ $employee->Ten }}" class="profile-avatar"
                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->Ten) }}&background=0F5132&color=fff&size=128'">
            <div class="profile-info">
                <h1>{{ $employee->Ten }}</h1>
                <div style="font-size: 18px; opacity: 0.9;">
                    {{ $employee->ttCongViec->chucVu->Ten ?? 'Chưa có chức vụ' }} -
                    {{ $employee->ttCongViec->phongBan->Ten ?? 'Chưa có phòng ban' }}
                </div>

                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <i class="bi bi-envelope-fill" style="font-size: 18px;"></i>
                        {{ $employee->Email ?? 'Chưa có' }}
                    </div>
                    <div class="profile-meta-item">
                        <i class="bi bi-telephone-fill" style="font-size: 18px;"></i>
                        {{ $employee->SoDienThoai ?? 'Chưa có' }}
                    </div>
                    @if($employee->ttCongViec && $employee->ttCongViec->LoaiNhanVien !== null)
                        <div class="profile-meta-item">
                            <i class="bi bi-person-badge-fill" style="font-size: 18px;"></i>
                            {{ $employee->ttCongViec->LoaiNhanVien == 1 ? 'Văn phòng' : 'Công nhân' }}
                        </div>
                    @endif
                </div>

                <div class="action-buttons-header">
                    <a href="{{ route('nhan-vien.suaView', $employee->id) }}" class="btn btn-light" style="background: white; border: none; font-weight: 600; color: #0F5132; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        <i class="bi bi-pencil-square"></i>
                        Chỉnh sửa hồ sơ
                    </a>

                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab(event, 'basic')">
            <i class="bi bi-person-lines-fill"></i> Thông tin cơ bản
        </button>
        <button class="tab" onclick="switchTab(event, 'work')">
            <i class="bi bi-briefcase-fill"></i> Thông tin công việc
        </button>
        <button class="tab" onclick="switchTab(event, 'relatives')">
            <i class="bi bi-people-fill"></i> Thân nhân
        </button>
        <button class="tab" onclick="switchTab(event, 'salary')">
            <i class="bi bi-cash-coin"></i> Diễn biến lương
            @if($employee->dienBienLuongs->isNotEmpty())
                <span class="badge" style="background:#0F5132;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">
                    {{ $employee->dienBienLuongs->count() }}
                </span>
            @endif
        </button>
        <button class="tab" onclick="switchTab(event, 'contracts')">
            <i class="bi bi-file-earmark-text-fill"></i> Hợp đồng
            @if($employee->hopDongs->isNotEmpty())
                <span class="badge" style="background:#0F5132;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">
                    {{ $employee->hopDongs->count() }}
                </span>
            @endif
        </button>
    </div>

    {{-- Tab Contents --}}
    @include('employees.partials.tab_basic')
    @include('employees.partials.tab_work')
    @include('employees.partials.tab_relatives')
    @include('employees.partials.tab_salary')
    @include('employees.partials.tab_contracts')

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
                }
            }

            document.getElementById('addRelativeForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('{{ route("than-nhan.tao") }}', {
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
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire('Lỗi!', data.message || 'Đã có lỗi xảy ra', 'error');
                        }
                    })
                    .catch(() => {
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
                        fetch(`/than-nhan/xoa/${id}`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Đã xóa!', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                }
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
        </script>
    @endpush
@endsection