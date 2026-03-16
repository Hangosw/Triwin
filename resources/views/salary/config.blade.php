@extends('layouts.app')

@section('title', 'Diễn biến lương – ' . $nhanVien->Ten)

@push('styles')
<style>
    .detail-section {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .detail-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .salary-component {
        background: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        border-left: 4px solid #0BAA4B;
    }
    .salary-structure-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }
    .component-label { font-size: 13px; color: #6b7280; margin-bottom: 8px; }
    .component-value { font-size: 18px; font-weight: 600; color: #0BAA4B; }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .info-item > div:first-child { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
    .info-item > div:last-child  { font-size: 16px; font-weight: 600; }
    .empty-state { text-align: center; padding: 56px 24px; color: #9ca3af; }
    .empty-state i { font-size: 56px; display: block; margin-bottom: 16px; opacity: 0.4; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
    <div>
        <h1>Diễn biến lương</h1>
        <p>{{ $nhanVien->Ma }} &mdash; {{ $nhanVien->Ten }}</p>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

{{-- ===== Thông tin nhân viên ===== --}}
<div class="detail-section">
    <h2><i class="bi bi-person-badge" style="color:#0BAA4B;"></i> Thông tin nhân viên</h2>
    <div class="info-grid">
        <div class="info-item">
            <div>Mã nhân viên</div>
            <div>{{ $nhanVien->Ma }}</div>
        </div>
        <div class="info-item">
            <div>Họ và tên</div>
            <div>{{ $nhanVien->Ten }}</div>
        </div>
        <div class="info-item">
            <div>Chức vụ</div>
            <div>{{ $nhanVien->ttCongViec?->chucVu?->Ten ?? '–' }}</div>
        </div>
        <div class="info-item">
            <div>Phòng ban</div>
            <div>{{ $nhanVien->ttCongViec?->phongBan?->Ten ?? '–' }}</div>
        </div>
    </div>
</div>

{{-- ===== Diễn biến lương hiện tại ===== --}}
<div class="detail-section">
    <h2><i class="bi bi-graph-up-arrow" style="color:#0BAA4B;"></i> Diễn biến lương hiện tại</h2>

    @if ($dienBienHienTai)
        @php
            $ngach    = $dienBienHienTai->ngachLuong;
            $bac      = $dienBienHienTai->bacLuong;
            $luongNgach = $bac ? $bac->HeSo * $mucLuongCoSo : 0;
            $vuotKhung  = $luongNgach * (($dienBienHienTai->PhuCapVuotKhung ?? 0) / 100);
        @endphp

        <div class="info-grid">
            <div class="info-item">
                <div>Mã ngạch</div>
                <div>{{ $ngach?->Ma ?? '–' }}</div>
            </div>
            <div class="info-item">
                <div>Tên ngạch</div>
                <div>{{ $ngach?->Ten ?? '–' }}</div>
            </div>
            <div class="info-item">
                <div>Nhóm ngạch</div>
                <div>{{ $ngach?->Nhom ?? '–' }}</div>
            </div>
            <div class="info-item">
                <div>Bậc lương</div>
                <div>{{ $bac ? 'Bậc ' . $bac->Bac : '–' }}</div>
            </div>
            <div class="info-item">
                <div>Hệ số lương</div>
                <div style="color:#0BAA4B;">{{ $bac ? number_format($bac->HeSo, 2) : '–' }}</div>
            </div>
            <div class="info-item">
                <div>Phụ cấp vượt khung</div>
                <div style="color:#f97316;">{{ $dienBienHienTai->PhuCapVuotKhung ?? 0 }}%</div>
            </div>
            <div class="info-item">
                <div>Ngày hưởng</div>
                <div>{{ $dienBienHienTai->NgayHuong ? \Carbon\Carbon::parse($dienBienHienTai->NgayHuong)->format('d/m/Y') : '–' }}</div>
            </div>
            <div class="info-item">
                <div>Mức lương cơ sở</div>
                <div>{{ number_format($mucLuongCoSo, 0, ',', '.') }} đ</div>
            </div>
        </div>

        <div class="salary-structure-grid">
            <div class="salary-component">
                <div class="component-label">Lương theo ngạch bậc</div>
                <div class="component-value">{{ number_format($luongNgach, 0, ',', '.') }} đ</div>
                <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
                    {{ $bac ? number_format($bac->HeSo, 2) : '?' }} × {{ number_format($mucLuongCoSo, 0, ',', '.') }}
                </div>
            </div>
            @if(($dienBienHienTai->PhuCapVuotKhung ?? 0) > 0)
                <div class="salary-component" style="border-left-color:#f97316;">
                    <div class="component-label">Phụ cấp vượt khung</div>
                    <div class="component-value" style="color:#f97316;">{{ number_format($vuotKhung, 0, ',', '.') }} đ</div>
                    <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
                        {{ $dienBienHienTai->PhuCapVuotKhung }}% × {{ number_format($luongNgach, 0, ',', '.') }}
                    </div>
                </div>
            @endif
            <div class="salary-component" style="border-left-color:#6366f1; background:#f5f3ff;">
                <div class="component-label" style="color:#6366f1;">Tổng lương ngạch bậc</div>
                <div class="component-value" style="color:#6366f1; font-size:22px;">
                    {{ number_format($luongNgach + $vuotKhung, 0, ',', '.') }} đ
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-cash-stack"></i>
            <div style="font-size:15px; font-weight:500; margin-bottom:8px; color:#6b7280;">Chưa có diễn biến lương</div>
            <div style="font-size:14px;">
                Diễn biến lương sẽ được tạo tự động khi tạo hợp đồng có chọn <strong>Ngạch/Bậc lương</strong>.
            </div>
        </div>
    @endif
</div>

{{-- ===== Lịch sử diễn biến lương ===== --}}
<div class="detail-section">
    <h2><i class="bi bi-clock-history" style="color:#0BAA4B;"></i> Lịch sử diễn biến lương</h2>

    @if ($dienBienLuongs->isEmpty())
        <div class="empty-state" style="padding:32px;">
            <i class="bi bi-inbox" style="font-size:40px;"></i>
            <div>Chưa có lịch sử diễn biến lương</div>
        </div>
    @else
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ngày hưởng</th>
                        <th>Mã ngạch</th>
                        <th>Tên ngạch</th>
                        <th>Bậc</th>
                        <th>Hệ số</th>
                        <th>PC Vượt khung</th>
                        <th>Lương ngạch bậc</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dienBienLuongs as $i => $dbl)
                        @php
                            $luongBac = $dbl->bacLuong
                                ? $dbl->bacLuong->HeSo * $mucLuongCoSo * (1 + ($dbl->PhuCapVuotKhung ?? 0) / 100)
                                : 0;
                            $isFirst = $i === 0;
                        @endphp
                        <tr @if($isFirst) style="background:#f0fdf4;" @endif>
                            <td><strong>{{ $i + 1 }}</strong></td>
                            <td>
                                {{ $dbl->NgayHuong ? \Carbon\Carbon::parse($dbl->NgayHuong)->format('d/m/Y') : '–' }}
                            </td>
                            <td>{{ $dbl->ngachLuong?->Ma ?? '–' }}</td>
                            <td>{{ $dbl->ngachLuong?->Ten ?? '–' }}</td>
                            <td>{{ $dbl->bacLuong ? 'Bậc ' . $dbl->bacLuong->Bac : '–' }}</td>
                            <td><strong>{{ $dbl->bacLuong ? number_format($dbl->bacLuong->HeSo, 2) : '–' }}</strong></td>
                            <td>{{ $dbl->PhuCapVuotKhung ?? 0 }}%</td>
                            <td style="font-weight:600; color:{{ $isFirst ? '#0BAA4B' : '#6b7280' }};">
                                {{ number_format($luongBac, 0, ',', '.') }} đ
                            </td>
                            <td>
                                @if($isFirst)
                                    <span class="badge badge-success">Hiện tại</span>
                                @else
                                    <span class="badge" style="background:#f3f4f6; color:#6b7280;">Đã qua</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
