<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>In Hợp Đồng - {{ $hopDong->nhanVien->Ten ?? 'N/A' }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 20mm;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            box-sizing: border-box;
        }

        /* Print Specifics */
        @media print {
            body {
                background: none;
            }

            .container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-italic {
            font-style: italic;
        }

        h1 {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin: 20px 0 10px 0;
        }

        h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }

        ul {
            margin: 5px 0;
            padding-left: 20px;
            list-style-type: none;
        }

        ul>li {
            margin-bottom: 5px;
            position: relative;
        }

        ul>li::before {
            content: "-";
            position: absolute;
            left: -15px;
        }

        table.signature {
            width: 100%;
            margin-top: 50px;
        }

        table.signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .print-btn-container {
            text-align: center;
            margin: 20px 0;
        }

        .print-btn {
            background-color: #0F5132;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .print-btn:hover {
            background-color: #166534;
        }
    </style>
</head>

<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">In Hợp Đồng</button>
    </div>

    <div class="container">

        <table class="header-table">
            <tr>
                <td style="width: 40%; text-align: center;">
                    <span class="text-bold">CÔNG TY TNHH PHẦN MỀM</span><br>
                    <span>Số: {{ $hopDong->SoHopDong ?? '..... /HĐLĐ' }}</span>
                </td>
                <td style="width: 60%; text-align: center;">
                    <span class="text-bold">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</span><br>
                    <span class="text-bold">Độc lập – Tự do – Hạnh phúc</span><br>
                    <span>----------------------------</span>
                </td>
            </tr>
        </table>

        <h1>HỢP ĐỒNG LAO ĐỘNG</h1>
        <br>

        <div>
            <span class="text-bold">Chúng tôi, một bên là Ông/Bà:</span> NGUYỄN VÀ VĂN B <span
                style="float: right;">Quốc tịch: Việt Nam</span><br>
            <span class="text-bold">Chức vụ:</span> Giám đốc<br>
            <span class="text-bold">Đại diện cho:</span> CÔNG TY TNHH PHẦN MỀM<br>
            <span class="text-bold">Điện thoại:</span> 0123456789<br>
            <span class="text-bold">Địa chỉ:</span> 123 Đường Công Nghệ, Phường Sáng Tạo, Quận 1, TP.HCM<br>
        </div>
        <br>
        <div>
            <span class="text-bold">Và một bên là Ông/Bà:</span> <span class="text-bold"
                style="text-transform: uppercase;">{{ $hopDong->nhanVien->Ten ?? '...' }}</span> <span
                style="float: right;">Quốc tịch: Việt Nam</span><br>
            <span class="text-bold">Sinh ngày:</span>
            {{ $hopDong->nhanVien->NgaySinh ? \Carbon\Carbon::parse($hopDong->nhanVien->NgaySinh)->format('d/m/Y') : '.../.../......' }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-bold">Tại:</span> {{ $hopDong->nhanVien->QueQuan ?? '...................' }}<br>
            <span class="text-bold">Nghề nghiệp:</span> Nhân viên<br>
            <span class="text-bold">Địa chỉ thường trú:</span>
            {{ $hopDong->nhanVien->DiaChi ?? '...................' }}<br>
            <span class="text-bold">Số CCCD/CMND:</span> {{ $hopDong->nhanVien->SoCCCD ?? '...................' }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-bold">Cấp ngày:</span>
            {{ $hopDong->nhanVien->NgayCap ? \Carbon\Carbon::parse($hopDong->nhanVien->NgayCap)->format('d/m/Y') : '.../.../......' }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="text-bold">Nơi cấp:</span> {{ $hopDong->nhanVien->NoiCap ?? '...................' }}<br>
        </div>

        <p class="text-italic" style="margin-top: 15px;">Thoả thuận ký kết hợp đồng lao động và cam kết làm đúng những
            điều khoản sau đây:</p>

        <h2>Điều 1: THỜI HẠN VÀ CÔNG VIỆC HỢP ĐỒNG</h2>
        <ul>
            <li><span class="text-bold">Loại hợp đồng lao động:</span>
                {{ $hopDong->loaiHopDong->TenLoai ?? ($hopDong->Loai ?? '...................') }}</li>
            <li><span class="text-bold">Từ ngày:</span>
                {{ $hopDong->NgayBatDau ? \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') : '.../.../......' }}
                @if($hopDong->NgayKetThuc)
                    đến ngày {{ \Carbon\Carbon::parse($hopDong->NgayKetThuc)->format('d/m/Y') }}
                @endif
            </li>
            <li><span class="text-bold">Địa điểm làm việc:</span> Số 123 Đường Công Nghệ, Phường Sáng Tạo, Quận 1,
                TP.HCM</li>
            <li><span class="text-bold">Chức danh chuyên môn:</span> {{ $hopDong->chucVu->Ten ?? 'Nhân viên' }}</li>
            <li><span class="text-bold">Công việc phải làm:</span> Liên quan đến chuyên môn và những công việc khác do
                Giám đốc (hoặc cá nhân được Giám đốc ủy quyền) phân công theo quy định của Pháp luật.</li>
        </ul>

        <h2>Điều 2: CHẾ ĐỘ LÀM VIỆC</h2>
        <div><span class="text-bold">1) Thời giờ làm việc:</span></div>
        <ul>
            <li>Trong ngày: 8h/ngày - 44h/tuần. Sáng từ 8h đến 12h, Chiều từ 13h đến 17h.</li>
        </ul>
        <div><span class="text-bold">2) Thời gian nghỉ:</span></div>
        <ul>
            <li>Nghỉ hàng năm, nghỉ lễ, tết, nghỉ việc riêng... theo quy định của Luật lao động.</li>
            <li>Tuỳ theo yêu cầu công việc công ty có thể điều động làm việc ngoài giờ.</li>
            <li>Điều kiện an toàn vệ sinh lao động tại nơi làm việc theo quy định của Pháp luật hiện hành.</li>
        </ul>

        <h2>Điều 3: NGHĨA VỤ VÀ QUYỀN LỢI CỦA NGƯỜI LAO ĐỘNG</h2>
        <div><span class="text-bold">1. Quyền lợi:</span></div>
        <ul>
            <li>Phương tiện đi lại làm việc: tự túc.</li>
            <li><span class="text-bold">Mức lương chính:</span>
                {{ number_format($hopDong->MucLuong ?? 0, 0, ',', '.') }} VNĐ/tháng (tại thời điểm ký hợp đồng).
            </li>
            <li>Hình thức trả lương: Bằng chuyển khoản / tiền mặt.</li>
            <li>Được trang bị bảo hộ lao động: Theo yêu cầu công việc được phân công.</li>
            <li>Tiền thưởng lễ, tết: Hưởng theo quy chế lương thưởng chung của toàn công ty.</li>
            <li>Chế độ đào tạo: Theo nghị quyết của Ban Giám đốc công ty.</li>
        </ul>

        <div><span class="text-bold">2. Nghĩa vụ:</span></div>
        <ul>
            <li>Hoàn thành những công việc đã cam kết trong hợp đồng lao động.</li>
            <li>Chấp hành lệnh điều hành sản xuất - kinh doanh, nội quy kỷ luật lao động, an toàn lao động...</li>
            <li>Bồi thường vi phạm: Theo qui định của Công ty và Luật lao động. Doanh nghiệp yêu cầu Người lao động chịu
                bồi thường thiệt hại gây ra do vi phạm quy định điều khoản.</li>
        </ul>

        <h2>Điều 4: NGHĨA VỤ VÀ QUYỀN HẠN CỦA DOANH NGHIỆP</h2>
        <ul>
            <li>Bảo đảm việc làm và thực hiện đầy đủ cam kết trong hợp đồng.</li>
            <li>Thanh toán đầy đủ, đúng thời hạn các chế độ quyền lợi cho người lao động.</li>
            <li>Có quyền điều hành người lao động hoàn thành công việc theo hợp đồng, bố trí điều chuyển công việc hợp
                lý.</li>
            <li>Đơn phương chấm dứt hợp đồng nếu người lao động vi phạm nghiêm trọng nội quy, không hoàn thành chỉ tiêu,
                hoặc có hành vi gian lận.</li>
        </ul>

        <h2>Điều 5: ĐIỀU KHOẢN THI HÀNH</h2>
        <ul>
            <li>Những vấn đề chưa ghi trong hợp đồng này sẽ áp dụng theo nội quy lao động công ty hoặc quy định pháp
                luật.</li>
            <li>Hợp đồng được làm thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</li>
        </ul>

        <div style="text-align: right; margin-top: 20px;">
            <span class="text-italic">Hợp đồng này lập tại văn phòng công ty ngày {{ date('d') }} tháng {{ date('m') }}
                năm {{ date('Y') }}.</span>
        </div>

        <table class="signature">
            <tr>
                <td>
                    <span class="text-bold">NGƯỜI LAO ĐỘNG</span><br>
                    <span class="text-italic">(Ký, ghi rõ họ tên)</span>
                    <br><br><br><br><br>
                    <span class="text-bold">{{ $hopDong->nhanVien->Ten ?? '...................' }}</span>
                </td>
                <td>
                    <span class="text-bold">ĐẠI DIỆN CÔNG TY</span><br>
                    <span class="text-italic">(Ký, đóng dấu, ghi rõ họ tên)</span>
                    <br><br><br><br><br>
                    <span class="text-bold">NGUYỄN VÀ VĂN B</span>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Mở hộp thoại in tự động khi vừa tải trang xong
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>