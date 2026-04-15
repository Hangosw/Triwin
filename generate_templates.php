<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$storagePath = 'storage/app/contracts/';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
}

// 1. Generate template_nda.docx (FULL VERSION)
$phpWordNDA = new PhpWord();
$section = $phpWordNDA->addSection([
    'marginTop' => 1134, // 20mm
    'marginBottom' => 1134,
    'marginLeft' => 1417, // 25mm
    'marginRight' => 1134,
]);

$headerStyle = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'];
$normalStyle = ['size' => 12, 'name' => 'Times New Roman'];
$boldStyle = ['bold' => true, 'size' => 12, 'name' => 'Times New Roman'];
$italicStyle = ['italic' => true, 'size' => 11, 'name' => 'Times New Roman'];
$centerStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
$justifyStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH];

$section->addText("THỎA THUẬN BẢO MẬT THÔNG TIN VÀ", $headerStyle, $centerStyle);
$section->addText("KHÔNG CẠNH TRANH", $headerStyle, $centerStyle);
$section->addTextBreak(1);

$section->addText("Thỏa Thuận Bảo Mật Thông Tin Và Không Cạnh Tranh này (sau đây gọi chung là “Thỏa Thuận”) được lập, xem xét, hiểu rõ và thống nhất ký kết, thực hiện giữa:", $normalStyle, $justifyStyle);
$section->addTextBreak(1);

$table = $section->addTable(['borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 80]);
$table->addRow();
$cellA = $table->addCell(4750);
$cellA->addText("BÊN SỞ HỮU THÔNG TIN", ['bold' => true, 'size' => 11], $centerStyle);
$cellA->addText("(dưới đây được gọi là \"Công Ty\")", ['italic' => true, 'size' => 10], $centerStyle);
$cellA->addText("CÔNG TY TNHH TRIWIN", ['bold' => true, 'size' => 11]);
$cellA->addText("ĐỊA CHỈ: M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM", $normalStyle);
$cellA->addText("ĐIỆN THOẠI: 0283.3622.6639", $normalStyle);
$cellA->addText("ĐẠI DIỆN: DIỆP THẾ CHINH", $normalStyle);
$cellA->addText("CHỨC VỤ: Giám đốc", $normalStyle);

$cellB = $table->addCell(4750);
$cellB->addText("BÊN ĐƯỢC BIẾT THÔNG TIN", ['bold' => true, 'size' => 11], $centerStyle);
$cellB->addText("(dưới đây được gọi là \"Nhân Viên\")", ['italic' => true, 'size' => 10], $centerStyle);
$cellB->addText("ÔNG/BÀ: \${TenNhanVien}", ['bold' => true, 'size' => 11]);
$cellB->addText("SINH NGÀY: \${NgaySinh}", $normalStyle);
$cellB->addText("HỘ KHẨU THƯỜNG TRÚ: \${DiaChiHK}", $normalStyle);
$cellB->addText("CHỖ Ở HIỆN TẠI: \${CHHT}", $normalStyle);
$cellB->addText("Số CCCD: \${SoCCCD}, cấp ngày \${NgayCapCCCD}", $normalStyle);

$section->addTextBreak(1);
$section->addText("(Công Ty hoặc Nhân Viên, nay gọi chung là \"Các Bên\", và gọi riêng là \"Bên\". Thuật ngữ \"Công Ty\" được sử dụng trong Thỏa Thuận này, tùy từng trường hợp cụ thể và ngữ cảnh phù hợp, sẽ bao gồm: Người đại diện theo pháp luật, các thành viên ban lãnh đạo, các nhân viên, các đại lý và các đại diện hợp pháp khác của Công Ty hoặc do Công Ty chỉ định).", $italicStyle, $justifyStyle);
$section->addTextBreak(1);

$section->addText("Căn cứ:", ['bold' => true, 'size' => 12, 'underline' => 'single']);
$section->addListItem("Điều Lệ của Công Ty;", 0, $normalStyle);
$section->addListItem("Hợp Đồng số \${SoHopDong} được ký kết giữa Công Ty và Nhân Viên ngày \${NgayBatDau} tháng \${ThangBatDau} năm \${NamBatDau} và các hợp đồng ký kết tiếp theo;", 0, $normalStyle);
$section->addListItem("Nội Quy Lao Động;", 0, $normalStyle);
$section->addListItem("Thỏa thuận của Các Bên;", 0, $normalStyle);
$section->addListItem("Nhằm duy trì hoạt động kinh doanh bền vững của Công Ty, và đảm bảo các bí mật kinh doanh của Công Ty được bảo vệ toàn vẹn.", 0, $normalStyle);

$section->addTextBreak(1);
$section->addText("Xét Rằng:", ['bold' => true, 'size' => 12, 'underline' => 'single']);
$section->addText("A. Nhân Viên hiện đang làm việc hợp pháp tại Công Ty. Với vai trò của mình, Nhân Viên đã tiếp cận, được cho phép tiếp cận, và đã, đang hoặc sẽ có được những thông tin mật về Công Ty và hoạt động kinh doanh, cũng như hoạt động nội bộ của Công Ty. Những thông tin mật này có giá trị quan trọng đối với Công Ty, và có khái niệm cơ bản như quy định tại Điều 1 dưới đây.", $normalStyle, $justifyStyle);
$section->addText("B. Sử dụng hoặc tiết lộ thông tin mật của Công Ty, không phải vì lợi ích của Công Ty, sẽ gây ra những nguy hại đáng kể cho Công Ty, và chỉ riêng những thiệt hại về tiền sẽ không đủ để bồi thường cho Công Ty về việc sử dụng hay tiết lộ các thông tin mật trái phép.", $normalStyle, $justifyStyle);
$section->addText("C. Trong quá trình làm việc tại Công Ty, Nhân Viên đã, đang và sẽ nhận khoản thù lao đáng kể theo Hợp Đồng Lao Động đã ký kết, hoặc quy chế hoạt động của Công Ty, và Công Ty có quyền lợi hợp pháp trong việc bảo vệ hoạt động kinh doanh và triển vọng tương lai của Công Ty.", $normalStyle, $justifyStyle);

$section->addTextBreak(1);
$section->addText("Nay, Các Bên đồng ý, thống nhất ký kết và thực hiện Thỏa Thuận này theo những điều khoản và điều kiện dưới đây.", $normalStyle, $justifyStyle);
$section->addTextBreak(1);

// Article 1
$section->addText("1. Một số khái niệm cơ bản:", $boldStyle);
$section->addText("1.1 Thông Tin Bảo Mật (sau đây gọi chung là \"Thông Tin Mật\") nghĩa là tất cả thông tin mật, không công khai, bất kể thông tin đó được lưu giữ hoặc giao, trao đổi hoặc có được trước, vào hoặc sau ngày ký Thỏa Thuận này liên quan đến hoạt động kinh doanh, công nghệ hoặc những vấn đề khác của Công Ty, bao gồm mọi bí mật kinh doanh (được định nghĩa như Khoản 10, Điều 3 của Luật Cạnh Tranh ban hành ngày 03/12/2004, được sửa đổi, bổ sung tùy từng thời điểm); hoạt động kinh doanh; kế hoạch kinh doanh; các thông tin, báo cáo tài chính, tiếp thị; hệ thống, công nghệ, ý tưởng, khái niệm, bí quyết, kỹ thuật, thiết kế, đặc tính kỹ thuật, bản thiết kế, hình vẽ, tên khách hàng và chi tiết liên hệ, bán và mua liên quan đến khách hàng và nhà cung cấp; biểu đồ, mô hình, chức năng, khả năng và thiết kế (bao gồm nhưng không giới hạn, phần mềm máy tính, quy trình sản xuất hoặc các thông tin khác có trong bản vẽ hoặc đặc tính kỹ thuật); sở hữu trí tuệ hoặc bất kỳ thông tin nào khác được cho biết là đối tượng có nghĩa vụ phải giữ bí mật, do Công Ty sở hữu hoặc sử dụng hoặc được cấp bản quyền cho Công Ty.", $normalStyle, $justifyStyle);
$section->addText("Để làm rõ thêm khái niệm về Thông Tin Mật, các nội dung sau cũng thuộc phạm vi là các Thông Tin Mật: các loại tài liệu, vật mẫu, mô hình, đĩa, băng và các loại thông tin và hồ sơ lưu trữ (tài liệu tài chính hoặc các loại khác); báo cáo; các khoản mục thông tin có liên quan đến một hoặc nhiều chương trình và các loại thông tin khác do Công Ty (bao gồm người đại diện theo pháp luật, các thành viên ban lãnh đạo, các nhân viên, các đại lý và các đại diện hợp pháp khác) sở hữu hoặc sử dụng nhằm mục đích phục vụ Công Ty; các dự án ở bất kỳ giai đoạn (đã được nghiệm thu của hội đồng thẩm định nội bộ của Công Ty, hoặc trong quá trình chuẩn bị hoặc đã, đang được đưa vào thực hiện thực tế; các thông tin phát sinh trong bất kỳ một giai đoạn nào của chu trình phát triển dự án bao gồm nhưng không giới hạn bởi việc tìm kiếm, phát hiện, đề xuất ý tưởng dự án, xây dựng các kế hoạch cụ thể để giải trình cho ý tưởng của (các) dự án; các quy trình xin cấp phép, các thủ tục thành lập, các báo cáo tài chính, báo cáo kế hoạch kinh doanh, nhân sự, danh sách khách hàng tiềm năng, các ý tưởng thiết kế, các mẫu thiết kế sản phẩm do Nhân Viên hoặc bên thứ ba cung cấp trong quá trình thực hiện hợp đồng lao động hoặc/và hợp đồng cung cấp dịch vụ cho Công Ty. Không ảnh hưởng đến những quy định khác của Thỏa Thuận này, tất cả những văn bản mang tiêu đề “ Bảo mật thông tin”, “Thông tin mật”, “Tối mật”, “Quyền truy cập tài liệu mật”, “Thông tin nhạy cảm”, “ Ý tưởng cần được bảo mật” đều được định nghĩa là thông tin mật theo nội dung của Thoả Thuận này.", $normalStyle, $justifyStyle);
$section->addText("1.2 Thông Tin Loại Trừ nghĩa là các thông tin có tính chất sau đây:", $normalStyle);
$section->addListItem("Những thông tin mà tại thời điểm ký bản Thỏa Thuận này đã, hoặc sau đó sẽ, được công bố rộng rãi mà không phải do Nhân Viên vi phạm Thỏa Thuận này hoặc do các hành vi trái pháp luật khác.", 0, $normalStyle);
$section->addListItem("Nhân Viên đã có được những thông tin này một cách độc lập từ một nguồn hợp pháp khác (\"Bên Thứ Ba\") không phải từ Công Ty hoặc những người đại diện, nhân viên khác của Công Ty...", 0, $normalStyle);
$section->addListItem("Nhân Viên phát triển một cách độc lập mà không có sự tiếp cận với các Thông Tin Mật.", 0, $normalStyle);

// Article 2
$section->addText("2. Xác định chủ sở hữu Thông Tin Mật", $boldStyle);
$section->addText("Trong các trường hợp Công Ty sử dụng Nhân Viên với tư cách là người lao động, chuyên gia, nhà thầu của Công Ty để thực hiện các công việc phục vụ các mục đích của Công Ty...", $normalStyle, $justifyStyle);

// Article 3
$section->addText("3. Tiết lộ Thông Tin Mật", $boldStyle);
$section->addText("3.1 Nhân Viên đồng ý không tiết lộ bất kỳ Thông Tin Mật nào cho bất kỳ người nào ngoại trừ: (a) Với sự chấp thuận bằng văn bản của Công Ty; hoặc (b) Theo quy định của pháp luật hoặc theo yêu cầu của cơ quan nhà nước có thẩm quyền...", $normalStyle, $justifyStyle);

// Article 4
$section->addText("4. Trách nhiệm cơ bản của Nhân Viên khi sử dụng Thông Tin Mật", $boldStyle);
$section->addListItem("Bảo đảm an toàn và giữ bí mật nghiêm ngặt tất cả các Thông Tin Mật...", 1, $normalStyle, 'decimal');
$section->addListItem("Không thực hiện sao chép, biên tập hoặc chuyển hóa nội dung của bất kỳ Thông Tin Mật nào...", 1, $normalStyle, 'decimal');
$section->addListItem("Hạn chế việc sử dụng các Thông Tin Mật...", 1, $normalStyle, 'decimal');
$section->addListItem("Không tiết lộ hay tạo điều kiện cho bất kỳ bên thứ ba nào tiếp cận...", 1, $normalStyle, 'decimal');
$section->addListItem("Ngay sau khi nhận được yêu cầu của Công Ty, Nhân Viên phải ngay lập tức gửi trả hoặc hủy...", 1, $normalStyle, 'decimal');
$section->addListItem("Trường hợp Nhân làm tiết lộ hoặc thất thoát Thông Tin Mật gây thiệt hại, Nhân Viên phải bồi thường...", 1, $normalStyle, 'decimal');

// Article 5
$section->addText("5. Trả lại Thông Tin Mật", $boldStyle);
$section->addText("Nếu Nhân Viên không còn là nhân viên của Công Ty, Nhân Viên phải ngay lập tức giao và trả lại cho Công Ty mọi tài liệu, thông tin...", $normalStyle, $justifyStyle);

// Article 6
$section->addText("6. Phỏng Vấn Nhân Viên trước khi Nhân Viên chính thức rời, bỏ Công Ty", $boldStyle);
$section->addText("Trước khi Nhân Viên chính thức rời khỏi Công Ty, Nhân Viên có nghĩa vụ thu xếp thời gian để thực hiện một cuộc phỏng vấn nhân viên...", $normalStyle, $justifyStyle);

// Article 7
$section->addText("7. Các nghĩa vụ tiếp diễn trong việc bảo mật Thông Tin Mật", $boldStyle);
$section->addText("Các quyền và nghĩa vụ bảo mật sẽ tiếp tục có hiệu lực và ràng buộc đối với Nhân Viên kể cả sau khi chấm dứt quan hệ lao động...", $normalStyle, $justifyStyle);

// Article 8
$section->addText("8. Cam kết không cạnh tranh", $boldStyle);
$section->addText("Nhân Viên đồng ý chừng nào mình còn là nhân viên của Công Ty và trong thời hạn 05 (Năm) năm kể từ ngày Nhân Viên thôi việc:", $normalStyle, $justifyStyle);
$section->addListItem("Nhân viên sẽ không tham gia vào hoạt động kinh doanh cạnh tranh với Công Ty trên toàn lãnh thổ Việt Nam...", 0, $normalStyle);
$section->addListItem("Nhân Viên sẽ không thu hút hoặc lôi kéo khách hàng, đối tác của Công Ty...", 0, $normalStyle);
$section->addListItem("Nhân Viên sẽ không thu hút hoặc lôi kéo nhân viên của Công Ty...", 0, $normalStyle);
$section->addListItem("Nhân Viên sẽ không sử dụng tên thương mại, logo của Công Ty gây nhầm lẫn.", 0, $normalStyle);

// Article 9
$section->addText("9. Thông báo cho bên thứ ba về sự tồn tại của Thỏa Thuận", $boldStyle);
$section->addText("Công Ty có quyền thông báo cho bất cứ doanh nghiệp nào có ý định tuyển dụng Nhân Viên về sự tồn tại của bản Thoả Thuận này.", $normalStyle, $justifyStyle);

// Article 10
$section->addText("10. Vi phạm Thỏa Thuận", $boldStyle);
$section->addListItem("Nếu Nhân Viên không tuân theo, Công Ty có quyền áp dụng các biện pháp cưỡng chế hợp lý...", 1, $normalStyle, 'decimal');
$section->addListItem("Nhân Viên cam kết chịu trách nhiệm trước pháp luật và bồi thường mọi thiệt hại.", 1, $normalStyle, 'decimal');

// Article 11
$section->addText("11. Hiệu lực và Thời hạn của Thỏa Thuận", $boldStyle);
$section->addText("11.1 Bản Thỏa Thuận này có hiệu lực kể từ ngày ký kết và ràng buộc các bên.", $normalStyle, $justifyStyle);
$section->addText("11.2 Thời hạn bắt đầu từ ngày ký cho đến khi nhân viên thôi việc hoặc có thỏa thuận mới.", $normalStyle, $justifyStyle);

// Article 12
$section->addText("12. Điều khoản khác", $boldStyle);
$section->addListItem("Thỏa Thuận được điều chỉnh bởi pháp luật Việt Nam. Tranh chấp giải quyết theo hòa giải hoặc tòa án.", 1, $normalStyle, 'decimal');
$section->addListItem("Mọi sửa đổi phải lập thành văn bản.", 1, $normalStyle, 'decimal');
$section->addListItem("Lập thành 02 bản, mỗi bên giữ 01 bản.", 1, $normalStyle, 'decimal');
$section->addListItem("Thay thế các thỏa thuận cũ.", 1, $normalStyle, 'decimal');

$section->addTextBreak(2);
$section->addText("TP.HCM ngày \${NgayPL} tháng \${ThangPL} năm \${NamPL}", $normalStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
$section->addTextBreak(1);

$section->addText("Cam kết và ký bởi Nhân Viên:", ['bold' => true, 'italic' => true, 'size' => 12]);
$section->addText("Chữ ký:", $normalStyle);
$section->addText("\${ChuKyNhanVien}", $normalStyle);
$section->addTextBreak(2);
$section->addText("Họ và tên: \${TenNhanVien}", ['bold' => true, 'size' => 12]);
$section->addText("Chức danh: \${ChucDanh}", $normalStyle);
$section->addText("Phòng ban: \${PhongBan}", $normalStyle);

$objWriter = IOFactory::createWriter($phpWordNDA, 'Word2007');
$objWriter->save($storagePath . 'template_nda.docx');

// 2. Generate template_phu_luc.docx (NEW FORMAT)
$phpWordPL = new PhpWord();
$sectionPL = $phpWordPL->addSection([
    'marginTop' => 1134,
    'marginBottom' => 1134,
    'marginLeft' => 1417,
    'marginRight' => 1134,
]);

$sectionPL->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM", $boldStyle, $centerStyle);
$sectionPL->addText("Độc lập – Tự do – Hạnh phúc", $boldStyle, $centerStyle);
$sectionPL->addText("----------------------------", $normalStyle, $centerStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("PHỤ LỤC HỢP ĐỒNG LAO ĐỘNG", $headerStyle, $centerStyle);
$sectionPL->addText("(Kèm theo HĐLĐ số: \${SoHopDong})", $normalStyle, $centerStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("Hôm nay, ngày \${NgayKy} tháng \${ThangKy} năm \${NamKy}, Tại văn phòng công ty TNHH TRIWIN", $normalStyle);
$sectionPL->addText("Chúng tôi gồm có:", $normalStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("BÊN SỬ DỤNG LAO ĐỘNG (BÊN A):", $boldStyle);
$sectionPL->addText("Bên A: CÔNG TY TNHH TRIWIN", $boldStyle);
$sectionPL->addText("Đại diện: \${TenDaiDien}", $normalStyle);
$sectionPL->addText("Chức vụ: \${ChucVuDaiDien}", $normalStyle);
$sectionPL->addText("Địa chỉ: M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM", $normalStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("NGƯỜI LAO ĐỘNG (BÊN B):", $boldStyle);
$sectionPL->addText("Bên B: ông/bà \${TenNhanVien}", $boldStyle);
$sectionPL->addText("Sinh ngày: \${NgaySinh}     Quốc tịch: Việt Nam", $normalStyle);
$sectionPL->addText("Địa chỉ thường trú: \${DiaChi}", $normalStyle);
$sectionPL->addText("Số thẻ căn cước: \${SoCCCD}, cấp ngày \${NgayCap}", $normalStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("Căn cứ Hợp đồng lao động số \${SoHopDong} ký ngày \${NgayKyHD} và nhu cầu sử dụng lao động, hai bên cùng nhau thỏa thuận ký kết Phụ lục hợp đồng lao động (PLHĐ) với các điều khoản như bên dưới, PLHĐ này là một phần không tách rời của hợp đồng.", $normalStyle, $justifyStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("ĐIỀU 1: CÁC NỘI DUNG THAY ĐỔI, BỔ SUNG", $boldStyle);
$sectionPL->addText("1.1 Thay đổi về mức phụ cấp phúc lợi:", $normalStyle);
$sectionPL->addText("- Tổng các khoản phụ cấp: \${TongPhuCap} VNĐ/tháng (Bằng chữ: \${TongPhuCapChu}).", $normalStyle);
$sectionPL->addText("Chi tiết các khoản phụ cấp bao gồm:", $normalStyle);

// Allowance table (Hidden borders, for dynamic rows)
$tablePL = $sectionPL->addTable(['borderColor' => 'ffffff', 'borderSize' => 0, 'cellMargin' => 0]);
$tablePL->addRow();
$cell = $tablePL->addCell(9500);
$cell->addText("\${dong_phu_cap}", $normalStyle); // Placeholder for the whole line: "1.2.1 Tiền ăn trưa: 770.000 đồng/ tháng"

$sectionPL->addTextBreak(1);
$sectionPL->addText("1.2 Tiền thưởng cuối năm: Lương tháng 13 (Theo quy định của công ty).", $normalStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("ĐIỀU 2: THỜI GIAN THỰC HIỆN", $boldStyle);
$sectionPL->addText("Từ ngày \${NgayBatDau}", $normalStyle);
$sectionPL->addTextBreak(1);

$sectionPL->addText("ĐIỀU 3: CÁC ĐIỀU KHOẢN KHÁC", $boldStyle);
$sectionPL->addText("Các điều kiện và nội dung khác của Hợp đồng lao động không thay đổi và vẫn giữ nguyên giá trị hiệu lực.", $normalStyle, $justifyStyle);
$sectionPL->addText("Phụ lục này được lập thành 02 (hai) bản có giá trị pháp lý như nhau, mỗi bên giữ 01 (một) bản.", $normalStyle, $justifyStyle);

$sectionPL->addTextBreak(2);

$tableSign = $sectionPL->addTable(['cellMargin' => 0]);
$tableSign->addRow();
$cellA = $tableSign->addCell(4750);
$cellA->addText("BÊN A", $boldStyle, $centerStyle);
$cellA->addText("(Ký, đóng dấu, ghi rõ họ tên)", $italicStyle, $centerStyle);
$cellA->addText("\${ChuKyDaiDien}", $normalStyle, $centerStyle);
$cellA->addText("\${TenDaiDien}", $boldStyle, $centerStyle);

$cellB = $tableSign->addCell(4750);
$cellB->addText("BÊN B", $boldStyle, $centerStyle);
$cellB->addText("(Ký, ghi rõ họ tên)", $italicStyle, $centerStyle);
$cellB->addText("\${ChuKyNhanVien}", $normalStyle, $centerStyle);
$cellB->addText("\${TenNhanVien}", $boldStyle, $centerStyle);

$objWriterPL = IOFactory::createWriter($phpWordPL, 'Word2007');
$objWriterPL->save($storagePath . 'template_phu_luc.docx');

echo "\nPhu Luc Template updated successfully in " . $storagePath;

// 3. Generate template_hop_dong_lao_dong.docx (FULL LBR CONTRACT)
$phpWordHD = new PhpWord();
$sectionHD = $phpWordHD->addSection([
    'marginTop' => 1134,
    'marginBottom' => 1134,
    'marginLeft' => 1417,
    'marginRight' => 1134,
]);

$sectionHD->addText("CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM", $boldStyle, $centerStyle);
$sectionHD->addText("Độc lập – Tự do – Hạnh phúc", $boldStyle, $centerStyle);
$sectionHD->addText("----------------------------", $normalStyle, $centerStyle);
$sectionHD->addTextBreak(1);

$sectionHD->addText("Tên đơn vị: CÔNG TY TNHH TRIWIN", $normalStyle);
$sectionHD->addText("Số: \${SoHopDong}", $normalStyle);
$sectionHD->addTextBreak(1);
$sectionHD->addText("HỢP ĐỒNG LAO ĐỘNG", $headerStyle, $centerStyle);
$sectionHD->addTextBreak(1);

$sectionHD->addText("Chúng tôi, một bên là Ông/Bà: \${TenDaiDien}, Quốc tịch: Việt Nam", $normalStyle);
$sectionHD->addText("Chức vụ: \${ChucVuDaiDien}", $normalStyle);
$sectionHD->addText("Đại diện cho: CÔNG TY TNHH TRIWIN", $normalStyle);
$sectionHD->addText("Điện thoại: 0283.3622.6639", $normalStyle);
$sectionHD->addText("Địa chỉ: M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM", $normalStyle);
$sectionHD->addTextBreak(1);

$sectionHD->addText("Và một bên là Ông/Bà: \${TenNhanVien}, Quốc tịch: Việt Nam", $normalStyle);
$sectionHD->addText("Sinh ngày: \${NgaySinh}    tại: Việt Nam", $normalStyle);
$sectionHD->addText("Nghề nghiệp: \${ChucDanh}", $normalStyle);
$sectionHD->addText("Địa chỉ thường trú: \${DiaChi}", $normalStyle);
$sectionHD->addText("Số CMND/CCCD: \${SoCCCD}  Cấp ngày: \${NgayCap}   Nơi cấp: \${NoiCap}", $normalStyle);
$sectionHD->addTextBreak(1);

$sectionHD->addText("Thoả thuận ký kết hợp đồng lao động và cam kết làm đúng những điều khoản sau đây:", $italicStyle);
$sectionHD->addTextBreak(1);

// Điều 1
$sectionHD->addText("Điều 1: THỜI HẠN VÀ CÔNG VIỆC HỢP ĐỒNG", $boldStyle);
$sectionHD->addListItem("Loại hợp đồng lao động: \${TenLoaiHD}", 0, $normalStyle);
$sectionHD->addListItem("Từ ngày: \${NgayBatDau}", 0, $normalStyle);
$sectionHD->addListItem("Địa điểm làm việc: M2 đường số 5, KDC Cityland, Phường Tân Phú, Quận 7, TP.HCM", 0, $normalStyle);
$sectionHD->addListItem("Chức danh chuyên môn: \${ChucDanhChiTiet} (nếu có)", 0, $normalStyle);
$sectionHD->addListItem("Công việc phải làm: Liên quan đến chuyên môn và những công việc khác do Giám đốc (hoặc cá nhân được giám đốc ủy quyền) phân công theo quy định của Pháp luật.", 0, $normalStyle);

// Điều 2
$sectionHD->addText("Điều 2: CHẾ ĐỘ LÀM VIỆC", $boldStyle);
$sectionHD->addText("1) Thời giờ làm việc:", $boldStyle);
$sectionHD->addListItem("Trong ngày: 8h/ngày - 44h/tuần, Sáng từ 8h đến 12h, Chiều từ 1h đến 5h.", 0, $normalStyle);
$sectionHD->addText("2) Thời gian nghỉ:", $boldStyle);
$sectionHD->addListItem("Nghỉ hàng năm, nghỉ lễ, tết, nghỉ việc riêng. Theo quy định của luật lao động.", 0, $normalStyle);
$sectionHD->addListItem("Tùy theo yêu cầu công việc công ty có thể điều động làm việc ngoài giờ.", 0, $normalStyle);
$sectionHD->addListItem("Điều kiện an toàn vệ sinh lao động tại nơi làm việc theo quy định của pháp luật hiện hành.", 0, $normalStyle);

// Điều 3
$sectionHD->addText("Điều 3: NGHĨA VỤ VÀ QUYỀN LỢI CỦA NGƯỜI LAO ĐỘNG", $boldStyle);
$sectionHD->addText("1. Quyền lợi:", $boldStyle);
$sectionHD->addListItem("Phương tiện đi lại làm việc: tự túc", 0, $normalStyle);
$sectionHD->addListItem("Mức lương chính: \${LuongCoBan} VNĐ/tháng (tại thời điểm ký hợp đồng).", 0, $normalStyle);
$sectionHD->addListItem("Các hỗ trợ phúc lợi khác theo quy định của Công ty.", 0, $normalStyle);
$sectionHD->addListItem("Hình thức trả lương: tiền mặt.", 0, $normalStyle);
$sectionHD->addListItem("Được trang bị bảo hộ lao động: Theo công việc được phân công.", 0, $normalStyle);
$sectionHD->addListItem("Tiền thưởng lễ, tết: được hưởng theo quy chế lương thưởng của Công ty.", 0, $normalStyle);
$sectionHD->addListItem("Chế độ nghỉ ngơi (nghỉ hàng tuần, phép năm, lễ tết...): theo quy định của Nhà nước.", 0, $normalStyle);

$sectionHD->addText("2. Nghĩa vụ:", $boldStyle);
$sectionHD->addListItem("Hoàn thành những công việc đã cam kết trong hợp đồng lao động.", 0, $normalStyle);
$sectionHD->addListItem("Chấp hành lệnh điều hành sản xuất - kinh doanh, nội quy kỷ luật lao động, an toàn lao động.", 0, $normalStyle);
$sectionHD->addListItem("Thực hiện công việc theo đúng chức danh chuyên môn dưới sự quản lý của Ban Giám đốc.", 0, $normalStyle);
$sectionHD->addListItem("Phối hợp cùng các bộ phận khác để phát huy tối đa hiệu quả công việc.", 0, $normalStyle);
$sectionHD->addListItem("Tham dự đầy đủ các buổi huấn luyện, đào tạo do Công ty tổ chức.", 0, $normalStyle);
$sectionHD->addListItem("Nắm rõ và chấp hành nghiêm túc kỷ luật lao động, PCCC, nội quy lao động.", 0, $normalStyle);
$sectionHD->addListItem("Bồi thường vi phạm và vật chất: Theo qui định của Cty và Luật lao động.", 0, $normalStyle);

// Điều 4
$sectionHD->addText("Điều 4: NGHĨA VỤ VÀ QUYỀN HẠN CỦA NGƯỜI SỬ DỤNG LAO ĐỘNG", $boldStyle);
$sectionHD->addText("1. Nghĩa vụ:", $boldStyle);
$sectionHD->addListItem("Bảo đảm việc làm và thực hiện đầy đủ những điều đã cam kết trong hợp đồng lao động.", 0, $normalStyle);
$sectionHD->addListItem("Thanh toán đầy đủ, đúng thời hạn các chế độ cho người lao động.", 0, $normalStyle);
$sectionHD->addText("2. Quyền hạn:", $boldStyle);
$sectionHD->addListItem("Điều hành người lao động hoàn thành công việc theo hợp đồng.", 0, $normalStyle);
$sectionHD->addListItem("Tạm hoãn, chấm dứt hợp đồng, kỷ luật người lao động theo quy định của Pháp luật.", 0, $normalStyle);

// Điều 5
$sectionHD->addText("Điều 5: ĐIỀU KHOẢN THI HÀNH", $boldStyle);
$sectionHD->addText("Những vấn đề không ghi trong hợp đồng này thì áp dụng nội quy lao động và Pháp Luật lao động. Hợp đồng lao động được làm thành 02 bản có giá trị ngang nhau, mỗi bên giữ một bản.", $normalStyle, $justifyStyle);
$sectionHD->addTextBreak(1);
$sectionHD->addText("Hợp đồng này làm tại văn phòng công ty ngày \${NgayKy} tháng \${ThangKy} năm \${NamKy}.", $normalStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
$sectionHD->addTextBreak(1);

$tableHD = $sectionHD->addTable();
$tableHD->addRow();
$cellX = $tableHD->addCell(4750);
$cellX->addText("NGƯỜI LAO ĐỘNG", $boldStyle, $centerStyle);
$cellX->addText("(Ký, ghi rõ họ tên)", $italicStyle, $centerStyle);
$cellX->addTextBreak(2);
$cellX->addText("\${TenNhanVien}", $boldStyle, $centerStyle);

$cellY = $tableHD->addCell(4750);
$cellY->addText("NGƯỜI SỬ DỤNG LAO ĐỘNG", $boldStyle, $centerStyle);
$cellY->addText("(Ký tên, đóng dấu)", $italicStyle, $centerStyle);
$cellY->addTextBreak(2);
$cellY->addText("\${TenDaiDien}", $boldStyle, $centerStyle);

$objWriterHD = IOFactory::createWriter($phpWordHD, 'Word2007');
$objWriterHD->save($storagePath . 'template_hop_dong.docx');

echo "\nLabor Contract Template generated successfully in " . $storagePath;
