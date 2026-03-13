@php
    $chucVu = $nhanVien->ttCongViec?->chucVu?->Ten ?? '—';
    $phongBan = $nhanVien->ttCongViec?->phongBan?->Ten ?? '—';
    $maBP = $phongBan;
    $maNV = $nhanVien->Ma ?? '—';
    $hoTen = $nhanVien->Ten ?? '—';
    $soTK = $nhanVien->SoTaiKhoan ?? '—';
    $mstTNCN = $nhanVien->MaSoThue ?? '—';
    $ngayNhanViec = $nhanVien->NgayVaoCongTy
        ? \Carbon\Carbon::parse($nhanVien->NgayVaoCongTy)->format('d/m/Y')
        : '—';
    $soNguoiPT = $luong['so_nguoi_phu_thuoc'] ?? 0;

    $luongCoBan = $luong['luong_co_ban'] ?? 0;
    $tongPhuCap = $luong['tong_phu_cap'] ?? 0;
    $tongTangCa = $luong['tong_tang_ca'] ?? 0;
    $tongThuNhap = $luong['tong_thu_nhap'] ?? 0;
    $tongKhauTruBH = $luong['tong_khau_tru_bh'] ?? 0;
    $thueTNCN = $luong['thue_tncn'] ?? 0;
    $tongKhauTru = $luong['tong_khau_tru'] ?? 0;
    $luongThucNhan = $luong['luong_thuc_nhan'] ?? 0;

    // Ngày công (công nhân)
    $ngayCongThucTe = $luong['ngay_cong_thuc_te'] ?? null;
    $ngayCongChuan = $luong['ngay_cong_chuan'] ?? 26;
    $isCongNhan = ($luong['loai_nhan_vien'] ?? 1) === 0;

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
        padding: 4px;
    }

    .slip-wrap table {
        width: 100%;
        border-collapse: collapse;
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
        align-items: flex-start;
        margin-bottom: 6px;
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
</style>

<div class="slip-wrap">
    {{-- Header --}}
    <div class="slip-header">
        <div style="font-size:13px; max-width:160px;">
            Công ty TNHH Cao Su Bình Long
        </div>
        <div class="slip-title">PHIẾU LƯƠNG THÁNG {{ $thang }}/{{ $nam }}</div>
        <div style="width:100px;"></div>
    </div>

    {{-- Thông tin nhân viên --}}
    <table style="margin-bottom:0; border-bottom:none;">
        <tr>
            <td colspan="2">Họ tên: <span class="slip-bold">{{ $hoTen }}</span></td>
            <td>Mã NV: <span class="slip-bold">{{ $maNV }}</span></td>
        </tr>
        <tr>
            <td>Chức danh: {{ $chucVu }}</td>
            <td>Số TK: <span class="slip-bold">{{ $soTK }}</span></td>
            <td>Mã BP: <span class="slip-bold">{{ $maBP }}</span></td>
        </tr>
        <tr>
            <td>Nhận việc: {{ $ngayNhanViec }}</td>
            <td style="text-align:center;">MST TNCN:<br><span class="slip-bold">{{ $mstTNCN }}</span></td>
            <td>Số người phụ thuộc: <span class="slip-bold">{{ $soNguoiPT }}</span></td>
            <td>Lương HĐLĐ: <span class="slip-bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span></td>
        </tr>
    </table>

    {{-- 3 cột chính --}}
    <table style="margin-top:0; border-top:none;">
        <tr>
            <td width="34%" class="slip-bold">
                Công chính hưởng P/C
                @if($isCongNhan)
                    <span style="float:right; color:#dc2626;">{{ $ngayCongThucTe }}/{{ $ngayCongChuan }}</span>
                @endif
            </td>
            <td width="33%" style="text-align:center;" class="slip-bold">Hỗ trợ phụ cấp</td>
            <td width="33%" style="text-align:center;" class="slip-bold">Các khoản khấu trừ</td>
        </tr>
        <tr>
            {{-- Cột 1: Lương công --}}
            <td>
                @if($isCongNhan)
                    <div class="slip-row-sub">
                        <span>Công chính</span>
                        <span class="slip-bold">{{ $ngayCongThucTe }}</span>
                    </div>
                    <div class="slip-row-sub">
                        <span>Lương theo ngày công</span>
                        <span
                            class="slip-bold">{{ number_format($luong['luong_ngay_cong'] ?? $luongCoBan, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="slip-row-sub">
                        <span>Lương cơ bản</span>
                        <span class="slip-bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($tongTangCa > 0)
                    <div class="slip-row-sub">
                        <span>Lương làm thêm</span>
                        <span class="slip-bold">{{ number_format($tongTangCa, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="slip-row-sub">
                    <span>Thu nhập khác</span>
                    <span>—</span>
                </div>
            </td>

            {{-- Cột 2: Phụ cấp --}}
            <td>
                @php $hasAllowance = false; @endphp
                @foreach($allowances as $tenPC => $soTienPC)
                    @if($soTienPC > 0)
                        @php $hasAllowance = true; @endphp
                        <div class="slip-row-sub">
                            <span>{{ $tenPC }}</span>
                            <span class="slip-bold">{{ number_format($soTienPC, 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endforeach
                @if(!$hasAllowance)
                    <div style="color:#9ca3af; font-style:italic;">Không có phụ cấp</div>
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
                <div class="slip-row-sub">
                    <span>Khác</span>
                    <span>—</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tổng cộng --}}
    <table style="border-top:none; margin-top:0;">
        <tr>
            <td width="34%"><span class="slip-blue slip-bold">Thực lĩnh lương (1)</span></td>
            <td width="16%" class="slip-bold">{{ number_format($luongThucNhan, 0, ',', '.') }}</td>
            <td colspan="2"><span class="slip-blue slip-bold">P/cấp công tác (2)</span></td>
            <td>—</td>
        </tr>
        <tr>
            <td class="slip-total-label">Tổng cộng (1)+(2)</td>
            <td colspan="4" class="slip-total-val">{{ number_format($luongThucNhan, 0, ',', '.') }} đ</td>
        </tr>
        <tr>
            <td style="height:38px; vertical-align:middle;"><span class="slip-bold">Ghi chú</span></td>
            <td colspan="4" style="color:#6b7280; font-size:12px;">
                @if($isCongNhan)
                    Công nhân — {{ $ngayCongThucTe }}/{{ $ngayCongChuan }} ngày công — Tính tự động
                    {{ now()->format('d/m/Y') }}
                @else
                    Nhân viên văn phòng — Lương cứng — Tính tự động {{ now()->format('d/m/Y') }}
                @endif
            </td>
        </tr>
    </table>
</div>