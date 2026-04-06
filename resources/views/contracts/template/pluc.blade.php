@php
    $nv = $hopDong->nhanVien;
    $nguoiKy = $hopDong->nguoiKy;
    $ngayKy = \Carbon\Carbon::parse($phuLuc->ngay_ky);
    $ngayKyHopDong = \Carbon\Carbon::parse($hopDong->NgayBatDau);
    
    // Tính tổng phụ cấp từ phụ lục
    $tongPhuCap = $phuLuc->dieuKhoans->sum('pivot.so_tien');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phụ lục hợp đồng - {{ $nv->Ten }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            line-height: 1.6;
            color: #000;
            background: #f3f4f6;
            margin: 0;
            padding: 20px 0;
            font-size: 13pt;
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

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-weight: bold;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 20px;
        }
        .bold {
            font-weight: bold;
        }
        
        /* Signature styles */
        .footer-sign {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: center;
        }
        .signature-display {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signature-img {
            max-height: 100px;
            max-width: 200px;
        }

        @media print {
            body { background: none; padding: 0; }
            .container { 
                margin: 0; 
                padding: 15mm 20mm; 
                box-shadow: none; 
                width: 100%;
                max-width: none;
            }
            .no-print { display: none !important; }
        }

        /* UI Controls */
        .no-print-controls {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .print-btn {
            padding: 10px 25px;
            font-size: 15px;
            cursor: pointer;
            background: #6b7280;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .sign-btn {
            background: #0BAA4B;
        }
        .print-btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Signature Modal Styles (Synced with NDA) */
        .signature-modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
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
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .signature-pad-wrapper {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            margin: 15px 0;
            touch-action: none;
        }
        #signature-pad { width: 100%; height: 200px; cursor: crosshair; }
        .swal2-container { z-index: 3000 !important; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="no-print no-print-controls">
        <button onclick="window.print()" class="print-btn">In phụ lục</button>
        @if($canSign)
            <button class="print-btn sign-btn" onclick="openSignatureModal()">Ký số</button>
        @endif
    </div>

    {{-- Signature Modal --}}
    <div id="signatureModal" class="signature-modal no-print">
        <div class="signature-modal-content">
            <h3 style="margin-top:0; color:#0BAA4B;">Ký số phụ lục</h3>

            @if($isAdmin)
                <div style="margin-bottom:15px;">
                    <label style="font-weight:600; display:block; margin-bottom:8px;">Chọn vị trí ký:</label>
                    <div style="display:flex; gap:20px;">
                        <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <input type="radio" name="sign_position" value="employee" checked> Người lao động
                        </label>
                        <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <input type="radio" name="sign_position" value="company"> Đại diện công ty
                        </label>
                    </div>
                </div>
            @endif

            <div class="signature-pad-wrapper">
                <canvas id="signature-pad"></canvas>
            </div>

            <div style="display:flex; justify-content:space-between; margin-top:20px;">
                <button onclick="closeSignatureModal()" style="background:#f3f4f6; border:1px solid #d1d5db; padding:8px 16px; border-radius:6px; cursor:pointer;">Hủy</button>
                <div>
                    <button onclick="clearSignature()" style="background:white; border:1px solid #d1d5db; padding:8px 16px; border-radius:6px; cursor:pointer; margin-right:8px;">Xóa trắng</button>
                    <button onclick="saveSignature()" id="btn-save-signature" style="background:#0BAA4B; color:white; border:none; padding:8px 20px; border-radius:6px; cursor:pointer; font-weight:600;">Hoàn tất</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="title">PHỤ LỤC HỢP ĐỒNG LAO ĐỘNG</div>
            <div style="margin-top: 5px;">(Kèm theo HĐLĐ số: <span class="bold">{{ $hopDong->SoHopDong }}</span>)</div>
        </div>

        <div class="section">
            <p>Hôm nay, ngày {{ $ngayKy->day }} tháng {{ $ngayKy->month }} năm {{ $ngayKy->year }}, Tại văn phòng công ty TNHH TRIWIN</p>
            <p>Chúng tôi gồm có:</p>
        </div>

        <div class="section">
            <div class="bold">BÊN SỬ DỤNG LAO ĐỘNG (BÊN A):</div>
            <div>Bên A: <span class="bold">CÔNG TY TNHH TRIWIN</span></div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Đại diện:</div>
                <div class="bold">{{ $nguoiKy->Ten }}</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Chức vụ:</div>
                <div>{{ $nguoiKy->ttCongViec->chucVu->TenChucVu ?? 'Giám đốc' }}</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Địa chỉ:</div>
                <div>M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM</div>
            </div>
        </div>

        <div class="section">
            <div class="bold">NGƯỜI LAO ĐỘNG (BÊN B):</div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Ông/Bà:</div>
                <div class="bold">{{ strtoupper($nv->Ten) }}</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Sinh ngày:</div>
                <div>{{ \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') }} &nbsp;&nbsp;&nbsp; Quốc tịch: Việt Nam</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Địa chỉ:</div>
                <div>{{ $nv->DiaChi }}</div>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="min-width: 100px;">Số CCCD:</div>
                <div>{{ $nv->SoCCCD }}, cấp ngày {{ $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '...' }}</div>
            </div>
        </div>

        <div class="section" style="text-align: justify;">
            <p>Căn cứ Hợp đồng lao động số <span class="bold">{{ $hopDong->SoHopDong }}</span> ký ngày {{ $ngayKyHopDong->format('d/m/Y') }} và nhu cầu sử dụng lao động, hai bên cùng nhau thỏa thuận ký kết Phụ lục hợp đồng lao động (PLHĐ) với các điều khoản như sau:</p>
            
            <p><span class="bold">ĐIỀU 1: CÁC NỘI DUNG THAY ĐỔI, BỔ SUNG</span></p>
            <p>1.1 Thay đổi về mức phụ cấp phúc lợi:</p>
            <p>- Tổng các khoản phụ cấp: <span class="bold">{{ number_format($tongPhuCap, 0, ',', '.') }} VNĐ/tháng</span>.</p>
            <p>Chi tiết các khoản phụ cấp bao gồm:</p>
            <div style="margin-left: 20px; margin-top: 10px;">
                @foreach($phuLuc->dieuKhoans as $index => $dk)
                    <p style="margin: 5px 0;">1.2.{{ $index + 1 }} {{ $dk->noi_dung }}: <span class="bold">{{ number_format($dk->pivot->so_tien, 0, ',', '.') }} đồng/ tháng</span></p>
                @endforeach
            </div>
            
            <p>1.2 Tiền thưởng cuối năm: Lương tháng 13 (Theo quy định của công ty).</p>
            
            <p><span class="bold">ĐIỀU 2: THỜI GIAN THỰC HIỆN</span></p>
            <p>- Áp dụng từ ngày {{ $ngayKyHopDong->format('d/m/Y') }} đến khi có phụ lục hợp đồng điều chỉnh mới.</p>
        </div>

        <div class="section" style="text-align: justify;">
            <p>Phụ lục này là bộ phận không tách rời của hợp đồng lao động số {{ $hopDong->SoHopDong }} được làm thành hai bản có giá trị pháp lý như nhau, mỗi bên giữ một bản.</p>
        </div>

        <div class="footer-sign">
            <div id="employee-signature-area">
                <div class="bold">NGƯỜI LAO ĐỘNG</div>
                <div style="font-style: italic; font-size: 11pt;">(Ký và ghi rõ họ tên)</div>
                <div class="signature-display" style="height: 100px;">
                    @if($kySo && $kySo->chu_ky_nhan_vien)
                        <img src="{{ asset('storage/' . $kySo->chu_ky_nhan_vien) }}" class="signature-img">
                    @endif
                </div>
                <div class="bold">{{ $nv->Ten }}</div>
            </div>
            <div id="company-signature-area">
                <div class="bold">ĐẠI DIỆN CÔNG TY</div>
                <div style="font-style: italic; font-size: 11pt;">(Ký tên và đóng dấu)</div>
                <div class="signature-display" style="height: 100px;">
                    @if($kySo && $kySo->chu_ky_dai_dien)
                        <img src="{{ asset('storage/' . $kySo->chu_ky_dai_dien) }}" class="signature-img">
                    @endif
                </div>
                <div class="bold">{{ $nguoiKy->Ten }}</div>
            </div>
        </div>
    </div>

    {{-- Scripting --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let signaturePad;
        const canvas = document.getElementById('signature-pad');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function openSignatureModal() {
            document.getElementById('signatureModal').style.display = 'flex';
            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255,255,1,0)',
                    penColor: 'rgb(0,0,0)'
                });
                resizeCanvas();
            } else {
                signaturePad.clear();
            }
        }

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
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

            const btn = document.getElementById('btn-save-signature');
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const signatureData = signaturePad.toDataURL();
            const positionRadio = document.querySelector('input[name="sign_position"]:checked');
            const position = positionRadio ? positionRadio.value : 'employee';

            try {
                const response = await fetch("{{ route('hop-dong.save-signature') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: "{{ $phuLuc->id }}",
                        type: 'phu_luc',
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
                btn.innerText = 'Hoàn tất';
            }
        }

        window.addEventListener('resize', resizeCanvas);
    </script>
</body>
</html>