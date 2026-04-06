<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chúc mừng sinh nhật {{ $nhanVien->Ten }}</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #fdf2f8; margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fdf2f8; padding: 24px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(236, 72, 153, 0.12); border: 1px solid #fbcfe8;">

                    <!-- Header: Gradient xanh–hồng -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0BAA4B 0%, #ec4899 100%); padding: 40px 40px 32px; text-align: center;">
                            <div style="font-size: 60px; margin-bottom: 12px;">🎂</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; text-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                                CHÚC MỪNG SINH NHẬT!
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 8px 0 0 0; font-size: 16px;">🎊 Happy Birthday 🎊</p>
                        </td>
                    </tr>

                    <!-- Confetti strip -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #0BAA4B, #ec4899, #0BAA4B, #ec4899, #0BAA4B); height: 5px;"></td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background: #ffffff; padding: 40px;">
                            <p style="font-size: 17px; color: #1f2937; margin: 0 0 16px 0; line-height: 1.7;">
                                Xin chào <strong style="color: #ec4899;">{{ $nhanVien->Ten }}</strong>,
                            </p>
                            <p style="font-size: 15px; color: #4b5563; line-height: 1.8; margin: 0 0 24px 0;">
                                Nhân ngày sinh nhật đặc biệt hôm nay, toàn thể đội ngũ <strong>{{ $companyName }}</strong> xin gửi đến bạn những lời chúc mừng nồng nhiệt nhất! 🌟
                            </p>

                            <!-- Quote box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #f0fdf4, #fdf2f8); border-left: 4px solid #ec4899; border-radius: 8px; padding: 20px 24px;">
                                        <p style="margin: 0; font-size: 16px; color: #111827; font-style: italic; line-height: 1.8; font-weight: 500;">
                                            "Chúc bạn một ngày sinh nhật thật vui vẻ và ý nghĩa! Mong bạn luôn tràn đầy sức khỏe, hạnh phúc và không ngừng tỏa sáng trong công việc cũng như cuộc sống." 🌸
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Wish list -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 12px;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 32px; text-align: center; font-size: 18px; vertical-align: top; padding-top: 2px;">🌿</td>
                                                <td style="font-size: 15px; color: #374151; padding-left: 10px; line-height: 1.6;">Sức khỏe dồi dào, tinh thần phấn khởi</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 12px;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 32px; text-align: center; font-size: 18px; vertical-align: top; padding-top: 2px;">💖</td>
                                                <td style="font-size: 15px; color: #374151; padding-left: 10px; line-height: 1.6;">Hạnh phúc bên gia đình và những người thân yêu</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 32px; text-align: center; font-size: 18px; vertical-align: top; padding-top: 2px;">🚀</td>
                                                <td style="font-size: 15px; color: #374151; padding-left: 10px; line-height: 1.6;">Thành công và thăng tiến trên con đường sự nghiệp</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Emoji strip -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f0fdf4, #fdf2f8); padding: 20px 40px; text-align: center; font-size: 22px; letter-spacing: 6px; border-top: 1px solid #fbcfe8; border-bottom: 1px solid #d1fae5;">
                            🎉 🎈 🎁 🎀 🎊
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #ffffff; padding: 24px 40px; text-align: center; border-top: 1px solid #f3f4f6;">
                            <p style="margin: 0; font-size: 13px; color: #9ca3af; line-height: 1.8;">
                                Thân ái,<br>
                                <strong style="color: #0BAA4B;">{{ $companyName }}</strong><br>
                                <span style="font-size: 11px;">Email tự động từ hệ thống HRM — Vui lòng không trả lời</span>
                            </p>
                        </td>
                    </tr>

                </table>
                <p style="font-size: 12px; color: #c084fc; margin-top: 16px; text-align: center;">
                    © {{ date('Y') }} {{ $companyName }} · 🎂 Birthday Email
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
