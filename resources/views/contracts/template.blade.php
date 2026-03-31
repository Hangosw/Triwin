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
            padding: 25mm;
            box-sizing: border-box;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        @media screen and (max-width: 1024px) {
            .container {
                padding: 10mm;
                max-width: 100%;
                overflow-x: hidden;
            }
            body {
                font-size: 12pt;
            }
        }

        /* Print Specifics */
        @media print {
            body {
                background: none;
            }

            .container {
                margin: 0;
                padding: 15mm 20mm;
                box-shadow: none;
                width: 100%;
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
            table-layout: fixed;
        }

        table.signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
            word-wrap: break-word;
        }
        
        @media screen and (max-width: 600px) {
            table.signature td {
                display: block;
                width: 100%;
                margin-bottom: 30px;
            }
        }

        .print-btn-container {
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .print-btn {
            background-color: #0BAA4B;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
        }

        .print-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Signature Modal Styles */
        .signature-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1050;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px);
        }
        .signature-modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            width: 95%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: left;
        }
        .signature-pad-wrapper {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            margin: 15px 0;
            touch-action: none;
        }
        #signature-pad {
            width: 100%;
            height: 200px;
            cursor: crosshair;
        }
        .signature-img {
            max-height: 100px;
            max-width: 200px;
            display: block;
            margin: 0 auto;
        }

        .swal2-container {
            z-index: 3000 !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()" style="background-color: #6b7280; margin-right: 10px;">In Hợp Đồng</button>
        @php
            $currentUser = auth()->user();
            $currentNhanVienId = $currentUser?->nhanVien?->id ?? null;

            $isAdmin = $currentUser?->hasRole('Super Admin');

            $isOwner = $currentNhanVienId && $hopDong->NhanVienId && $currentNhanVienId == $hopDong->NhanVienId;

            $canSignEmployee = $isAdmin || $isOwner;
            $canSignCompany = $isAdmin;
        @endphp

        @if($canSignEmployee || $canSignCompany)
            <button class="print-btn" onclick="openSignatureModal()">Kí số</button>
        @endif
    </div>

    <!-- Signature Modal -->
    <div id="signatureModal" class="signature-modal no-print">
        <div class="signature-modal-content">
            <h3 style="margin-top: 0; color: #0BAA4B;">Kí số hợp đồng</h3>
            
            @if($isAdmin)
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Chọn vị trí ký:</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="sign_position" value="employee" checked> Người lao động
                        </label>
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="sign_position" value="company"> Đại diện công ty
                        </label>
                    </div>
                </div>
            @else
                <input type="hidden" id="sign_position" value="employee">
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 15px;">Bạn đang kí tên với tư cách <strong>Người lao động</strong>.</p>
            @endif

            <div class="signature-pad-wrapper">
                <canvas id="signature-pad"></canvas>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <button onclick="closeSignatureModal()" style="background: #f3f4f6; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; cursor: pointer;">Hủy</button>
                <div>
                    <button onclick="clearSignature()" style="background: white; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-right: 8px;">Xóa trắng</button>
                    <button onclick="saveSignature()" style="background: #0BAA4B; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">Hoàn tất</button>
                </div>
            </div>
        </div>
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
                    <span class="text-bold">----------------------------</span>
                </td>
            </tr>
        </table>

        <h1>HỢP ĐỒNG LAO ĐỘNG</h1>
        <br>

            <span class="text-bold">Chúng tôi, một bên là Ông/Bà:</span> {{ $hopDong->nguoiKy->Ten ?? '...' }} <span
                style="float: right;">Quốc tịch: Việt Nam</span><br>
            <span class="text-bold">Chức vụ:</span> Giám đốc<br>
            <span class="text-bold">Đại diện cho:</span> CÔNG TY TNHH PHẦN MỀM<br>
            <span class="text-bold">Điện thoại:</span> 0123456789<br>
            <span class="text-bold">Địa chỉ:</span> 123 Đường Công Nghệ, Phường Sáng Tạo, Quận 1, TP.HCM<br>
        <br>
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
                <td id="employee-signature-area">
                    <span class="text-bold">NGƯỜI LAO ĐỘNG</span><br>
                    <span class="text-italic">(Ký, ghi rõ họ tên)</span>
                    <div class="signature-display" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                        <!-- Signature will be inserted here -->
                    </div>
                    <span class="text-bold">{{ $hopDong->nhanVien->Ten ?? '...................' }}</span>
                </td>
                <td id="company-signature-area">
                    <span class="text-bold">ĐẠI DIỆN CÔNG TY</span><br>
                    <span class="text-italic">(Ký, đóng dấu, ghi rõ họ tên)</span>
                    <div class="signature-display" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                        <!-- Signature will be inserted here -->
                    </div>
                    <span class="text-bold">{{ $hopDong->nguoiKy->Ten ?? '...................' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Signature Pad Library -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let signaturePad;
        const canvas = document.getElementById('signature-pad');

        function openSignatureModal() {
            document.getElementById('signatureModal').style.display = 'flex';
            
            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });
                
                // Adjust canvas size
                resizeCanvas();
            } else {
                signaturePad.clear();
            }
        }

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        function closeSignatureModal() {
            document.getElementById('signatureModal').style.display = 'none';
        }

        function clearSignature() {
            signaturePad.clear();
        }

        async function saveSignature() {
            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thông báo',
                    text: 'Vui lòng vẽ chữ ký trước khi hoàn tất.'
                });
                return;
            }

            const btn = event.target;
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const signatureData = signaturePad.toDataURL();
            const positionRadio = document.querySelector('input[name="sign_position"]:checked');
            const position = positionRadio ? positionRadio.value : (document.getElementById('sign_position')?.value || 'employee');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch("{{ route('hop-dong.save-signature') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: "{{ $hopDong->id }}",
                        type: 'hop_dong',
                        position: position,
                        signature: signatureData
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const areaId = position === 'employee' ? 'employee-signature-area' : 'company-signature-area';
                    const displayArea = document.querySelector(`#${areaId} .signature-display`);
                    displayArea.innerHTML = `<img src="${result.image_url}" class="signature-img">`;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Đã lưu chữ ký thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeSignatureModal();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: result.message || 'Không thể lưu chữ ký.'
                    });
                }
            } catch (error) {
                console.error('Error saving signature:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi hệ thống',
                    text: 'Đã có lỗi xảy ra khi kết nối máy chủ.'
                });
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        window.addEventListener("resize", resizeCanvas);

        // Standard auto-print logic disabled to allow for digital signature
        /*
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
        */
    </script>
</body>

</html>
