@php
    $chucVu = $nhanVien->ttCongViec?->chucVu?->Ten ?? '—';
    $phongBan = $nhanVien->ttCongViec?->phongBan?->Ten ?? '—';
    $maBP = $phongBan;
    $maNV = $nhanVien->Ma ?? '—';
    $hoTen = $nhanVien->Ten ?? '—';
    $soNguoiPT = $luong['so_nguoi_phu_thuoc'] ?? 0;
    $mstTNCN = $nhanVien->MaSoThue ?? '—';
    $soHopDong = $hopDong?->SoHopDong ?? '—';
    $ngayNhanViec = ($hopDong && $hopDong->NgayKy)
        ? \Carbon\Carbon::parse($hopDong->NgayKy)->format('d/m/Y')
        : '—';
    $soNgayNghi = $luong['so_ngay_nghi'] ?? 0;

    $luongCoBan = $luong['luong_co_ban'] ?? 0;
    $tongPhuCap = $luong['tong_phu_cap'] ?? 0;
    $tongTangCa = $luong['tong_tang_ca'] ?? 0;
    $tongThuNhap = $luongRecord ? $luongRecord->LuongCoBan + $luongRecord->PhuCap + $luongRecord->KhenThuong : ($luong['tong_thu_nhap'] ?? 0);
    $tongKhauTruBH = $luongRecord ? $luongRecord->KhauTruBaoHiem : ($luong['tong_khau_tru_bh'] ?? 0);
    $thueTNCN = $luongRecord ? $luongRecord->ThueTNCN : ($luong['thue_tncn'] ?? 0);
    $tamUng = $luongRecord ? $luongRecord->TamUng : ($luong['tam_ung'] ?? 0);
    $kyLuat = $luongRecord ? $luongRecord->KyLuat : 0;
    $khenThuong = $luongRecord ? $luongRecord->KhenThuong : 0;
    $phuCapRecord = $luongRecord ? $luongRecord->PhuCap : $tongPhuCap;

    $tongKhauTru = $luongRecord ? ($tongKhauTruBH + $thueTNCN + $tamUng + $kyLuat) : ($luong['tong_khau_tru'] ?? 0);
    $luongThucNhan = $luongRecord ? $luongRecord->Luong : ($luong['luong_thuc_nhan'] ?? 0);

    // Ngày công
    $ngayCongThucTe = $luong['ngay_cong_thuc_te'] ?? 0;
    $ngayCongChuan = $luong['ngay_cong_chuan'] ?? 26;
    $isCongNhan = ($luong['loai_nhan_vien'] ?? 1) === 0;
    $loaiNhanVienText = $luong['loai_nhan_vien_text'] ?? '';
    $isContractMode = str_contains($loaiNhanVienText, 'hợp đồng');

    // Phụ cấp chi tiết từ hợp đồng
    $allowances = [
        'Thưởng chuyên cần' => 0,
        'Hỗ trợ nhà trọ' => $hopDong?->PhuCapKhuVuc ?? 0,
        'Hỗ trợ đi lại' => $hopDong?->PhuCapXangXe ?? 0,
        'Phụ cấp ăn trưa' => $hopDong?->PhuCapAnTrua ?? 0,
        'Phụ cấp chức vụ' => $hopDong?->PhuCapChucVu ?? 0,
        'Phụ cấp trách nhiệm' => $hopDong?->PhuCapTrachNhiem ?? 0,
        'Phụ cấp độc hại' => $hopDong?->PhuCapDocHai ?? 0,
        'Phụ cấp thâm niên' => $hopDong?->PhuCapThamNien ?? 0,
        'Phụ cấp điện thoại' => $hopDong?->PhuCapDienThoai ?? 0,
        'Phụ cấp khác' => $hopDong?->PhuCapKhac ?? 0,
    ];

    // Bảo hiểm chi tiết
    $bhxhRate = 0;
    $bhytRate = 0;
    $bhtnRate = 0;
    foreach ($baoHiems as $bh) {
        $tenLower = mb_strtolower($bh->TenLoai ?? '');
        if (str_contains($tenLower, 'bhxh') || str_contains($tenLower, 'xã hội')) {
            $bhxhRate = $bh->TiLeNhanVien;
        } elseif (str_contains($tenLower, 'bhyt') || str_contains($tenLower, 'y tế')) {
            $bhytRate = $bh->TiLeNhanVien;
        } elseif (str_contains($tenLower, 'bhtn') || str_contains($tenLower, 'thất nghiệp')) {
            $bhtnRate = $bh->TiLeNhanVien;
        }
    }
    $bhxh = ($luongCoBan * $bhxhRate) / 100;
    $bhyt = ($luongCoBan * $bhytRate) / 100;
    $bhtn = ($luongCoBan * $bhtnRate) / 100;
@endphp

<style>
    .slip-wrap {
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #000;
        line-height: 1.5;
        padding: 0;
        background: #fff;
        border: 1.5px solid #2e59d9;
        max-width: 820px;
        margin: 0 auto;
    }

    .slip-inner {
        padding: 0;
    }

    .slip-wrap table {
        width: 100%;
        border-collapse: collapse;
        margin-top: -1px; /* Overlap borders */
    }

    .slip-wrap td,
    .slip-wrap th {
        border: 1px solid #2e59d9;
        padding: 7px 9px;
        vertical-align: top;
    }

    .slip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1.5px solid #2e59d9;
        background: #f8faff;
    }

    .slip-title {
        color: #dc2626;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        flex: 1;
    }

    .slip-bold {
        font-weight: bold;
    }

    .slip-blue {
        color: #1d4ed8;
    }

    .slip-total-label {
        font-size: 17px;
        color: #1d4ed8;
        font-weight: bold;
    }

    .slip-total-val {
        font-size: 17px;
        color: #1d4ed8;
        font-weight: bold;
    }

    .slip-row-section td {
        font-weight: bold;
        background: #f8faff;
    }

    .slip-row-sub {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }

    .slip-row-sub:first-child {
        margin-top: 0;
    }

    /* Dark Mode Overrides */
    body.dark-theme .slip-wrap {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-color: #374151 !important;
    }

    body.dark-theme .slip-wrap td,
    body.dark-theme .slip-wrap th {
        border-color: #374151 !important;
        color: #e5e7eb !important;
    }

    body.dark-theme .slip-title {
        color: #10b981 !important; /* Brand Green */
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    }

    body.dark-theme .slip-header {
        background: #1f2937 !important;
        border-bottom-color: #374151 !important;
    }

    body.dark-theme .slip-bold {
        color: #ffffff !important;
    }

    body.dark-theme .slip-blue,
    body.dark-theme .slip-total-label,
    body.dark-theme .slip-total-val {
        color: #60a5fa !important; /* Lighter blue for dark bg */
    }

    body.dark-theme .slip-row-section td {
        background: #1f2937 !important;
        color: #10b981 !important;
    }

    body.dark-theme .slip-header span {
        color: #9ca3af !important;
    }

    body.dark-theme .slip-row-sub span:first-child {
        color: #9ca3af;
    }

    body.dark-theme .slip-total-val {
        color: #10b981 !important;
        font-size: 19px;
    }
</style>

<div class="slip-wrap">
    <div class="slip-inner">
        {{-- Header --}}
        <div class="slip-header">
        <div style="font-size:13px; max-width:200px; font-weight: bold;">
            {{ \App\Models\SystemConfig::getValue('company_name', 'Vietnam Rubber Group') }}<br>
            <span style="font-size: 11px; font-weight: normal; color: #666;">
                {{ \App\Models\SystemConfig::getValue('company_address', '') }}
            </span>
        </div>
        <div class="slip-title">PHIẾU LƯƠNG THÁNG {{ $thang }}/{{ $nam }}</div>
        <div style="width:100px;"></div>
    </div>

    {{-- Thông tin nhân viên --}}
    <table style="border:none;">
        <tr>
            <td colspan="2">Họ tên: <span class="slip-bold">{{ $hoTen }}</span></td>
            <td>Mã NV: <span class="slip-bold">{{ $maNV }}</span></td>
        </tr>
        <tr>
            <td>Chức danh: <span class="slip-bold">{{ $chucVu }}</span></td>
            <td>Số HĐ: <span class="slip-bold">{{ $soHopDong }}</span></td>
            <td>Kì lương: <span class="slip-bold">{{ $thang }}/{{ $nam }}</span></td>
        </tr>
        <tr>
            <td>Tham gia: {{ $ngayNhanViec }}</td>
            <td style="text-align:center;">MST TNCN:<br><span class="slip-bold">{{ $mstTNCN }}</span></td>
            <td>Nghi phép: <span class="slip-bold">{{ number_format($soNgayNghi, 1) }}</span></td>
            <td>Lương HĐLĐ: <span class="slip-bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span></td>
        </tr>
    </table>


    {{-- 3 cột chính --}}
    <table style="border:none;">
        <tr>
            <td width="34%" class="slip-bold">
                Số ngày đi làm
                <span style="float:right; color:#dc2626;">{{ number_format($ngayCongThucTe, 2) }}/{{ $ngayCongChuan }}</span>
            </td>
            <td width="33%" style="text-align:center;" class="slip-bold">Phụ cấp hợp đồng</td>
            <td width="33%" style="text-align:center;" class="slip-bold">Các khoản khấu trừ</td>
        </tr>
        <tr>
            {{-- Cột 1: Lương công --}}
            <td>
                @if($isContractMode)
                    <div class="slip-row-sub">
                        <span>Tổng lương hợp đồng</span>
                        <span class="slip-bold">{{ number_format($luong['luong_ngay_cong'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                @else
                    @if($isCongNhan)
                        <div class="slip-row-sub">
                            <span>Công thực tế</span>
                            <span class="slip-bold">{{ number_format($ngayCongThucTe, 2) }}</span>
                        </div>
                        <div class="slip-row-sub">
                            <span>Lương theo ngày công</span>
                            <span class="slip-bold">{{ number_format($luong['luong_ngay_cong'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="slip-row-sub">
                            <span>Lương cơ bản</span>
                            <span class="slip-bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endif
                @if($tongTangCa > 0)
                    <div class="slip-row-sub">
                        <span>Lương làm thêm</span>
                        <span class="slip-bold">{{ number_format($tongTangCa, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($khenThuong > 0)
                    <div class="slip-row-sub">
                        <span>Thưởng</span>
                        <span class="slip-bold">{{ number_format($khenThuong, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="slip-row-sub">
                        <span>Thưởng</span>
                        <span>—</span>
                    </div>
                @endif
            </td>

            {{-- Cột 2: Phụ cấp --}}
            <td>
                @if($phuCapRecord > 0)
                    <div class="slip-row-sub">
                        <span>Phụ cấp hợp đồng</span>
                        <span class="slip-bold">{{ number_format($phuCapRecord, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div style="color:#9ca3af; font-style:italic; text-align:center; padding-top:10px;">
                        Không có phụ cấp
                    </div>
                @endif
            </td>

            {{-- Cột 3: Khấu trừ --}}
            <td>
                @if($bhxhRate > 0)
                    <div class="slip-row-sub">
                        <span>BHXH ({{ $bhxhRate }}%)</span>
                        <span class="slip-bold">{{ number_format($bhxh, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($bhytRate > 0)
                    <div class="slip-row-sub">
                        <span>BHYT ({{ $bhytRate }}%)</span>
                        <span class="slip-bold">{{ number_format($bhyt, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($bhtnRate > 0)
                    <div class="slip-row-sub">
                        <span>BHTN ({{ $bhtnRate }}%)</span>
                        <span class="slip-bold">{{ number_format($bhtn, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if(count($baoHiems) === 0)
                    <div class="slip-row-sub">
                        <span style="color:#9ca3af; font-style:italic;">Chưa cấu hình bảo hiểm</span>
                    </div>
                @endif
                <div class="slip-row-sub">
                    <span>Thuế TNCN</span>
                    <span class="slip-bold">{{ $thueTNCN > 0 ? number_format($thueTNCN, 0, ',', '.') : '—' }}</span>
                </div>
                @if($tamUng > 0)
                    <div class="slip-row-sub">
                        <span>Lương tạm ứng</span>
                        <span class="slip-bold" style="color:#dc2626;">{{ number_format($tamUng, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="slip-row-sub">
                    <span>Phạt/Kỷ luật</span>
                    <span class="slip-bold">{{ $kyLuat > 0 ? number_format($kyLuat, 0, ',', '.') : '—' }}</span>
                </div>
            </td>
        </tr>
    </table>


    {{-- Tổng cộng --}}
    <table style="border:none;">
        <tr>
            <td width="34%"><span class="slip-blue slip-bold">Thực lĩnh lương</span></td>
            <td colspan="4" class="slip-total-val" style="text-align: right;">
                {{ number_format($luongThucNhan, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="height:38px; vertical-align:middle;"><span class="slip-bold">Ghi chú</span></td>
            <td colspan="4" style="color:#6b7280; font-size:12px;">
                {{ $loaiNhanVienText }} — {{ number_format($ngayCongThucTe, 2) }} ngày công — Tính tự động {{ now()->format('d/m/Y') }}
            </td>
        </tr>
    </table>
    </div>
</div>
