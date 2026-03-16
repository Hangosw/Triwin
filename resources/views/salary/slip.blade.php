<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xuất Phiếu Lương - Công ty TNHH Cao Su Bình Long</title>
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
            padding: 10px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: normal;
        }

        .title {
            color: red;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
            margin-top: 10px;
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
            color: #0000ff;
        }

        /* Layout cho phần chi tiết lương */
        .salary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
        }

        .section-title {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }

        .total-row {
            font-size: 18px;
            color: #0000ff;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="payslip-container">
        <div class="header">
            <div class="company-name">Công ty Phần Mềm Việt Tín</div>
        </div>
        <div class="title">PHIẾU LƯƠNG THÁNG 12/2020</div>

        <table>
            <tr>
                <td colspan="2">Họ tên: <span class="bold">PHẠM THỊ CHÚC LY</span></td>
                <td>Mã NV: <span class="bold">VTS5311</span></td>
            </tr>
            <tr>
                <td>Chức danh: Công nhân</td>
                <td>Số TK: <span class="bold">0631000449323</span></td>
                <td>Mã BP: <span class="bold">XƯỞNG 04-ĐÁ</span></td>
            </tr>
            <tr>
                <td>Nhận việc: 19/03/2016</td>
                <td class="text-center">MST TNCN: <br> 8396543222</td>
                <td>Số người phụ thuộc: </td>
                <td>Lương HĐLĐ: <span class="bold">4,410,000</span></td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr class="bold">
                <td width="33%">Công chính hưởng P/C <span style="float:right; color:red">25</span></td>
                <td width="33%" class="text-center">Hỗ trợ phụ cấp</td>
                <td width="33%" class="text-center">Các khoản khấu trừ</td>
            </tr>
            <tr>
                <td>
                    <div style="display:flex; justify-content:space-between;">
                        <span>Công chính</span>
                        <span class="bold">17.50</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Lương chính</span>
                        <span class="bold">3,850,000</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Công làm thêm</span>
                        <span class="bold">10.25</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Lương làm thêm</span>
                        <span class="bold">2,255,000</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Thu nhập khác</span>
                        <span></span>
                    </div>
                </td>
                <td>
                    <div style="display:flex; justify-content:space-between;">
                        <span>Thưởng chuyên cần</span>
                        <span></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Hỗ trợ nhà trọ</span>
                        <span class="bold">100,000</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Hỗ trợ đi lại (PhuCapXangXe)</span>
                        <span class="bold">134,615</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Phép năm</span>
                        <span class="bold">220,000</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Chăm sóc sức khỏe: độc hại</span>
                        <span></span>
                    </div>
                </td>
                <td>
                    <div style="display:flex; justify-content:space-between;">
                        <span>BHXH ( 8%)</span>
                        <span class="bold">352,800</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>BHYT ( 4.5%)</span>
                        <span class="bold">198,450</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>BHTN ( 1%)</span>
                        <span class="bold">44,100</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Thuế TNCN</span>
                        <span></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:10px;">
                        <span>Khác</span>
                        <span></span>
                    </div>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="33%"><span class="blue-text bold">Thực lĩnh lương (1)</span></td>
                <td width="10%" class="bold">5,964,265</td>
                <td colspan="2"><span class="blue-text bold">P/cấp công tác (2)</span></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td>Tổng cộng (1)+(2)</td>
                <td colspan="4">5,964,265</td>
            </tr>
            <tr>
                <td height="40px" valign="middle"><span class="bold">Ghi chú</span></td>
                <td colspan="4"></td>
            </tr>
        </table>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        <button onclick="window.print()">Xuất PDF / In Phiếu Lương</button>
    </div>

</body>

</html>
