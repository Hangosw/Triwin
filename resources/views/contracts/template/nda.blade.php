<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thỏa Thuận Bảo Mật Thông Tin Và Không Cạnh Tranh</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }

        .no-print-header {
            background: #fff;
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-print { background: #6b7280; color: #fff; }
        .btn-word { background: #2b579a; color: #fff; }
        .btn-sign-trigger { background: #0BAA4B; color: #fff; }
        
        .btn-action:hover { opacity: 0.9; transform: translateY(-1px); }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 25mm 20mm 25mm 25mm;
            box-sizing: border-box;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .title-block {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .title-text {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            border: 2px solid #000;
            padding: 12px 20px;
            width: 70%;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table td {
            border: 1px solid #000;
            padding: 10px 12px;
            vertical-align: top;
        }

        .party-label {
            font-weight: bold;
            font-style: italic;
            display: block;
            margin-bottom: 2px;
        }

        .intro {
            margin-bottom: 15px;
            text-align: justify;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: none;
        }

        .underline {
            text-decoration: underline;
        }

        ul.bullet-list {
            list-style: disc;
            padding-left: 35px;
            margin: 10px 0;
        }

        ul.bullet-list li {
            margin-bottom: 8px;
            text-align: justify;
        }

        ol.roman-list {
            list-style: lower-roman;
            padding-left: 35px;
            margin: 10px 0;
        }

        ol.roman-list li {
            margin-bottom: 10px;
            text-align: justify;
        }

        ol.alpha-list {
            list-style: lower-alpha;
            padding-left: 35px;
            margin: 10px 0;
        }

        ol.alpha-list li {
            margin-bottom: 10px;
            text-align: justify;
        }

        .indent {
            padding-left: 0px;
        }

        .signature-block {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-date {
            margin-bottom: 25px;
        }

        @media print {
            .no-print-header, .no-print { display: none !important; }
            body { background: none; margin: 0; padding: 0; }
            .page { 
                margin: 0; 
                box-shadow: none; 
                width: 100%; 
                padding: 15mm 20mm;
                min-height: auto;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }

        /* Signature Modal Styles */
        .signature-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            justify-content: center;
            align-items: center;
        }
        .signature-modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .signature-pad-wrapper {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            margin: 15px 0;
            touch-action: none;
        }
        #signature-pad { width: 100%; height: 200px; cursor: crosshair; }
        .signature-img { max-height: 80px; display: block; margin: 0 auto; }
        .swal2-container { z-index: 3000 !important; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="no-print-header">
        <button onclick="window.print()" class="btn-action btn-print">In Thỏa Thuận</button>
        <a href="{{ route('hop-dong.download-nda-word', $hopDong->id) }}" class="btn-action btn-word">Tải Word (.docx)</a>
        @if($canSign)
            <button onclick="openSignatureModal()" class="btn-action btn-sign-trigger">Ký số</button>
        @endif
    </div>

    {{-- Signature Modal --}}
    <div id="signatureModal" class="signature-modal no-print">
        <div class="signature-modal-content">
            <h3 style="margin-top:0; color:#0BAA4B;">Ký số Thỏa thuận</h3>

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

    <div class="page">
        {{-- TIÊU ĐỀ --}}
        <div class="title-block">
            <div class="title-text">
                THỎA THUẬN BẢO MẬT THÔNG TIN VÀ<br>
                KHÔNG CẠNH TRANH
            </div>
        </div>

        {{-- PHẦN MỞ ĐẦU --}}
        <p class="intro">
            Thỏa Thuận Bảo Mật Thông Tin Và Không Cạnh Tranh này (sau đây gọi chung là <strong>“Thỏa Thuận”</strong>) được lập, xem xét, hiểu rõ và thống nhất ký kết, thực hiện giữa:
        </p>

        {{-- BẢNG THÔNG TIN CÁC BÊN --}}
        <table>
            <tr>
                <td style="width:50%">
                    <span class="party-label">BÊN SỞ HỮU THÔNG TIN</span>
                    <em>(dưới đây được gọi là "Công Ty")</em>
                </td>
                <td style="width:50%">
                    <span class="party-label">BÊN ĐƯỢC BIẾT THÔNG TIN</span>
                    <em>(dưới đây được gọi là "Nhân Viên")</em>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>CÔNG TY TNHH TRIWIN</strong><br>
                    <strong>ĐỊA CHỈ:</strong> M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM<br>
                    <strong>ĐIỆN THOẠI:</strong> 0283.3622.6639<br>
                    <strong>ĐẠI DIỆN:</strong> DIÊP THẾ CHINH<br>
                    <strong>CHỨC VỤ:</strong> Giám đốc
                </td>
                <td>
                    <strong>ÔNG/BÀ:</strong> {{ $employee_name ?? '................................' }}<br>
                    <strong>SINH NĂM:</strong> {{ $employee_dob ?? '................................' }}<br>
                    <strong>HỘ KHẨU THƯỜNG TRÚ:</strong> {{ $employee_permanent_address ?? '................................' }}<br>
                    <strong>CHỎ Ở HIỆN TẠI:</strong> {{ $employee_current_address ?? '................................' }}<br>
                    <strong>Số CCCD:</strong> {{ $employee_cccd ?? '................................' }}<br>
                    cấp ngày {{ $employee_cccd_date ?? '................................' }}
                </td>
            </tr>
        </table>

        <p class="intro" style="font-style: italic; font-size: 11pt;">
            (Công Ty hoặc Nhân Viên, nay gọi chung là "Các Bên", và gọi riêng là "Bên". Thuật ngữ "Công Ty" được sử dụng trong Thỏa Thuận này, tùy từng trường hợp cụ thể và ngữ cảnh phù hợp, sẽ bao gồm: Người đại diện theo pháp luật, các thành viên ban lãnh đạo, các nhân viên, các đại lý và các đại diện hợp pháp khác của Công Ty hoặc do Công Ty chỉ định).
        </p>

        <p><span class="underline"><strong>Căn cứ:</strong></span></p>
        <ul class="bullet-list">
            <li>Điều Lệ của Công Ty;</li>
            <li>Hợp Đồng số {{ $contract_number ?? '.......' }} được ký kết giữa Công Ty và Nhân Viên ngày {{ $contract_date ?? '.......' }} và các hợp đồng ký kết tiếp theo;</li>
            <li>Nội Quy Lao Động;</li>
            <li>Thỏa thuận của Các Bên;</li>
            <li>Nhằm duy trì hoạt động kinh doanh bền vững của Công Ty, và đảm bảo các bí mật kinh doanh của Công Ty được bảo vệ toàn vẹn.</li>
        </ul>

        <p><span class="underline"><strong>Xét Rằng:</strong></span></p>
        <ol class="alpha-list">
            <li>Nhân Viên hiện đang làm việc hợp pháp tại Công Ty. Với vai trò của mình, Nhân Viên đã tiếp cận, được cho phép tiếp cận, và đã, đang hoặc sẽ có được những thông tin mật về Công Ty và hoạt động kinh doanh, cũng như hoạt động nội bộ của Công Ty. Những thông tin mật này có giá trị quan trọng đối với Công Ty, và có khái niệm cơ bản như quy định tại Điều 1 dưới đây.</li>
            <li>Sử dụng hoặc tiết lộ thông tin mật của Công Ty, không phải vì lợi ích của Công Ty, sẽ gây ra những nguy hại đáng kể cho Công Ty, và chỉ riêng những thiệt hại về tiền sẽ không đủ để bồi thường cho Công Ty về việc sử dụng hay tiết lộ các thông tin mật trái phép.</li>
            <li>Trong quá trình làm việc tại Công Ty, Nhân Viên đã, đang và sẽ nhận khoản thù lao đáng kể theo Hợp Đồng Lao Động đã ký kết, hoặc quy chế hoạt động của Công Ty, và Công Ty có quyền lợi hợp pháp trong việc bảo vệ hoạt động kinh doanh và triển vọng tương lai của Công Ty.</li>
        </ol>

        <p><span class="underline"><strong>Nay,</strong></span> Các Bên đồng ý, thống nhất ký kết và thực hiện Thỏa Thuận này theo những điều khoản và điều kiện dưới đây.</p>

        <p class="section-title">1. Một số khái niệm cơ bản:</p>
        <div class="indent">
            <p><strong>1.1 Thông Tin Bảo Mật</strong> (sau đây gọi chung là "Thông Tin Mật") nghĩa là tất cả thông tin mật, không công khai, bất kể thông tin đó được lưu giữ hoặc giao, trao đổi hoặc có được trước, vào hoặc sau ngày ký Thỏa Thuận này liên quan đến hoạt động kinh doanh, công nghệ hoặc những vấn đề khác của Công Ty, bao gồm mọi bí mật kinh doanh; hoạt động kinh doanh; kế hoạch kinh doanh; các thông tin, báo cáo tài chính, tiếp thị; hệ thống, công nghệ, ý tưởng, khái niệm, bí quyết, kỹ thuật, thiết kế, đặc tính kỹ thuật, bản thiết kế, hình vẽ, tên khách hàng và chi tiết liên hệ, bán và mua liên quan đến khách hàng và nhà cung cấp; biểu đồ, mô hình, chức năng, khả năng và thiết kế; sở hữu trí tuệ hoặc bất kỳ thông tin nào khác được cho biết là đối tượng có nghĩa vụ phải giữ bí mật, do Công Ty sở hữu hoặc sử dụng hoặc được cấp bản quyền cho Công Ty.</p>
            <p>Để làm rõ thêm khái niệm về Thông Tin Mật, các nội dung sau cũng thuộc phạm vi là các Thông Tin Mật: các loại tài liệu, vật mẫu, mô hình, đĩa, băng và các loại thông tin và hồ sơ lưu trữ; báo cáo; các khoản mục thông tin có liên quan đến một hoặc nhiều chương trình và các loại thông tin khác do Công Ty sở hữu hoặc sử dụng nhằm mục đích phục vụ Công Ty; các dự án ở bất kỳ giai đoạn nào; các thông tin phát sinh trong bất kỳ một giai đoạn nào của chu trình phát triển dự án bao gồm nhưng không giới hạn bởi việc tìm kiếm, phát hiện, đề xuất ý tưởng dự án, xây dựng các kế hoạch cụ thể; các quy trình xin cấp phép, các thủ tục thành lập, các báo cáo tài chính, báo cáo kế hoạch kinh doanh, nhân sự, danh sách khách hàng tiềm năng, các ý tưởng thiết kế, các mẫu thiết kế sản phẩm do Nhân Viên cung cấp trong quá trình thực hiện hợp đồng lao động.</p>
            <p><strong>1.2 Thông Tin Loại Trừ</strong> nghĩa là các thông tin mà tại thời điểm ký bản Thỏa Thuận này đã, hoặc sau đó sẽ, được công bố rộng rãi mà không phải do Nhân Viên vi phạm Thỏa Thuận này; Nhân Viên đã có được những thông tin này một cách độc lập từ một nguồn hợp pháp khác không phải từ Công Ty; Nhân Viên phát triển một cách độc lập mà không có sự tiếp cận với các Thông Tin Mật.</p>
        </div>

        <p class="section-title">2. Xác định chủ sở hữu Thông Tin Mật</p>
        <div class="indent">
            <p>Trong các trường hợp Công Ty sử dụng Nhân Viên để thực hiện các công việc phục vụ các mục đích của Công Ty, những thông tin này hoàn toàn thuộc quyền sở hữu của Công Ty, trừ trường hợp có thoả thuận khác bằng văn bản. Công Ty có quyền chiếm hữu, sử dụng và định đoạt các Thông Tin Mật, và yêu cầu Nhân Viên có trách nhiệm bảo mật theo đúng quy định của Thỏa Thuận này.</p>
        </div>

        <p class="section-title">3. Tiết lộ Thông Tin Mật</p>
        <div class="indent">
            <p>3.1 Nhân Viên đồng ý không tiết lộ bất kỳ Thông Tin Mật nào cho bất kỳ người nào ngoại trừ: (a) Với sự chấp thuận bằng văn bản của Công Ty; hoặc (b) Theo yêu cầu của cơ quan nhà nước có thẩm quyền và phải thông báo cho Công Ty.</p>
            <p>3.2 Nhân Viên phải cố gắng hết mình để đảm bảo rằng những người nhận Thông Tin Mật hiểu được tính chất mật của thông tin đó và không tiết lộ trái phép.</p>
        </div>

        <p class="section-title">4. Trách nhiệm của Nhân Viên khi sử dụng Thông Tin Mật</p>
        <ol class="roman-list">
            <li>Bảo đảm an toàn và giữ bí mật nghiêm ngặt tất cả các Thông Tin Mật.</li>
            <li>Không thực hiện sao chép, biên tập hoặc chuyển hóa nội dung khi chưa có sự đồng ý.</li>
            <li>Hạn chế việc sử dụng trong phạm vi cần thiết theo yêu cầu công việc.</li>
            <li>Không tiết lộ hay tạo điều kiện cho bất kỳ bên thứ ba nào tiếp cận Thông Tin Mật.</li>
            <li>Ngay lập tức gửi trả hoặc hủy Thông Tin Mật khi có yêu cầu của Công Ty.</li>
            <li>Bồi thường mọi thiệt hại gây ra cho Công Ty do vi phạm Thỏa Thuận này.</li>
        </ol>

        <p class="section-title">5. Trả lại Thông Tin Mật</p>
        <div class="indent">
            <p>Khi chấm dứt quan hệ lao động, Nhân Viên phải ngay lập tức trả lại cho Công Ty mọi tài liệu, thông tin có chứa đựng Thông Tin Mật. Việc trả lại chỉ được coi là hoàn thành khi có biên bản ký xác nhận.</p>
        </div>

        <p class="section-title">6. Phỏng Vấn Nhân Viên trước khi rời Công Ty</p>
        <div class="indent">
            <p>Trước khi chính thức nghỉ việc, Nhân Viên có nghĩa vụ thực hiện cuộc phỏng vấn với ban lãnh đạo để hoàn tất việc bàn giao các tài sản và thông tin mật.</p>
        </div>

        <p class="section-title">7. Nghĩa vụ tiếp diễn</p>
        <div class="indent">
            <p>Nghĩa vụ bảo mật Thông Tin Mật của Nhân Viên vẫn tiếp tục có hiệu lực sau khi chấm dứt hợp đồng lao động và kéo dài cho đến khi các thông tin đó không còn là bí mật.</p>
        </div>

        <p class="section-title">8. Cam kết không cạnh tranh</p>
        <div class="indent">
            <p>Nhân Viên cam kết trong thời gian làm việc và 05 (năm) năm sau khi nghỉ việc:</p>
            <ol class="alpha-list">
                <li>Không trực tiếp hoặc gián tiếp tham gia hoạt động kinh doanh cạnh tranh với Công Ty trên toàn lãnh thổ Việt Nam.</li>
                <li>Không lôi kéo khách hàng, đối tác hiện tại hoặc tiềm năng của Công Ty.</li>
                <li>Không lôi kéo nhân viên của Công Ty sang làm việc cho tổ chức khác.</li>
                <li>Không sử dụng nhãn hiệu, logo gây nhầm lẫn với Công Ty.</li>
            </ol>
        </div>

        <p class="section-title">9. Thông báo cho bên thứ ba</p>
        <div class="indent">
            <p>Công Ty có quyền thông báo cho bất kỳ bên thứ ba nào (bao gồm cả nhà tuyển dụng mới của Nhân Viên) về sự tồn tại của Thỏa Thuận này.</p>
        </div>

        <p class="section-title">10. Vi phạm Thỏa Thuận</p>
        <ol class="roman-list">
            <li>Mọi hành vi vi phạm sẽ bị xử lý theo quy định của Công Ty và pháp luật.</li>
            <li>Nhân Viên cam kết bồi thường toàn bộ thiệt hại và chi trả các chi phí pháp lý phát sinh do vi phạm.</li>
        </ol>

        <p class="section-title">11. Hiệu lực và Thời hạn</p>
        <div class="indent">
            <p>Thỏa Thuận có hiệu lực kể từ ngày ký và ràng buộc các bên cho đến khi có thỏa thuận thay thế bằng văn bản.</p>
        </div>

        <p class="section-title">12. Điều khoản chung</p>
        <ol class="roman-list">
            <li>Mọi tranh chấp phát sinh sẽ được giải quyết qua thương lượng, nếu không thành sẽ đưa ra tòa án có thẩm quyền.</li>
            <li>Thỏa Thuận được lập thành 02 bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</li>
        </ol>

        <div class="signature-block">
            <p class="signature-date">
                TP.HCM ngày {{ $sign_day ?? '.......' }} tháng {{ $sign_month ?? '.......' }} năm {{ $sign_year ?? '.......' }}
            </p>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%; text-align: center;" id="company-signature-area">
                    <strong>ĐẠI DIỆN CÔNG TY</strong><br>
                    <em>(Ký, đóng dấu, ghi rõ họ tên)</em>
                    <div style="height: 100px; display: flex; align-items: center; justify-content: center;" class="sig-display">
                        @if($kySo && $kySo->chu_ky_dai_dien)
                            <img src="{{ asset('storage/' . $kySo->chu_ky_dai_dien) }}" class="signature-img">
                        @endif
                    </div>
                    <strong>DIÊP THẾ CHINH</strong>
                </div>
                <div style="width: 45%; text-align: center;" id="employee-signature-area">
                    <strong>NHÂN VIÊN</strong><br>
                    <em>(Ký, ghi rõ họ tên)</em>
                    <div style="height: 100px; display: flex; align-items: center; justify-content: center;" class="sig-display">
                        @if($kySo && $kySo->chu_ky_nhan_vien)
                            <img src="{{ asset('storage/' . $kySo->chu_ky_nhan_vien) }}" class="signature-img">
                        @endif
                    </div>
                    <strong>{{ $employee_name ?? '................................' }}</strong>
                </div>
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
                    backgroundColor: 'rgba(255,255,255,0)',
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
                        id: "{{ $hopDong->id }}",
                        type: 'hop_dong',
                        position: position,
                        signature: signatureData
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const areaId = position === 'employee' ? 'employee-signature-area' : 'company-signature-area';
                    const displayArea = document.querySelector(`#${areaId} .sig-display`);
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