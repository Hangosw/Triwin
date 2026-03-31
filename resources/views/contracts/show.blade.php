@extends('layouts.app')

@section('title', 'Chi tiết hợp đồng - Vietnam Rubber Group')

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
            display: flex;
            align-items: center;
            gap: 12px;
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
            background: linear-gradient(135deg, #0BAA4B 0%, #088c3d 100%);
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
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }

        .profile-info h1 {
            font-size: 28px;
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
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .salary-value {
            font-weight: 700;
            color: #0BAA4B;
            font-size: 18px;
        }

        .secondary-text {
            color: #6b7280;
        }

        body.dark-theme .secondary-text {
            color: #8b93a8 !important;
        }

        .file-box {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .file-box:hover {
            border-color: #0BAA4B;
            background: #f0fdf4;
        }

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
            font-size: 15px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
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

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
                text-align: center;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Dark Theme Overrides */
        body.dark-theme .detail-section {
            background-color: #1a1d27;
            border-color: #2e3349;
            color: #e8eaf0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
        }

        body.dark-theme .detail-section h2 {
            color: #e8eaf0;
            border-bottom-color: #2e3349;
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
            color: #8b93a8;
        }

        body.dark-theme .tab:hover {
            color: #0BAA4B;
        }

        body.dark-theme .tab.active {
            color: #0BAA4B;
        }

        body.dark-theme .file-box {
            background-color: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .file-box:hover {
            background-color: rgba(11, 170, 75, 0.1);
            border-color: #0BAA4B;
        }

        body.dark-theme .file-icon-bg {
            background-color: #1a1d27 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }

        /* Related Docs Cards */
        .related-doc-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .related-doc-card:hover {
            border-color: #0BAA4B;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .related-doc-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            color: white;
        }

        .related-doc-bg-icon {
            position: absolute;
            top: -10px;
            right: -10px;
            opacity: 0.05;
            font-size: 60px;
        }

        .related-doc-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .related-doc-subtitle {
            font-size: 11px;
            color: #6b7280;
            font-family: monospace;
        }

        .related-doc-btns {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .btn-center {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
        }

        /* Dark Mode for Related Docs */
        body.dark-theme .related-doc-card {
            background-color: #21263a;
            border-color: #2e3349;
        }

        body.dark-theme .related-doc-card:hover {
            border-color: #0BAA4B;
            background-color: rgba(11, 170, 75, 0.05);
        }

        body.dark-theme .related-doc-title {
            color: #e8eaf0;
        }

        body.dark-theme .related-doc-subtitle {
            color: #8b93a8;
        }

        body.dark-theme .related-doc-bg-icon {
            color: #ffffff;
            opacity: 0.03;
        }
    </style>
@endpush

@section('content')
    <!-- Back Button & Actions -->
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('hop-dong.danh-sach') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại danh sách
        </a>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('hop-dong.suaView', $hopDong->id) }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
            @if(!str_starts_with($hopDong->Loai ?? '', 'nda'))
                <a href="{{ route('hop-dong.download-word', $hopDong->id) }}" class="btn btn-success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Tải Word
                </a>
            @endif
            <a href="{{ route('hop-dong.print', $hopDong->id) }}" target="_blank" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                In hợp đồng
            </a>
        </div>
    </div>

    <!-- Contract Header -->
    <div class="profile-header">
        <div class="profile-content">
            @php
                $avatar = $hopDong->nhanVien->AnhDaiDien
                    ? asset($hopDong->nhanVien->AnhDaiDien)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($hopDong->nhanVien->Ten) . '&background=ffffff&color=0F5132&size=128';
            @endphp
            <img src="{{ $avatar }}" alt="{{ $hopDong->nhanVien->Ten }}" class="profile-avatar">
            <div class="profile-info">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                    <span class="badge"
                        style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                        {{ $hopDong->SoHopDong }}
                    </span>
                    @php
                        $isExpiringSoon = false;
                        if ($hopDong->NgayKetThuc && $hopDong->TrangThai == 1) {
                            $today = \Carbon\Carbon::today();
                            $endDate = \Carbon\Carbon::parse($hopDong->NgayKetThuc);
                            $diff = $today->diffInDays($endDate, false);
                            if ($diff >= 0 && $diff <= 25) {
                                $isExpiringSoon = true;
                            }
                        }
                    @endphp

                    @if($isExpiringSoon)
                        <span class="badge badge-warning"
                            style="background: #ffffff; color: #92400e; display: flex; align-items: center; gap: 6px;">
                            Sắp hết hạn
                            <i class="bi bi-arrow-repeat" style="font-size: 14px;"></i>
                        </span>
                    @elseif($hopDong->TrangThai == 1)
                        <span class="badge badge-success" style="background: #ffffff; color: #065f46;">Đang hiệu lực</span>
                    @elseif($hopDong->TrangThai == 0)
                        <span class="badge badge-danger" style="background: #ffffff; color: #991b1b;">Hết hiệu lực</span>
                    @else
                        <span class="badge badge-warning" style="background: #ffffff; color: #92400e;">Bị hủy</span>
                    @endif
                </div>
                <h1>{{ $hopDong->nhanVien->Ten }}</h1>
                <div style="opacity: 0.9; font-size: 16px;">
                    <i class="bi bi-briefcase-fill"></i> {{ $hopDong->chucVu->Ten ?? 'N/A' }} |
                    <i class="bi bi-building"></i> {{ $hopDong->phongBan->Ten ?? 'N/A' }}
                </div>

                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <i class="bi bi-file-earmark-text"></i>
                        @php
                            $loai = $hopDong->Loai;
                            $label = $hopDong->loaiHopDong->TenLoai ?? $loai;
                            $textColor = '#1e40af'; // Default info color

                            if ($loai === 'thu_viec') {
                                $label = 'HĐ thử việc';
                                $textColor = '#92400e';
                            } elseif ($loai === 'chinh_thuc_xac_dinh_thoi_han') {
                                $label = 'HĐ chính thức (XĐTH)';
                                $textColor = '#065f46';
                            } elseif ($loai === 'chinh_thuc_khong_xac_dinh_thoi_han') {
                                $label = 'HĐ chính thức (KXĐTH)';
                                $textColor = '#065f46';
                            } elseif ($loai === 'khoan_viec') {
                                $label = 'HĐ khoán việc';
                                $textColor = '#1e40af';
                            } elseif ($loai === 'thoi_vu') {
                                $label = 'HĐ thời vụ';
                                $textColor = '#374151';
                            } elseif (str_starts_with($loai ?? '', 'nda')) {
                                $label = 'Thỏa thuận bảo mật (NDA)';
                                $textColor = '#7c3aed';
                            }
                        @endphp
                        Loại: <span class="badge"
                            style="background: white; color: {{ $textColor }}; margin-left:8px;">{{ $label }}</span>
                    </div>
                    <div class="profile-meta-item">
                        <i class="bi bi-calendar-check"></i>
                        Bắt đầu: {{ \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') }}
                    </div>
                    <div class="profile-meta-item">
                        <i class="bi bi-calendar-x"></i>
                        Kết thúc:
                        {{ $hopDong->NgayKetThuc ? \Carbon\Carbon::parse($hopDong->NgayKetThuc)->format('d/m/Y') : 'Không thời hạn' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab('general')">Thông tin hợp đồng</button>
        <button class="tab" onclick="switchTab('salary')">Lương & Phụ cấp</button>
        <button class="tab" onclick="switchTab('history')">Lịch sử thay đổi</button>
    </div>

    <!-- Tab Content: General -->
    <div class="tab-content active" id="tab-general">
        <div class="detail-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Chi tiết các điều khoản
            </h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Số hợp đồng</div>
                    <div class="detail-value" style="font-weight: 500;">{{ $hopDong->SoHopDong }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Loại hợp đồng</div>
                    <div class="detail-value" style="font-weight: 500;">
                        @if($hopDong->Loai == 'chinh_thuc' || $hopDong->Loai == 'chinh_thuc_xac_dinh_thoi_han') Hợp đồng
                            chính thức (XĐTH)
                        @elseif($hopDong->Loai == 'chinh_thuc_khong_xac_dinh_thoi_han') Hợp đồng chính thức (KXĐTH)
                        @elseif($hopDong->Loai == 'thu_viec') Hợp đồng thử việc
                        @elseif($hopDong->Loai == 'khoan_viec') Hợp đồng khoán việc
                        @elseif($hopDong->Loai == 'thoi_vu') Hợp đồng thời vụ
                        @elseif(str_starts_with($hopDong->Loai ?? '', 'nda')) Thỏa thuận bảo mật (NDA)
                        @else {{ $hopDong->Loai }} @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Hình thức</div>
                    <div class="detail-value" style="font-weight: 500;">
                        @if($hopDong->Loai == 'chinh_thuc' || $hopDong->Loai == 'chinh_thuc_xac_dinh_thoi_han') Hợp đồng
                            chính thức (XĐTH)
                        @elseif($hopDong->Loai == 'chinh_thuc_khong_xac_dinh_thoi_han') Hợp đồng chính thức (KXĐTH)
                        @elseif($hopDong->Loai == 'thu_viec') Hợp đồng thử việc
                        @elseif($hopDong->Loai == 'khoan_viec') Hợp đồng khoán việc
                        @elseif($hopDong->Loai == 'thoi_vu') Hợp đồng thời vụ
                        @elseif(str_starts_with($hopDong->Loai ?? '', 'nda')) Thỏa thuận bảo mật (NDA)
                        @else {{ $hopDong->Loai }} @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Ngày ký</div>
                    <div class="detail-value" style="font-weight: 500;">
                        {{ \Carbon\Carbon::parse($hopDong->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Người ký</div>
                    <div class="detail-value" style="font-weight: 500;">{{ $hopDong->nguoiKy->Ten ?? 'Lãnh đạo đơn vị' }}
                    </div>
                </div>
            </div>
        </div>

        @if($hopDong->File)
            <div class="detail-section">
                <h2>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Tệp đính kèm
                </h2>
                <a href="{{ asset($hopDong->File) }}" target="_blank" class="file-box"
                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 16px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div class="file-icon-bg"
                        style="background: #f9fafb; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <svg fill="none" stroke="#D92D20" viewBox="0 0 24 24" style="width: 32px; height: 32px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 500;" class="detail-value" style="font-weight: 500;">Tài liệu hợp đồng</div>
                        <div style="font-size: 13px;" class="secondary-text">Click để xem hoặc tải về</div>
                    </div>
                    <div style="margin-left: auto;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="width: 20px; height: 20px; color: #9ca3af;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                </a>
            </div>
        @endif

        @if($laborContract || $ndaContract || $hopDong->phuLucs->isNotEmpty())
            <div class="detail-section">
                <h2 style="border-bottom-color: #7c3aed;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 24px; height: 24px; color: #7c3aed;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Tài liệu liên quan
                </h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    @if($laborContract)
                        <div class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="related-doc-icon" style="background: #0BAA4B;"><i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="related-doc-title">Hợp đồng lao động</div>
                                    <div class="related-doc-subtitle">{{ $laborContract->SoHopDong }}</div>
                                </div>
                            </div>
                            <div class="related-doc-btns">
                                <a href="{{ route('hop-dong.info', $laborContract->id) }}"
                                    class="btn btn-sm btn-secondary btn-center"
                                    style="flex: 1; font-size: 11px; display: flex; justify-content: center; align-items: center;">Chi
                                    tiết</a>
                                <a href="{{ route('hop-dong.print', $laborContract->id) }}" target="_blank"
                                    class="btn btn-sm btn-primary btn-center"
                                    style="flex: 1.2; font-size: 11px; background-color: #0BAA4B; border-color: #0BAA4B; display: flex; justify-content: center; align-items: center;">
                                    <i class="bi bi-printer"></i> In HĐLĐ
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($ndaContract)
                        <div class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-shield-lock"></i></div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="related-doc-icon" style="background: #7c3aed;"><i class="bi bi-shield-lock"></i></div>
                                <div style="min-width: 0;">
                                    <div class="related-doc-title">Thỏa thuận NDA</div>
                                    <div class="related-doc-subtitle">{{ $ndaContract->SoHopDong }}</div>
                                </div>
                            </div>
                            <div class="related-doc-btns">
                                <a href="{{ route('hop-dong.info', $ndaContract->id) }}" class="btn btn-sm btn-secondary btn-center"
                                    style="flex: 1; font-size: 11px; display: flex; justify-content: center; align-items: center;">Chi
                                    tiết</a>
                                <a href="{{ route('hop-dong.print', $ndaContract->id) }}" target="_blank"
                                    class="btn btn-sm btn-primary btn-center"
                                    style="flex: 1.2; font-size: 11px; background-color: #7c3aed; border-color: #7c3aed; display: flex; justify-content: center; align-items: center;">
                                    <i class="bi bi-printer"></i> In NDA
                                </a>
                            </div>
                        </div>
                    @endif

                    @php
                        $activePhuLuc = $hopDong->phuLucs->where('TrangThai', 1)->first() ?? $hopDong->phuLucs->first();
                    @endphp
                    @if($activePhuLuc)
                        <div class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-file-earmark-plus"></i></div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="related-doc-icon" style="background: #0284c7;"><i class="bi bi-file-earmark-plus"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <div class="related-doc-title">Phụ lục hiện tại</div>
                                    <div class="related-doc-subtitle">Kèm HĐ: {{ $hopDong->SoHopDong }}</div>
                                </div>
                            </div>
                            <div class="related-doc-btns">
                                <button onclick="switchTab('history')" class="btn btn-sm btn-secondary btn-center"
                                    style="flex: 1; font-size: 11px; display: flex; justify-content: center; align-items: center;">Lịch
                                    sử</button>
                                <a href="{{ route('hop-dong.print-phu-luc', $hopDong->id) }}" target="_blank"
                                    class="btn btn-sm btn-primary btn-center"
                                    style="flex: 1.2; font-size: 11px; background-color: #0284c7; border-color: #0284c7; display: flex; justify-content: center; align-items: center;">
                                    <i class="bi bi-printer"></i> In Phụ lục
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Tab Content: Salary -->
    <div class="tab-content" id="tab-salary">
        <div class="detail-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Chế độ lương
            </h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Lương cơ bản</div>
                    <div class="salary-value">{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tổng phụ cấp</div>
                    @php
                        $tongPhuCap = $hopDong->phuCaps->sum('pivot.so_tien');
                    @endphp
                    <div class="salary-value secondary-text">{{ number_format($tongPhuCap, 0, ',', '.') }} VNĐ
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tổng thu nhập</div>
                    <div class="salary-value" style="font-size: 24px; color: #0BAA4B;">
                        {{ number_format($hopDong->TongLuong, 0, ',', '.') }} VNĐ
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h2>Chi tiết phụ cấp</h2>
            <div class="detail-grid">
                @foreach($allAllowances as $allowance)
                    @php
                        $contractAllowance = $hopDong->phuCaps->where('id', $allowance->id)->first();
                        $amount = $contractAllowance ? $contractAllowance->pivot->so_tien : 0;
                    @endphp
                    <div class="detail-item">
                        <div class="detail-label">{{ $allowance->noi_dung }}</div>
                        <div class="detail-value" style="font-weight: 500;">{{ number_format($amount, 0, ',', '.') }} VNĐ</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tab Content: History -->
    <div class="tab-content" id="tab-history">
        @if($hopDong->phuLucs->isEmpty())
            <div class="detail-section" style="text-align: center; padding: 48px;">
                <div style="font-size: 48px; color: #e5e7eb; margin-bottom: 16px;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="secondary-text">Chưa có lịch sử thay đổi nào được ghi nhận cho hợp đồng này.</div>
            </div>
        @else
            @foreach($hopDong->phuLucs as $pl)
                <div class="detail-section">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <h2 style="margin-bottom: 4px; border-bottom: none; padding-bottom: 0;">
                                <i class="bi bi-file-earmark-diff"></i>
                                {{ $pl->ten_phu_luc }}
                            </h2>
                            <div class="secondary-text" style="font-size: 13px;">
                                <i class="bi bi-calendar3"></i> Ngày cập nhật:
                                {{ \Carbon\Carbon::parse($pl->created_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @if($pl->TrangThai == 1)
                            <span class="badge"
                                style="background: #ecfdf5; color: #065f46; border: 1px solid #10b981; padding: 6px 12px; border-radius: 6px;">
                                <i class="bi bi-check-circle-fill"></i> Mới nhất (Đang hiệu lực)
                            </span>
                        @else
                            <span class="badge"
                                style="background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db; padding: 6px 12px; border-radius: 6px;">
                                <i class="bi bi-archive"></i> Lịch sử
                            </span>
                        @endif
                    </div>

                    <div class="detail-grid">
                        @foreach($pl->dieuKhoans as $dk)
                            <div class="detail-item">
                                <div class="detail-label">{{ $dk->noi_dung }}</div>
                                <div class="detail-value" style="font-weight: 500;">
                                    {{ number_format($dk->pivot->so_tien, 0, ',', '.') }} VNĐ</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @push('scripts')
        <script>
            function switchTab(tabName) {
                document.querySelectorAll('.tab-content').forEach(c => {
                    c.classList.remove('active');
                    c.style.display = 'none';
                });
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

                const activeTab = document.getElementById('tab-' + tabName);
                if (activeTab) {
                    activeTab.classList.add('active');
                    activeTab.style.display = 'block';
                }
                event.currentTarget.classList.add('active');
            }
        </script>
    @endpush
@endsection