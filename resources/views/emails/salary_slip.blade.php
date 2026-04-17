<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phiếu lương tháng {{ $thang }}/{{ $nam }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #0BAA4B; margin-bottom: 5px;">THÔNG BÁO PHIẾU LƯƠNG</h2>
            <p style="color: #666; margin-top: 0;">Kỳ lương tháng {{ $thang }}/{{ $nam }}</p>
        </div>

        <p>Xin chào <strong>{{ $nhanVien->Ten }}</strong>,</p>
        <p>Công ty gửi bạn thông tin chi tiết phiếu lương tháng {{ $thang }}/{{ $nam }}. Bạn vui lòng xem chi tiết bên dưới:</p>

        <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; background-color: #fff;">
            @include('salary.slip_partial')
        </div>

        <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; font-size: 13px; color: #777;">
            <p>Đây là email tự động từ hệ thống quản lý nhân sự {{ \App\Models\SystemConfig::getValue('company_name') }}. Vui lòng không trả lời email này.</p>
            <p>Nếu có bất kỳ thắc mắc nào về lương, vui lòng liên hệ phòng Nhân sự để được giải đáp.</p>
            <p>Trân trọng,<br>Phòng Nhân sự - {{ \App\Models\SystemConfig::getValue('company_name') }}</p>
        </div>
    </div>
</body>
</html>
