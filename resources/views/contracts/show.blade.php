@extends('layouts.app')

@section('title', 'Chi tiết hợp đồng - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <style>
        .detail-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }

        .detail-section h2 {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        /* Employee Summary Card */
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .summary-avatar {
            width: 80px;
            height: 80px;
            border-radius: 16px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .summary-avatar:hover {
            transform: scale(1.05);
        }

        .summary-info h1 {
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .summary-meta {
            display: flex;
            gap: 16px;
            color: #6b7280;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }

        .salary-value {
            font-weight: 700;
            color: #0BAA4B;
            font-size: 20px;
        }

        .tabs {
            display: flex;
            gap: 24px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .tab {
            padding: 12px 0;
            background: none;
            border: none;
            font-size: 14px;
            font-weight: 600;
            color: #9ca3af;
            cursor: pointer;
            border-bottom: 2px solid transparent;
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

        @media (max-width: 992px) {
            .detail-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .summary-card { flex-direction: column; text-align: center; }
            .detail-grid { grid-template-columns: 1fr; }
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
            padding: 20px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .related-doc-card:hover {
            border-color: #0BAA4B;
            background: #fdfdfd;
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
        }

        .related-doc-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            color: white;
            transition: transform 0.3s;
        }

        .related-doc-card:hover .related-doc-icon {
            transform: scale(1.1);
        }

        .related-doc-info {
            flex: 1;
            min-width: 0;
        }

        .related-doc-action {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: all 0.3s;
        }

        .related-doc-card:hover .related-doc-action {
            background: #0BAA4B;
            color: white;
        }

        .related-doc-bg-icon {
            position: absolute;
            top: 50%;
            right: 60px;
            transform: translateY(-50%);
            opacity: 0.03;
            font-size: 80px;
            pointer-events: none;
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

        /* Timeline Styling */
        .history-timeline {
            position: relative;
            padding: 20px 0;
            margin-left: 20px;
        }

        .history-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        body.dark-theme .history-timeline::before {
            background: #2e3349;
        }

        .timeline-item {
            position: relative;
            padding-left: 32px;
            margin-bottom: 24px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -5px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #0BAA4B;
            z-index: 1;
        }

        .timeline-badge {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .timeline-title {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .timeline-content {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }

        body.dark-theme .timeline-content {
            background: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        .timeline-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #4b5563;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        body.dark-theme .timeline-user {
            color: #8b93a8;
            border-top-color: #2e3349;
        }

        .diff-added { color: #059669; font-weight: 500; }
        .diff-removed { color: #dc2626; text-decoration: line-through; }

        /* Contract Tree Structure */
        .contract-tree {
            padding: 20px 0;
            margin-left: 20px;
            position: relative;
        }

        .tree-root {
            position: relative;
            z-index: 2;
            margin-bottom: 0;
        }

        .tree-node {
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: inline-block;
            min-width: 320px;
            position: relative;
            transition: all 0.3s;
        }

        .tree-node:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #0BAA4B;
        }

        .tree-root .tree-node {
            border-left: 4px solid #0BAA4B;
            background: #fdfdfd;
        }

        .tree-branches {
            position: relative;
            padding-top: 0;
            padding-left: 40px;
            margin-top: -5px; /* Pull up to meet the root connector */
        }

        .tree-branches::before {
            content: '';
            position: absolute;
            left: 0;
            top: -20px;
            bottom: 35px; /* Stop at the last branch item's center */
            width: 3px;
            background: #cbd5e1;
            border-radius: 4px;
        }

        .tree-branch-item {
            position: relative;
            padding: 20px 0;
        }

        .tree-branch-item::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 50%;
            width: 40px;
            height: 3px;
            background: #cbd5e1;
            transform: translateY(-50%);
        }

        .tree-branch-item::after {
            content: '';
            position: absolute;
            left: -44px;
            top: 50%;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e1;
            z-index: 1;
            transform: translateY(-50%);
        }

        .branch-node {
            border-left: 4px solid #3b82f6;
        }

        /* Dark Mode for Tree */
        body.dark-theme .tree-node {
            background-color: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .tree-root .tree-node {
            background-color: rgba(11, 170, 75, 0.05);
        }

        body.dark-theme .tree-branches::before,
        body.dark-theme .tree-branch-item::before,
        body.dark-theme .tree-branch-item::after {
            background: #3d445e;
        }

        .node-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .node-title {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        body.dark-theme .node-title {
            color: #e8eaf0;
        }

        .node-subtitle {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
        }

        body.dark-theme .node-subtitle {
            color: #8b93a8;
        }

        .node-badge {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
@endpush

@section('content')
    <!-- Back Button & Actions -->
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        @can('Xem Danh Sách Hợp Đồng')
        <a href="{{ route('hop-dong.danh-sach') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại danh sách
        </a>
        @endcan
        <div style="display: flex; gap: 12px;">
            @can('Sửa Hợp Đồng')
            <a href="{{ route('hop-dong.suaView', $hopDong->id) }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
            @endcan
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

    <!-- Employee Summary Card -->
    <div class="summary-card">
        @php
            $avatar = $hopDong->nhanVien->AnhDaiDien
                ? asset($hopDong->nhanVien->AnhDaiDien)
                : 'https://ui-avatars.com/api/?name=' . urlencode($hopDong->nhanVien->Ten) . '&background=f3f4f6&color=1f2937&size=128';
        @endphp
        <a href="{{ route('nhan-vien.info', $hopDong->NhanVienId) }}" title="Xem chi tiết nhân viên">
            <img src="{{ $avatar }}" alt="{{ $hopDong->nhanVien->Ten }}" class="summary-avatar">
        </a>
        <div class="summary-info">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <span class="badge badge-info" style="text-transform: none; letter-spacing: 0;">
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
                    <span class="badge badge-warning">Sắp hết hạn</span>
                @elseif($hopDong->TrangThai == 1)
                    <span class="badge badge-success">Đang hiệu lực</span>
                @elseif($hopDong->TrangThai == 0)
                    <span class="badge badge-danger">Hết hiệu lực</span>
                @else
                    <span class="badge badge-warning">Bị hủy</span>
                @endif
            </div>
            <a href="{{ route('nhan-vien.info', $hopDong->NhanVienId) }}" style="text-decoration: none;">
                <h1>{{ $hopDong->nhanVien->Ten }}</h1>
            </a>
            <div class="summary-meta">
                <span><i class="bi bi-briefcase"></i> {{ $hopDong->chucVu->Ten ?? 'N/A' }}</span>
                <span><i class="bi bi-building"></i> {{ $hopDong->phongBan->Ten ?? 'N/A' }}</span>
                <span><i class="bi bi-person-badge"></i> {{ $hopDong->nhanVien->Ma ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab('general')">Thông tin hợp đồng & Lương</button>
        <button class="tab" onclick="switchTab('annex')">Phụ lục hợp đồng</button>
    </div>

    <!-- Tab Content: General & Salary -->
    <div class="tab-content active" id="tab-general">
        <div class="detail-section">
            <h2>
                <i class="bi bi-file-earmark-text"></i>
                Chi tiết điều khoản & Công việc
            </h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Số hợp đồng</div>
                    <div class="detail-value" style="font-weight: 500;">{{ $hopDong->SoHopDong }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Số ngày phép năm</div>
                    <div class="detail-value" style="font-weight: 500;">{{ $hopDong->NgayPhepNam ?? 12 }} ngày/năm</div>
                </div>
                @if($hopDong->NgayPhepKhaDung)
                <div class="detail-item">
                    <div class="detail-label">Phép khả dụng năm nay</div>
                    <div class="detail-value" style="font-weight: 500; color: #0BAA4B;">{{ $hopDong->NgayPhepKhaDung }} ngày</div>
                </div>
                @endif
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



        <!-- Merged Salary Sections -->
        <div class="detail-section">
            <h2>
                <i class="bi bi-cash-stack"></i>
                Chế độ lương & Thu nhập
            </h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Lương cơ bản</div>
                    <div class="salary-value">{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tổng phụ cấp</div>
                    @php
                        $tongPhuCap = collect($hopDong->PhuCap ?? [])->sum('amount');
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
            <h2><i class="bi bi-list-check"></i> Chi tiết phụ cấp</h2>
            <div class="detail-grid">
                @if(!empty($hopDong->PhuCap))
                    @foreach($hopDong->PhuCap as $pc)
                        <div class="detail-item">
                            <div class="detail-label">{{ $pc['name'] }}</div>
                            <div class="detail-value" style="font-weight: 500;">{{ number_format($pc['amount'], 0, ',', '.') }} VNĐ</div>
                        </div>
                    @endforeach
                @endif
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

        @if($laborContract || $ndaContract || $hopDong->appendices->isNotEmpty())
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
                        <a href="{{ route('hop-dong.print', $laborContract->id) }}" target="_blank" class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="related-doc-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="related-doc-info">
                                <div class="related-doc-title">Hợp đồng lao động</div>
                                <div class="related-doc-subtitle">{{ $laborContract->SoHopDong }}</div>
                            </div>
                            <div class="related-doc-action" title="In Hợp đồng">
                                <i class="bi bi-printer"></i>
                            </div>
                        </a>
                    @endif

                    @if($ndaContract)
                        <a href="{{ route('hop-dong.print', $ndaContract->id) }}" target="_blank" class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-shield-lock"></i></div>
                            <div class="related-doc-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div class="related-doc-info">
                                <div class="related-doc-title">Thỏa thuận NDA</div>
                                <div class="related-doc-subtitle">{{ $ndaContract->SoHopDong }}</div>
                            </div>
                            <div class="related-doc-action" title="In NDA">
                                <i class="bi bi-printer"></i>
                            </div>
                        </a>
                    @endif

                    @php
                        $activePhuLuc = $hopDong->appendices->where('TrangThai', 1)->first() ?? $hopDong->appendices->first();
                    @endphp
                    @if($activePhuLuc)
                        <a href="{{ route('hop-dong.print-phu-luc', $activePhuLuc->id) }}" target="_blank" class="related-doc-card">
                            <div class="related-doc-bg-icon"><i class="bi bi-file-earmark-plus"></i></div>
                            <div class="related-doc-icon" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                                <i class="bi bi-file-earmark-plus"></i>
                            </div>
                            <div class="related-doc-info">
                                <div class="related-doc-title">Phụ lục hiện tại</div>
                                <div class="related-doc-subtitle">{{ $activePhuLuc->SoHopDong }}</div>
                            </div>
                            <div class="related-doc-action" title="In Phụ lục">
                                <i class="bi bi-printer"></i>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Tab Content: Appendices -->
    <div class="tab-content" id="tab-annex">
        <div class="contract-tree">
            <!-- Hợp đồng gốc (Root) -->
            <div class="tree-root">
                <div class="node-label">
                    <i class="bi bi-star-fill" style="color: #f59e0b;"></i>
                    Hợp đồng lao động ban đầu
                </div>
                <div class="tree-node shadow-sm">
                    @if($rootHopDong->TrangThai == 1 && $hopDong->id == $rootHopDong->id)
                        <span class="badge badge-success node-badge">Đang hiệu lực</span>
                    @else
                        <span class="badge badge-secondary node-badge">Hết hiệu lực</span>
                    @endif
                    <div class="node-title">
                        <a href="{{ route('hop-dong.info', $rootHopDong->id) }}" style="text-decoration: none; color: inherit;">
                            {{ $rootHopDong->SoHopDong }}
                        </a>
                    </div>
                    <div class="node-subtitle">
                        <i class="bi bi-calendar-check-fill me-1"></i> Ngày ký: {{ \Carbon\Carbon::parse($rootHopDong->created_at)->format('d/m/Y') }}<br>
                        <i class="bi bi-person-fill me-1"></i> Đại diện: {{ $rootHopDong->nguoiKy->Ten ?? 'Ban Giám đốc' }}
                    </div>
                </div>
            </div>

            <!-- Các nhánh phụ lục (Branches) -->
            <div class="tree-branches">
                @if($rootHopDong->appendices->isNotEmpty())
                    @php
                        $sortedAppendices = $rootHopDong->appendices->sortByDesc('id');
                        $totalPl = $sortedAppendices->count();
                    @endphp
                    @foreach($sortedAppendices as $index => $plLink)
                        @php $pl = $plLink->hopDongPL; @endphp
                        @if($pl)
                        <div class="tree-branch-item">
                            <div class="node-label">
                                <i class="bi bi-file-earmark-plus-fill" style="color: #3b82f6;"></i>
                                {{ $plLink->ten_phu_luc }}
                                @if($pl->id == $hopDong->id && $pl->TrangThai == 1)
                                    <span class="badge badge-info ms-2" style="font-size: 9px; background-color: #0BAA4B; color: white;">Đang xem</span>
                                @elseif($index === 0 && $pl->TrangThai == 1)
                                    <span class="badge badge-info ms-2" style="font-size: 9px;">Mới nhất</span>
                                @endif
                            </div>
                            <div class="tree-node branch-node {{ $pl->id == $hopDong->id ? 'item-active' : '' }}" 
                                 style="{{ $pl->id == $hopDong->id ? 'border-left-color: #0BAA4B; background-color: rgba(11, 170, 75, 0.05);' : '' }}">
                                <div class="node-title">
                                    <a href="{{ route('hop-dong.info', $pl->id) }}" style="text-decoration: none; color: inherit;">
                                        {{ $pl->SoHopDong }}
                                    </a>
                                </div>
                                <div class="node-subtitle">
                                    <div class="d-flex align-items-center gap-3">
                                        <span><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($pl->NgayBatDau)->format('d/m/Y') }}</span>
                                        <span class="text-success fw-bold"><i class="bi bi-cash me-1"></i> {{ number_format($pl->TongLuong, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                    <div class="mt-1" style="font-size: 11px; opacity: 0.8;">
                                        Trạng thái: {{ $pl->TrangThai == 1 ? 'Đang hiệu lực' : 'Đã thay thế' }}
                                    </div>
                                </div>
                                <div style="margin-top: 12px; display: flex; gap: 8px;">
                                    <a href="{{ route('hop-dong.info', $pl->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size: 11px; padding: 2px 10px; border-radius: 20px;">
                                        <i class="bi bi-eye me-1"></i> Chi tiết
                                    </a>
                                    <a href="{{ route('hop-dong.print-phu-luc', $rootHopDong->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 10px; border-radius: 20px;">
                                        <i class="bi bi-printer me-1"></i> In phụ lục
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div style="padding: 40px; text-align: center; color: #9ca3af; font-style: italic; font-size: 14px;">
                        <i class="bi bi-info-circle me-1"></i> Chưa có phụ lục nào cho hợp đồng này.
                    </div>
                @endif
            </div>
        </div>
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
