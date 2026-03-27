@php
    $chucVu = $nhanVien->ttCongViec?->chucVu?->Ten ?? '—';
    $phongBan = $nhanVien->ttCongViec?->phongBan?->Ten ?? '—';
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

    $isCongNhan = ($luong['loai_nhan_vien'] ?? 1) === 0;

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

    $bhxhRate = 0; $bhytRate = 0; $bhtnRate = 0;
    foreach ($baoHiems as $bh) {
        $tenLower = mb_strtolower($bh->TenLoai ?? '');
        if (str_contains($tenLower, 'bhxh')) $bhxhRate = $bh->TiLeNhanVien;
        elseif (str_contains($tenLower, 'bhyt')) $bhytRate = $bh->TiLeNhanVien;
        elseif (str_contains($tenLower, 'bhtn')) $bhtnRate = $bh->TiLeNhanVien;
    }
    $bhxh = ($luongCoBan * $bhxhRate) / 100;
    $bhyt = ($luongCoBan * $bhytRate) / 100;
    $bhtn = ($luongCoBan * $bhtnRate) / 100;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu lương {{ $thang }}/{{ $nam }}</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f7fafc; padding: 20px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0BAA4B; padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">THÔNG BÁO PHIẾU LƯƠNG</h1>
                            <p style="color: rgba(255, 255, 255, 0.85); margin: 8px 0 0 0; font-size: 16px;">Kỳ lương tháng {{ $thang }}/{{ $nam }}</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="font-size: 16px; color: #1a202c; margin: 0 0 16px 0;">Xin chào <strong>{{ $hoTen }}</strong>,</p>
                            <p style="font-size: 15px; color: #4a5568; line-height: 1.6; margin: 0 0 32px 0;">
                                {{ $companyName }} gửi đến bạn thông tin chi tiết về thu nhập của bạn trong tháng {{ $thang }}/{{ $nam }}.
                            </p>

                            <!-- Employee Info Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #edf2f7; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="50%" style="padding-bottom: 12px;">
                                                    <span style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600;">Mã nhân viên</span><br>
                                                    <span style="font-size: 14px; color: #2d3748; font-weight: 600;">{{ $maNV }}</span>
                                                </td>
                                                <td width="50%" style="padding-bottom: 12px;">
                                                    <span style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600;">Chức danh</span><br>
                                                    <span style="font-size: 14px; color: #2d3748; font-weight: 600;">{{ $chucVu }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600;">Bộ phận</span><br>
                                                    <span style="font-size: 14px; color: #2d3748; font-weight: 600;">{{ $phongBan }}</span>
                                                </td>
                                                <td>
                                                    <span style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600;">Số tài khoản</span><br>
                                                    <span style="font-size: 14px; color: #2d3748; font-weight: 600;">{{ $soTK }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Financial Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                                <!-- Income Section -->
                                <tr>
                                    <td colspan="2" style="background-color: #f1f5f9; padding: 12px 15px; font-weight: 700; color: #334155; border-bottom: 2px solid #e2e8f0;">CÁC KHOẢN THU NHẬP</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; color: #4a5568;">Lương cơ bản (HĐLĐ)</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; font-weight: 600; color: #2d3748;">{{ number_format($luongCoBan, 0, ',', '.') }} đ</td>
                                </tr>
                                @if($tongTangCa > 0)
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; color: #4a5568;">Làm thêm giờ</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; font-weight: 600; color: #2d3748;">{{ number_format($tongTangCa, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                
                                @foreach($allowances as $tenPC => $soTienPC)
                                    @if($soTienPC > 0)
                                    <tr>
                                        <td style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; color: #4a5568;">{{ $tenPC }}</td>
                                        <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #edf2f7; font-weight: 600; color: #2d3748;">{{ number_format($soTienPC, 0, ',', '.') }} đ</td>
                                    </tr>
                                    @endif
                                @endforeach

                                <!-- Deduction Section -->
                                <tr>
                                    <td colspan="2" style="background-color: #fff1f2; padding: 12px 15px; font-weight: 700; color: #991b1b; border-bottom: 2px solid #fecaca; padding-top: 25px;">CÁC KHOẢN KHẤU TRỪ</td>
                                </tr>
                                @if($bhxh > 0)
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; color: #4a5568;">Bảo hiểm xã hội ({{ $bhxhRate }}%)</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; font-weight: 600; color: #2d3748;">- {{ number_format($bhxh, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($bhyt > 0)
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; color: #4a5568;">Bảo hiểm y tế ({{ $bhytRate }}%)</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; font-weight: 600; color: #2d3748;">- {{ number_format($bhyt, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($bhtn > 0)
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; color: #4a5568;">Bảo hiểm thất nghiệp ({{ $bhtnRate }}%)</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; font-weight: 600; color: #2d3748;">- {{ number_format($bhtn, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($thueTNCN > 0)
                                <tr>
                                    <td style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; color: #4a5568;">Thuế TNCN</td>
                                    <td align="right" style="padding: 12px 15px; border-bottom: 1px solid #fff1f2; font-weight: 600; color: #2d3748;">- {{ number_format($thueTNCN, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif

                                <!-- Net Pay -->
                                <tr>
                                    <td style="padding: 30px 15px 12px 15px; font-size: 18px; font-weight: 700; color: #2d3748;">THỰC NHẬN</td>
                                    <td align="right" style="padding: 30px 15px 12px 15px; font-size: 22px; font-weight: 800; color: #0BAA4B;">{{ number_format($luongThucNhan, 0, ',', '.') }} đ</td>
                                </tr>
                            </table>

                            <div style="margin-top: 40px; padding: 20px; background-color: #fdf2f2; border-radius: 8px; border-left: 4px solid #f87171;">
                                <p style="margin: 0; font-size: 14px; color: #991b1b; line-height: 1.5;">
                                    <strong>Ghi chú:</strong><br>
                                    @if($isCongNhan)
                                        Đối với công nhân: Số ngày công thực tế là {{ $luong['ngay_cong_thuc_te'] ?? 0 }}/{{ $luong['ngay_cong_chuan'] ?? 26 }} ngày.
                                    @else
                                        Dành cho nhân viên văn phòng (Lương khoán/Lương cứng).
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-size: 13px; color: #718096; line-height: 1.8;">
                                Đây là email tự động từ hệ thống HRM của <strong>{{ $companyName }}</strong>.<br>
                                Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ Phòng Hành chính - Nhân sự.<br>
                                Trân trọng cảm ơn!
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 12px; color: #a0aec0; margin-top: 20px;">
                    © {{ date('Y') }} {{ $companyName }}. Bảo mật thông tin lương là trách nhiệm của mỗi nhân viên.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
