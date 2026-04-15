@extends('layouts.app')

@section('title', 'Trang chủ - Cổng thông tin nhân viên')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 36px;">👋</span>
                Xin chào, {{ $nv->Ten }}! 
            </h1>
            <p style="font-size: 16px;">Chào mừng bạn quay lại hệ thống. Chúc bạn một ngày làm việc hiệu quả!</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('nhan-vien.info', $nv->id) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #0BAA4B, #22c55e); border: none; box-shadow: 0 4px 6px rgba(11,170,75,0.2);">
                <i class="bi bi-person-badge"></i> Xem thông tin cá nhân
            </a>
        </div>
    </div>
</div>

{{-- Khối thông báo --}}
@if($latestSalaries && $latestSalaries->count() > 0)
    @php
        $latestSalary = $latestSalaries->first();
    @endphp
    <div style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-left: 4px solid #3b82f6; border-radius: 8px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px; box-shadow: 0 2px 4px rgba(59,130,246,0.1);">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 15px; color: #1e3a8a; font-weight: 600;">Thông báo lương mới</h4>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #1e3a8a;">Phiếu lương tháng <strong>{{ \Carbon\Carbon::parse($latestSalary->ThoiGian)->format('m/Y') }}</strong> của bạn đã có sẵn trên hệ thống.</p>
            </div>
        </div>
        <a href="{{ route('salary.index') }}" style="background: white; color: #3b82f6; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.2s;">
            Xem chi tiết
        </a>
    </div>
@endif

{{-- Thao tác nhanh --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 32px;">
    <a href="{{ route('nghi-phep.ca-nhan') }}" style="text-decoration: none; background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s;">
        <div style="width: 48px; height: 48px; background: #fef2f2; color: #ef4444; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
            <i class="bi bi-calendar2-x"></i>
        </div>
        <div>
            <div style="font-weight: 600; color: #1f2937; font-size: 16px;">Đăng ký nghỉ phép</div>
            <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Tạo đơn xin nghỉ mới</div>
        </div>
    </a>

    <a href="{{ route('wfh.ca-nhan') }}" style="text-decoration: none; background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s;">
        <div style="width: 48px; height: 48px; background: #fffbeb; color: #f59e0b; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
            <i class="bi bi-house-door"></i>
        </div>
        <div>
            <div style="font-weight: 600; color: #1f2937; font-size: 16px;">Đăng ký WFH</div>
            <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Tạo đơn làm việc từ xa</div>
        </div>
    </a>

    <a href="{{ route('cham-cong.ca-nhan') }}" style="text-decoration: none; background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s;">
        <div style="width: 48px; height: 48px; background: #f0fdf4; color: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
            <i class="bi bi-fingerprint"></i>
        </div>
        <div>
            <div style="font-weight: 600; color: #1f2937; font-size: 16px;">Chấm công cá nhân</div>
            <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Xem lịch sử vào/ra</div>
        </div>
    </a>
</div>

{{-- Các con số thống kê --}}
<h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #1f2937;">Thống kê của tôi</h3>
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Ngày WFH tháng này</div>
        <div class="value" style="display: flex; align-items: baseline; gap: 8px;">
            {{ number_format($myWorkFromHomeDaysThisMonth, 1) }}
            <span style="font-size: 15px; font-weight: 500; color: #6b7280;">ngày</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="label">Đơn nghỉ chờ duyệt</div>
        <div class="value" style="color: {{ $myPendingLeaveCount > 0 ? '#ef4444' : '#1f2937' }}">
            {{ $myPendingLeaveCount }}
        </div>
    </div>

    <div class="stat-card">
        <div class="label">Đơn WFH chờ duyệt</div>
        <div class="value" style="color: {{ $myPendingWFHCount > 0 ? '#f59e0b' : '#1f2937' }}">
            {{ $myPendingWFHCount }}
        </div>
    </div>
</div>

<div class="dashboard-grid">
    {{-- Lịch sử lương --}}
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h2 style="font-size: 18px; font-weight: 700; margin: 0;">Lịch sử Lương (6 tháng)</h2>
            <a href="{{ route('salary.history') }}" style="color: #0BAA4B; text-decoration: none; font-size: 13px; font-weight: 500;">Xem tất cả</a>
        </div>
        <div class="table-container">
            <table class="table" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th style="padding: 12px; background: none; border-bottom: 1px solid #e5e7eb;">Tháng</th>
                        <th style="padding: 12px; background: none; border-bottom: 1px solid #e5e7eb; text-align: right;">Thực lĩnh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestSalaries as $salary)
                        <tr>
                            <td style="padding: 12px;">{{ \Carbon\Carbon::parse($salary->ThoiGian)->format('m/Y') }}</td>
                            <td style="padding: 12px; text-align: right; font-weight: 600; color: #0BAA4B;">{{ number_format($salary->Luong) }} ₫</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: #6b7280; padding: 24px;">Chưa có dữ liệu lương</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Đơn từ gần đây --}}
    <div class="card" style="margin-bottom: 0;">
        <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; margin-top: 0;">Đơn từ gần đây</h2>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @php
                $myRecentLogs = collect();
                foreach($recentLeaves as $leave) {
                    $myRecentLogs->push((object)[
                        'type' => 'leave',
                        'date' => $leave->created_at,
                        'title' => 'Nghỉ phép từ ' . \Carbon\Carbon::parse($leave->TuNgay)->format('d/m'),
                        'status' => $leave->TrangThai,
                        'url' => route('nghi-phep.ca-nhan')
                    ]);
                }
                foreach($recentWFHs as $wfh) {
                    $myRecentLogs->push((object)[
                        'type' => 'wfh',
                        'date' => $wfh->created_at,
                        'title' => 'WFH ngày ' . \Carbon\Carbon::parse($wfh->NgayBatDau)->format('d/m'),
                        'status' => $wfh->TrangThai == 'da_duyet' ? 1 : ($wfh->TrangThai == 'tu_choi' ? 0 : 2),
                        'url' => route('wfh.ca-nhan')
                    ]);
                }
                $myRecentLogs = $myRecentLogs->sortByDesc('date')->take(6);
            @endphp
            
            @forelse($myRecentLogs as $log)
                <a href="{{ $log->url }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; border: 1px solid #f3f4f6; transition: background 0.2s;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; background: {{ $log->type == 'leave' ? '#f0fdfa' : '#fffbeb' }}; color: {{ $log->type == 'leave' ? '#0d9488' : '#d97706' }};">
                        <i class="bi {{ $log->type == 'leave' ? 'bi-calendar-minus' : ($log->type == 'wfh' ? 'bi-house-door' : 'bi-clock-history') }}"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: #1f2937; font-size: 14px;">{{ $log->title }}</div>
                        <div style="font-size: 12px; color: #6b7280;">Nộp lúc {{ \Carbon\Carbon::parse($log->date)->format('H:i d/m/Y') }}</div>
                    </div>
                    <div>
                        @if($log->status == 1)
                            <span class="badge badge-success">Đã duyệt</span>
                        @elseif($log->status == 0)
                            <span class="badge badge-danger">Từ chối</span>
                        @else
                            <span class="badge badge-warning">Đang chờ</span>
                        @endif
                    </div>
                </a>
            @empty
                <div style="text-align: center; color: #6b7280; padding: 24px;">Chưa có đơn từ nào gần đây</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .action-buttons a:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(11,170,75,0.2) !important;
    }
</style>
@endsection
