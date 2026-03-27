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
            <a href="{{ route('hop-dong.download-word', $hopDong->id) }}" class="btn btn-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Tải Word
            </a>
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
                        <span class="badge badge-warning" style="background: #ffffff; color: #92400e; display: flex; align-items: center; gap: 6px;">
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
                    <div class="detail-value">{{ $hopDong->SoHopDong }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Loại hợp đồng</div>
                    <div class="detail-value">{{ $hopDong->loaiHopDong->TenLoai ?? 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Hình thức</div>
                    <div class="detail-value">
                        @if($hopDong->Loai == 'chinh_thuc' || $hopDong->Loai == 'chinh_thuc_xac_dinh_thoi_han') Chính thức
                            (Xác định thời hạn)
                        @elseif($hopDong->Loai == 'chinh_thuc_khong_xac_dinh_thoi_han') Chính thức (Không xác định thời hạn)
                        @elseif($hopDong->Loai == 'thu_viec') Thử việc
                        @elseif($hopDong->Loai == 'khoan_viec') Khoán việc
                        @elseif($hopDong->Loai == 'thoi_vu') Thời vụ
                        @else {{ $hopDong->Loai }} @endif
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Ngày ký</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($hopDong->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Người ký</div>
                    <div class="detail-value">{{ $hopDong->nguoiKy->Ten ?? 'Lãnh đạo đơn vị' }}</div>
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
                    style="text-decoration: none; color: inherit;">
                    <div style="background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <svg fill="none" stroke="#D92D20" viewBox="0 0 24 24" style="width: 32px; height: 32px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div style="font-weight: 500; color: #1f2937;">Tài liệu hợp đồng</div>
                        <div style="font-size: 13px; color: #6b7280;">Click để xem hoặc tải về</div>
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
                        $tongPhuCap = ($hopDong->PhuCapChucVu ?? 0) + ($hopDong->PhuCapTrachNhiem ?? 0) +
                            ($hopDong->PhuCapDocHai ?? 0) + ($hopDong->PhuCapThamNien ?? 0) +
                            ($hopDong->PhuCapKhuVuc ?? 0) + ($hopDong->PhuCapAnTrua ?? 0) +
                            ($hopDong->PhuCapXangXe ?? 0) + ($hopDong->PhuCapDienThoai ?? 0) +
                            ($hopDong->PhuCapKhac ?? 0);
                    @endphp
                    <div class="salary-value" style="color: #6b7280;">{{ number_format($tongPhuCap, 0, ',', '.') }} VNĐ
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
                <div class="detail-item">
                    <div class="detail-label">PC Chức vụ</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapChucVu ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Trách nhiệm</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapTrachNhiem ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Độc hại</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapDocHai ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Thâm niên</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapThamNien ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Khu vực</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapKhuVuc ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Ăn trưa</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapAnTrua ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Xăng xe</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapXangXe ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PC Điện thoại</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapDienThoai ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phụ cấp khác</div>
                    <div class="detail-value">{{ number_format($hopDong->PhuCapKhac ?? 0, 0, ',', '.') }} VNĐ</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function switchTab(tabName) {
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

                document.getElementById('tab-' + tabName).classList.add('active');
                event.target.classList.add('active');
            }
        </script>
    @endpush
@endsection
