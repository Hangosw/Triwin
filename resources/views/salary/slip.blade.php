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

    $ngayCongThucTe = $luong['ngay_cong_thuc_te'] ?? 0;
    $ngayCongChuan = $luong['ngay_cong_chuan'] ?? 26;
    $isCongNhan = ($luong['loai_nhan_vien'] ?? 1) === 0;
    $loaiNhanVienText = $luong['loai_nhan_vien_text'] ?? '';
    $isContractMode = str_contains($loaiNhanVienText, 'hợp đồng');

    $allowances = [
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

    $bhxhRate = 0;
    $bhytRate = 0;
    $bhtnRate = 0;
    foreach ($baoHiems as $bh) {
        $tenLower = mb_strtolower($bh->TenLoai ?? '');
        if (str_contains($tenLower, 'bhxh'))
            $bhxhRate = $bh->TiLeNhanVien;
        elseif (str_contains($tenLower, 'bhyt'))
            $bhytRate = $bh->TiLeNhanVien;
        elseif (str_contains($tenLower, 'bhtn'))
            $bhtnRate = $bh->TiLeNhanVien;
    }
    $bhxh = ($luongCoBan * $bhxhRate) / 100;
    $bhyt = ($luongCoBan * $bhytRate) / 100;
    $bhtn = ($luongCoBan * $bhtnRate) / 100;

    $companyName = \App\Models\SystemConfig::getValue('company_name', 'Vietnam Rubber Group');
    $companyAddress = \App\Models\SystemConfig::getValue('company_address', '');
@endphp

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Lương - {{ $hoTen }} - Tháng {{ $thang }}/{{ $nam }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            line-height: 1.4;
        }

        .payslip-container {
            width: 900px;
            margin: 20px auto;
            border: 2px solid #2e59d9;
            padding: 15px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .company-info {
            font-weight: bold;
        }

        .company-address {
            font-weight: normal;
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }

        .title {
            color: #dc2626;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #2e59d9;
            padding: 8px;
            vertical-align: top;
        }

        .bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .blue-text {
            color: #1d4ed8;
        }

        .row-sub {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .row-sub:last-child {
            margin-bottom: 0;
        }

        .total-row {
            font-size: 18px;
            color: #1d4ed8;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            .payslip-container {
                margin: 0;
                border: 2px solid #2e59d9;
            }
        }
    </style>
</head>

<body>

    <div class="payslip-container">
        <div class="header">
            <div class="company-info">
                {{ $companyName }}<br>
                <div class="company-address">{{ $companyAddress }}</div>
            </div>
        </div>
        <div class="title">PHIẾU LƯƠNG THÁNG {{ $thang }}/{{ $nam }}</div>

        <table style="margin-bottom: -1px;">
            <tr>
                <td colspan="2">Họ tên: <span class="bold">{{ $hoTen }}</span></td>
                <td>Mã NV: <span class="bold">{{ $maNV }}</span></td>
            </tr>
            <tr>
                <td>Chức danh: {{ $chucVu }}</td>
                <td>Số TK: <span class="bold">{{ $soTK }}</span></td>
                <td>Mã BP: <span class="bold">{{ $maBP }}</span></td>
            </tr>
            <tr>
                <td>Nhận việc: {{ $ngayNhanViec }}</td>
                <td class="text-center">MST TNCN: <br> <span class="bold">{{ $mstTNCN }}</span></td>
                <td>Số người phụ thuộc: <span class="bold">{{ $soNguoiPT }}</span></td>
                <td>Lương HĐLĐ: <span class="bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span></td>
            </tr>
        </table>

        <table>
            <tr class="bold">
                <td width="33%">
                    Công chính hưởng P/C
                    <span
                        style="float:right; color:#dc2626;">{{ number_format($ngayCongThucTe, 2) }}/{{ $ngayCongChuan }}</span>
                </td>
                <td width="34%" class="text-center">Hỗ trợ phụ cấp</td>
                <td width="33%" class="text-center">Các khoản khấu trừ</td>
            </tr>
            <tr>
                {{-- Cột 1: Thu nhập --}}
                <td>
                    @if($isContractMode)
                        <div class="row-sub">
                            <span>Tổng lương hợp đồng</span>
                            <span class="bold">{{ number_format($luong['luong_ngay_cong'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @else
                        @if($isCongNhan)
                            <div class="row-sub">
                                <span>Công thực tế</span>
                                <span class="bold">{{ number_format($ngayCongThucTe, 2) }}</span>
                            </div>
                            <div class="row-sub">
                                <span>Lương theo công</span>
                                <span class="bold">{{ number_format($luong['luong_ngay_cong'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="row-sub">
                                <span>Lương cơ bản</span>
                                <span class="bold">{{ number_format($luongCoBan, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif

                    @if($tongTangCa > 0)
                        <div class="row-sub">
                            <span>Lương làm thêm</span>
                            <span class="bold">{{ number_format($tongTangCa, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="row-sub">
                        <span>Thu nhập khác</span>
                        <span>—</span>
                    </div>
                </td>

                {{-- Cột 2: Phụ cấp --}}
                <td>
                    @if($isContractMode)
                        <div style="color:#0BAA4B; font-style:italic; font-size:12px; text-align:center; padding-top:10px;">
                            Đã gộp vào lương hợp đồng
                        </div>
                    @else
                        @php $hasA = false; @endphp
                        @foreach($allowances as $name => $val)
                            @if($val > 0)
                                @php $hasA = true; @endphp
                                <div class="row-sub">
                                    <span>{{ $name }}</span>
                                    <span class="bold">{{ number_format($val, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        @endforeach
                        @if(!$hasA)
                            <div style="color:#9ca3af; font-style:italic; text-align:center;">Không có phụ cấp</div>
                        @endif
                    @endif
                </td>

                {{-- Cột 3: Khấu trừ --}}
                <td>
                    @if($bhxh > 0)
                        <div class="row-sub">
                            <span>BHXH ({{ $bhxhRate }}%)</span>
                            <span class="bold">{{ number_format($bhxh, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($bhyt > 0)
                        <div class="row-sub">
                            <span>BHYT ({{ $bhytRate }}%)</span>
                            <span class="bold">{{ number_format($bhyt, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($bhtn > 0)
                        <div class="row-sub">
                            <span>BHTN ({{ $bhtnRate }}%)</span>
                            <span class="bold">{{ number_format($bhtn, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="row-sub">
                        <span>Thuế TNCN</span>
                        <span class="bold">{{ $thueTNCN > 0 ? number_format($thueTNCN, 0, ',', '.') : '—' }}</span>
                    </div>
                    <div class="row-sub">
                        <span>Khác</span>
                        <span>—</span>
                    </div>
                </td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr>
                <td width="33%"><span class="blue-text bold">Thực lĩnh lương (1)</span></td>
                <td width="16%" class="bold">{{ number_format($luongThucNhan, 0, ',', '.') }}</td>
                <td colspan="2"><span class="blue-text bold">P/cấp công tác (2)</span></td>
                <td>—</td>
            </tr>
            <tr class="total-row">
                <td>Tổng cộng (1)+(2)</td>
                <td colspan="4">{{ number_format($luongThucNhan, 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr>
                <td height="40px" valign="middle"><span class="bold">Ghi chú</span></td>
                <td colspan="4" style="color:#666; font-size:12px;">
                    {{ $loaiNhanVienText }} — Xuất bản ngày {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center no-print" style="margin-top: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; cursor: pointer; background: #0BAA4B; color: white; border: none; border-radius: 5px; font-weight: bold;">
            In Phiếu Lương / Xuất PDF
        </button>
    </div>

</body>

</html>