<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo duyệt nghỉ phép</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7fafc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f7fafc; padding: 20px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0BAA4B; padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 700;">ĐƠN NGHỈ PHÉP ĐÃ ĐƯỢC DUYỆT</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="font-size: 16px; color: #1a202c; margin: 0 0 16px 0;">Xin chào <strong>{{ $leave->nhanVien->Ten }}</strong>,</p>
                            <p style="font-size: 15px; color: #4a5568; line-height: 1.6; margin: 0 0 24px 0;">
                                Chúc mừng! Đơn đăng ký nghỉ phép của bạn đã được phê duyệt bởi hệ thống quản lý <strong>{{ $companyName }}</strong>.
                            </p>

                            <!-- Leave Details Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #edf2f7; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40%" style="padding-bottom: 12px; font-size: 13px; color: #718096; font-weight: 600;">LOẠI NGHỈ PHÉP</td>
                                                <td width="60%" style="padding-bottom: 12px; font-size: 14px; color: #2d3748; font-weight: 700;">{{ $leave->loaiNghiPhep->Ten }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 13px; color: #718096; font-weight: 600;">HƯỞNG LƯƠNG</td>
                                                <td style="padding-bottom: 12px; font-size: 14px; color: #0BAA4B; font-weight: 700;">{{ number_format($leave->loaiNghiPhep->HuongLuong, 0) }}%</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 13px; color: #718096; font-weight: 600;">THỜI GIAN</td>
                                                <td style="padding-bottom: 12px; font-size: 14px; color: #2d3748; font-weight: 700;">
                                                    {{ \Carbon\Carbon::parse($leave->TuNgay)->format('d/m/Y') }} 
                                                    @if($leave->TuNgay != $leave->DenNgay)
                                                        đến {{ \Carbon\Carbon::parse($leave->DenNgay)->format('d/m/Y') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #718096; font-weight: 600;">SỐ NGÀY NGHỈ</td>
                                                <td style="font-size: 14px; color: #2d3748; font-weight: 700;">{{ $leave->SoNgayNghi }} ngày</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 15px; color: #4a5568; line-height: 1.6; margin: 0;">
                                Chi tiết nghỉ phép đã được tự động cập nhật vào bảng chấm công của bạn. Chúc bạn có kỳ nghỉ vui vẻ!
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-size: 13px; color: #718096; line-height: 1.8;">
                                Đây là email tự động từ hệ thống HRM của <strong>{{ $companyName }}</strong>.<br>
                                Vui lòng không trả lời email này.
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 12px; color: #a0aec0; margin-top: 20px; text-align: center;">
                    © {{ date('Y') }} {{ $companyName }}.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
