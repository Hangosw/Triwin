@extends('layouts.app')

@section('title', 'Nghỉ phép cá nhân')

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
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--surface);
            padding: 28px;
            border-radius: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle at 100% 0%, var(--secondary-green), transparent 70%);
            opacity: 0.3;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--secondary-green);
        }

        .stat-card .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .stat-card.total .icon-box { background: #ecfdf5; color: #10b981; }
        .stat-card.used .icon-box { background: #fef2f2; color: #ef4444; }
        .stat-card.remaining .icon-box { background: #eff6ff; color: #3b82f6; }

        .stat-card .label {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .stat-card .value {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .stat-card.total .value { color: #059669; }
        .stat-card.used .value { color: #dc2626; }
        .stat-card.remaining .value { color: #2563eb; }

        .stat-card .unit {
            font-size: 16px;
            font-weight: 500;
            margin-left: 4px;
            color: var(--text-muted);
        }

        .card {
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .leave-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .leave-type-item {
            padding: 20px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: var(--transition);
        }

        .leave-type-item:hover {
            background: white;
            border-color: var(--primary-green);
            box-shadow: var(--shadow-sm);
            transform: scale(1.02);
        }

        .leave-type-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .leave-type-value {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .leave-type-value .number {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
        }

        .leave-type-value .label-text {
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
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

        /* Modal & Table styling updates */
        .card-header {
            padding: 24px 32px;
            background: white;
            border-bottom: 1px solid #f3f4f6;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 1000;
            backdrop-filter: blur(8px);
            overflow-y: auto;
            padding: 40px 16px;
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background: white;
            width: 600px;
            max-width: 100%;
            margin: auto;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid #f3f4f6;
            position: relative;
        }

        .modal-header {
            padding: 24px 32px;
            background: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 32px;
        }

        .table thead th {
            background: #f8fafc;
            padding: 16px 24px;
            font-weight: 700;
            font-size: 13px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        .table td {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 15px;
            transition: var(--transition);
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            background: white;
            box-shadow: 0 0 0 4px rgba(11, 170, 75, 0.1);
        }

        select.form-control {
            height: 46.5px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 20px;
            padding-right: 44px;
        }

        /* Dark Mode Overrides */
        body.dark-theme {
            --surface: #1a1d2d;
            --text-main: #e8eaf0;
            --text-muted: #8b93a8;
        }

        body.dark-theme #startDate, 
        body.dark-theme #endDate,
        body.dark-theme #leaveDaysDisplay {
            color: #e8eaf0 !important;
        }

        body.dark-theme .stat-card {
            background: #1a1d2d;
            border-color: #2e3349;
        }

        body.dark-theme .stat-card.total .icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        body.dark-theme .stat-card.used .icon-box { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        body.dark-theme .stat-card.remaining .icon-box { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        body.dark-theme .card {
            background: #1a1d2d;
            border-color: #2e3349;
        }

        body.dark-theme .leave-type-item {
            background: #21263a;
            border-color: #2e3349;
        }

        body.dark-theme .leave-type-item:hover {
            background: #1a1d2d;
            border-color: var(--primary-green);
        }

        body.dark-theme .leave-type-value .number {
            color: #fff;
        }

        body.dark-theme .card-header,
        body.dark-theme .modal-header {
            background: #1a1d2d;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .modal-content {
            background: #1a1d2d;
            border-color: #2e3349;
        }

        body.dark-theme .table thead th {
            background: #21263a;
            color: #c3c8da;
            border-color: #2e3349;
        }

        body.dark-theme .table td {
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .form-label {
            color: #c3c8da;
        }

        body.dark-theme .form-control {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-control:focus {
            background: #1a1d2d !important;
            border-color: var(--primary-green) !important;
        }

        body.dark-theme .modal-footer,
        body.dark-theme div[style*="background: #f9fafb"] {
            background: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme #splitLeaveSection {
            background: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        body.dark-theme #splitMessage {
            color: #fbbf24;
        }
    </style>
@endpush

@section('content')
    <div class="page-header" style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; font-weight: 850; letter-spacing: -0.03em;">Nghỉ phép cá nhân</h1>
        <p style="font-size: 16px; color: var(--text-muted);">Quản lý hạn mức và theo dõi lịch sử nghỉ phép của bạn</p>
    </div>

    <style>
        .stats-highlight-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        /* Theme-aware Hero Card */
        .hero-stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
            color: var(--text-main);
            border-radius: 28px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #dbeafe;
            transition: var(--transition);
        }

        .hero-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px -5px rgba(37, 99, 235, 0.15);
            border-color: #3b82f6;
        }

        .hero-stat-card::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .hero-label {
            font-size: 16px;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-value-group {
            position: relative;
            z-index: 1;
        }

        .hero-value {
            font-size: 56px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
            background: linear-gradient(to right, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-unit {
            font-size: 18px;
            font-weight: 500;
            color: #64748b;
            margin-left: 8px;
        }

        .hero-footer {
            margin-top: 24px;
        }

        .usage-progress-container {
            background: #e2e8f0;
            height: 10px;
            border-radius: 10px;
            margin: 12px 0 8px;
            overflow: hidden;
        }

        .usage-progress-bar {
            height: 100%;
            background: linear-gradient(to right, #3b82f6, #2563eb);
            border-radius: 10px;
            transition: width 1s ease-out;
        }

        .usage-stats-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #64748b;
        }

        .secondary-stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .mini-stat-card {
            background: var(--surface);
            padding: 20px 24px;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .mini-stat-card:hover {
            border-color: var(--primary-green);
            background: #f8fafc;
            transform: translateX(8px);
        }

        .mini-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .mini-info .label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .mini-info .value {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Dark Mode fixes */
        body.dark-theme .hero-stat-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-color: #2e3349;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        body.dark-theme .hero-stat-card::after {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        }
        body.dark-theme .hero-label { color: #94a3b8; }
        body.dark-theme .hero-value { background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; }
        body.dark-theme .usage-progress-container { background: rgba(255, 255, 255, 0.1); }
        body.dark-theme .usage-stats-row { color: #94a3b8; }
        body.dark-theme .hero-value-group p { color: #8b93a8 !important; }

        body.dark-theme .mini-stat-card {
            background: #1a1d2d;
            border-color: #2e3349;
        }
        body.dark-theme .mini-stat-card:hover {
            background: #21263a;
        }

        @media (max-width: 992px) {
            .stats-highlight-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $tongPhep = (float)($phepNam->TongPhepDuocNghi ?? 0);
        $daNghi = (float)($phepNam->DaNghi ?? 0);
        $khaDung = (float)($phepNam->KhaDung ?? 0);
        $conLai = (float)($phepNam->ConLai ?? 0);
        $totalEarned = max(0.1, $khaDung + $daNghi);
        $usagePercent = min(100, ($daNghi / $totalEarned) * 100);
    @endphp

    <div class="stats-highlight-container">
        <!-- Hero Card: Available Leave -->
        <div class="hero-stat-card">
            <div class="hero-stat-header">
                <div class="hero-label">
                    <i class="bi bi-rocket-takeoff"></i>
                    Phép khả dụng hiện tại
                </div>
                <div class="badge" style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: none; font-size: 11px;">
                    Tháng {{ now()->month }}/{{ now()->year }}
                </div>
            </div>

            <div class="hero-value-group">
                <div class="hero-value">
                    {{ number_format($khaDung, 1) }}<span class="hero-unit">ngày</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin: 0; font-weight: 500;">Số ngày phép bạn có thể sử dụng để đăng ký nghỉ ngay bây giờ.</p>
            </div>

            <div class="hero-footer">
                <div class="usage-stats-row">
                    <span>Tiến độ sử dụng phép đã tích lũy</span>
                    <span>{{ round($usagePercent) }}%</span>
                </div>
                <div class="usage-progress-container">
                    <div class="usage-progress-bar" style="width: {{ $usagePercent }}%"></div>
                </div>
                <div class="usage-stats-row" style="font-size: 11px;">
                    <span>Đã nghỉ: {{ $daNghi }} ngày</span>
                    <span>Tổng tích lũy: {{ number_format($totalEarned, 1) }} ngày</span>
                </div>
            </div>
        </div>

        <!-- Secondary Info Cards -->
        <div class="secondary-stats-grid">
            <div class="mini-stat-card">
                <div class="mini-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="mini-info">
                    <div class="label">Tổng quỹ phép cả năm</div>
                    <div class="value">{{ number_format($tongPhep, 1) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted)">ngày</span></div>
                </div>
            </div>

            <div class="mini-stat-card">
                <div class="mini-icon" style="background: #fef2f2; color: #ef4444;">
                    <i class="bi bi-calendar-minus"></i>
                </div>
                <div class="mini-info">
                    <div class="label">Tổng số ngày đã nghỉ</div>
                    <div class="value">{{ number_format($daNghi, 1) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted)">ngày</span></div>
                </div>
            </div>

            <div class="mini-stat-card">
                <div class="mini-icon" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="mini-info">
                    <div class="label">Còn lại của năm (Dự kiến)</div>
                    <div class="value">{{ number_format($conLai, 1) }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted)">ngày</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Thống kê các loại nghỉ khác --}}
    <div class="card" style="padding: 32px;">
        <h2 class="section-title">
            <i class="bi bi-grid-1x2 text-primary" style="color: var(--primary-green)"></i>
            Theo dõi các loại nghỉ khác ({{ now()->year }})
        </h2>
        <div class="leave-type-grid">
            @foreach($otherLeaveStats as $stat)
                <div class="leave-type-item">
                    <div class="leave-type-name">{{ $stat['ten'] }}</div>
                    <div class="leave-type-value">
                        <span class="number">{{ number_format($stat['da_dung'], 1) }}</span>
                        @if($stat['co_han_muc'])
                            <span class="label-text">/ {{ number_format($stat['han_muc'], 1) }} ngày</span>
                        @else
                            <span class="label-text">ngày đã dùng</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách đơn nghỉ phép</h3>
            <a href="{{ route('nghi-phep.dang-ky') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Đăng ký nghỉ phép
            </a>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Loại nghỉ</th>
                        <th>Thời gian</th>
                        <th>Số ngày</th>
                        <th>Lý do</th>
                        <th>Người duyệt</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nghiPheps as $index => $np)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-medium">{{ $np->loaiNghiPhep->Ten }}</td>
                            <td>
                                <div>{{ $np->TuNgay->format('d/m/Y') }} @if($np->TuBuoi != 'ca_ngay') <span style="font-size: 11px; color: var(--text-muted);">({{ $np->TuBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</span> @endif</div>
                                <div style="font-size: 10px; color: var(--text-muted); margin: 2px 0;">đến</div>
                                <div>{{ $np->DenNgay->format('d/m/Y') }} @if($np->DenBuoi != 'ca_ngay') <span style="font-size: 11px; color: var(--text-muted);">({{ $np->DenBuoi == 'sang' ? 'Sáng' : 'Chiều' }})</span> @endif</div>
                            </td>
                            <td class="font-medium" style="text-align: center;">{{ number_format((float)$np->SoNgayNghi, 1) }}</td>
                            <td>{{ $np->LyDo }}</td>
                            <td>{{ $np->nguoiDuyet->Ten ?? '-' }}</td>
                            <td>
                                @if($np->TrangThai === 2)
                                    <span class="badge badge-warning">Đang chờ</span>
                                @elseif($np->TrangThai === 1)
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Từ chối</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Bạn chưa có đơn nghỉ phép nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leave Modal -->
    <div class="modal" id="leaveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 20px; font-weight: 700;">Đăng ký nghỉ phép</h2>
                <button onclick="closeLeaveModal()" style="border: none; background: none; cursor: pointer;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="leaveForm" onsubmit="submitLeave(event)">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Loại nghỉ phép <span style="color: #ef4444;">*</span></label>
                        <select class="form-control" name="LoaiNghiPhepId">
                            @foreach($loaiNghiPheps as $type)
                                <option value="{{ $type->id }}">{{ $type->Ten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Từ ngày <span style="color: #ef4444;">*</span></label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <input type="text" class="form-control" id="startDate" name="TuNgay" readonly>
                                <select class="form-control" name="TuBuoi" onchange="calculateDays()">
                                    <option value="ca_ngay">Cả ngày</option>
                                    <option value="sang">Nghỉ Sáng</option>
                                    <option value="chieu">Nghỉ Chiều</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="form-label">Đến ngày <span style="color: #ef4444;">*</span></label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <input type="text" class="form-control" id="endDate" name="DenNgay" readonly>
                                <select class="form-control" name="DenBuoi" onchange="calculateDays()">
                                    <option value="ca_ngay">Cả ngày</option>
                                    <option value="sang">Nghỉ Sáng</option>
                                    <option value="chieu">Nghỉ Chiều</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lý do nghỉ <span style="color: #ef4444;">*</span></label>
                        <textarea class="form-control" name="LyDo" rows="3"
                            placeholder="Nhập lý do nghỉ..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số ngày nghỉ</label>
                        <input type="text" class="form-control" id="leaveDaysDisplay" readonly
                            style="background-color: #f9fafb;">
                    </div>
                    <!-- Split Leave Warning & Type Selection -->
                    <div id="splitLeaveSection" style="display: none; background: #fff7ed; padding: 16px; border-radius: 12px; border: 1px solid #ffedd5; margin-bottom: 24px;">
                        <p id="splitMessage" style="color: #9a3412; font-size: 14px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Quỹ phép năm của bạn không đủ hoặc vượt quá giới hạn mỗi lần sử dụng của hệ thống. Phần dư sẽ được tính vào loại nghỉ thay thế.
                        </p>
                        <label class="form-label">Loại nghỉ thay thế cho phần dư <span style="color: #ef4444;">*</span></label>
                        <select class="form-control no-select2" name="SplitLoaiNghiPhepId" id="splitTypeSelect">
                            <option value="">-- Chọn loại nghỉ --</option>
                            @foreach($loaiNghiPheps as $type)
                                <option value="{{ $type->id }}">{{ $type->Ten }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div
                    style="padding: 16px 24px; background: #f9fafb; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn" style="background: #e5e7eb; color: #374151;"
                        onclick="closeLeaveModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đơn đăng ký</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script>
        const workingSchedule = @json($workingSchedule->keyBy('Thu'));
        const leaveLimitsMap = @json($leaveLimitsMap);
        const annualLeaveLimit = {{ \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5) }};
        const annualLeaveId = {{ $loaiNghiPheps->firstWhere('Ten', 'Nghỉ phép năm')->id ?? 'null' }};
        let startPicker, endPicker;

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr.localize(flatpickr.l10ns.vn);

            startPicker = flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function (selectedDates) {
                    if (endPicker) endPicker.set('minDate', selectedDates[0]);
                    calculateDays();
                }
            });

            endPicker = flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function () {
                    calculateDays();
                }
            });
        });

        function calculateDays() {
            if (!startPicker || !endPicker) return;

            const fromDate = startPicker.selectedDates[0];
            const toDate = endPicker.selectedDates[0];

            if (fromDate && toDate) {
                if (toDate < fromDate) {
                    document.getElementById('leaveDaysDisplay').value = '';
                    return;
                }

                let count = 0;
                let cur = new Date(fromDate);
                cur.setHours(0, 0, 0, 0);
                let to = new Date(toDate);
                to.setHours(0, 0, 0, 0);

                const tuBuoi = document.getElementsByName('TuBuoi')[0].value;
                const denBuoi = document.getElementsByName('DenBuoi')[0].value;

                while (cur <= to) {
                    const dayOfWeek = cur.getDay();
                    const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);

                    if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                        let dayVal = parseFloat(workingSchedule[dbDayOfWeek].CoLamViec);
                        
                        // Xử lý ngày bắt đầu
                        if (cur.getTime() === fromDate.getTime()) {
                            if (tuBuoi === 'sang' || tuBuoi === 'chieu') {
                                dayVal = Math.min(dayVal, 0.5);
                            }
                        }
                        // Xử lý ngày kết thúc (tránh tính 2 lần nếu là cùng 1 ngày)
                        else if (cur.getTime() === toDate.getTime()) {
                            if (denBuoi === 'sang' || denBuoi === 'chieu') {
                                dayVal = Math.min(dayVal, 0.5);
                            }
                        }
                        
                        count += dayVal;
                    }
                    cur.setDate(cur.getDate() + 1);
                }

                // Trường hợp đặc biệt: Cùng 1 ngày
                if (fromDate.getTime() === toDate.getTime()) {
                    count = 0;
                    const dayOfWeek = fromDate.getDay();
                    const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);
                    if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                        if (tuBuoi === 'sang' && denBuoi === 'sang') count = 0.5;
                        else if (tuBuoi === 'chieu' && denBuoi === 'chieu') count = 0.5;
                        else if (tuBuoi === 'sang' && denBuoi === 'chieu') count = 1.0;
                        else count = parseFloat(workingSchedule[dbDayOfWeek].CoLamViec);
                    }
                }

                document.getElementById('leaveDaysDisplay').value = count.toFixed(1) + ' ngày';
                
                // Kiểm tra tách đơn (Dựa trên hạn mức của từng loại nghỉ)
                const typeSelect = document.getElementsByName('LoaiNghiPhepId')[0];
                const splitSection = document.getElementById('splitLeaveSection');
                const splitMessage = document.getElementById('splitMessage');
                const splitTypeSelect = document.getElementById('splitTypeSelect');
                
                const selectedTypeId = typeSelect.value;
                const remainingBalance = leaveLimitsMap[selectedTypeId] !== undefined ? parseFloat(leaveLimitsMap[selectedTypeId]) : 999;
                
                let message = "";
                if (selectedTypeId == annualLeaveId) {
                    const effectiveLimit = Math.min(remainingBalance, annualLeaveLimit);
                    if (count > effectiveLimit) {
                        message = count > remainingBalance 
                            ? 'Quỹ phép năm của bạn không đủ. Phần dư sẽ được tính vào loại nghỉ thay thế.' 
                            : `Số ngày đăng ký vượt quá giới hạn mỗi lần sử dụng của hệ thống (${annualLeaveLimit} ngày). Phần dư sẽ được tính vào loại nghỉ thay thế.`;
                    }
                } else if (remainingBalance < count && remainingBalance !== 999) {
                    message = 'Số ngày đăng ký vượt quá hạn mức tối đa còn lại của loại nghỉ này. Phần dư sẽ được tính vào loại nghỉ thay thế.';
                }
                
                if (message) {
                    splitMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + message;
                    splitSection.style.display = 'block';
                    splitTypeSelect.required = true;

                    // Cập nhật danh sách loại nghỉ thay thế (loại bỏ loại hiện tại và loại hết hạn mức)
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
                    splitTypeSelect.required = false;
                }
            }
        }

        // Thêm listener cho select loại nghỉ chính (hỗ trợ cả Select2)
        $(document).ready(function() {
            $('select[name="LoaiNghiPhepId"]').on('change select2:select', function() {
                calculateDays();
            });
        });

        function openLeaveModal() {
            document.getElementById('leaveModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            calculateDays();
        }

        function closeLeaveModal() {
            document.getElementById('leaveModal').classList.remove('show');
            document.getElementById('leaveForm').reset();
            document.body.style.overflow = '';
        }

        function submitLeave(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Manual Validation
            const loaiNghiPhepId = form.querySelector('[name="LoaiNghiPhepId"]').value;
            const tuNgay = form.querySelector('[name="TuNgay"]').value;
            const denNgay = form.querySelector('[name="DenNgay"]').value;
            const lyDo = form.querySelector('[name="LyDo"]').value;
            const splitSection = document.getElementById('splitLeaveSection');
            const splitTypeSelect = document.getElementById('splitTypeSelect');

            if (!loaiNghiPhepId || !tuNgay || !denNgay || !lyDo.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)'
                });
                return;
            }

            if (splitSection.style.display === 'block' && !splitTypeSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng chọn loại nghỉ thay thế cho phần dư.'
                });
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Đang xử lý...';

            Swal.fire({
                title: 'Đang gửi đơn...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("nghi-phep.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;
                    
                    if (!response.ok) {
                        throw new Error(data?.message || 'Có lỗi xảy ra từ máy chủ (Mã lỗi: ' + response.status + ')');
                    }
                    return data;
                })
                .then(data => {
                    if (data && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data?.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: error.message
                    });
                });
        }
    </script>
@endpush
