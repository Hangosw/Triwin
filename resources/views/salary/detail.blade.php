@extends('layouts.app')

@section('title', 'Chi tiết lương nhân viên - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Chi tiết lương nhân viên</h1>
        <p>Xem chi tiết bảng lương của từng nhân viên theo tháng</p>
    </div>

    @php
        $chucVu = $nhanVien->ttCongViec?->chucVu?->Ten ?? 'Chưa có';
        $phongBan = $nhanVien->ttCongViec?->phongBan?->Ten ?? 'Chưa có';

        // Dữ liệu từ LuongService
        $luongCoBan = $luong['luong_co_ban'];
        $tongPhuCap = $luong['tong_phu_cap'];
        $tongTangCa = $luong['tong_tang_ca'];
        $tongThuNhap = $luong['tong_thu_nhap'];
        $tongKhauTruBH = $luong['tong_khau_tru_bh'];
        $soNguoiPT = $luong['so_nguoi_phu_thuoc'];
        $tongGiamTru = $luong['tong_giam_tru'];
        $thuNhapChiuThue = $luong['thu_nhap_chiu_thue'];
        $thuNhapTinhThue = $luong['thu_nhap_tinh_thue'];
        $thueTNCN = $luong['thue_tncn'];
        $tongKhauTru = $luong['tong_khau_tru'];
        $luongThucNhan = $luong['luong_thuc_nhan'];

        $giamTruBanThan = \App\Services\LuongService::GIAM_TRU_BAN_THAN;
        $giamTruMoiNguoi = \App\Services\LuongService::GIAM_TRU_MOI_NGUOI;

        // Người phụ thuộc đã đăng ký giảm trừ gia cảnh
        $nguoiPhuThuocGiamTru = $thanNhans?->where('LaGiamTruGiaCanh', 1) ?? collect();

        // Chi tiết thuế lũy tiến (dùng cho hiển thị)
        $thueDetails = [];
        $bracketRates = [
            ['limit' => 5_000_000, 'rate' => 5, 'label' => 'Bậc 1 (≤ 5 tr)'],
            ['limit' => 10_000_000, 'rate' => 10, 'label' => 'Bậc 2 (5–10 tr)'],
            ['limit' => 18_000_000, 'rate' => 15, 'label' => 'Bậc 3 (10–18 tr)'],
            ['limit' => 32_000_000, 'rate' => 20, 'label' => 'Bậc 4 (18–32 tr)'],
            ['limit' => 52_000_000, 'rate' => 25, 'label' => 'Bậc 5 (32–52 tr)'],
            ['limit' => 80_000_000, 'rate' => 30, 'label' => 'Bậc 6 (52–80 tr)'],
            ['limit' => PHP_INT_MAX, 'rate' => 35, 'label' => 'Bậc 7 (> 80 tr)'],
        ];
        $remaining = $thuNhapTinhThue;
        $prevLimit = 0;
        foreach ($bracketRates as $bracket) {
            if ($remaining <= 0)
                break;
            $taxable = min($remaining, $bracket['limit'] - $prevLimit);
            $tax = $taxable * $bracket['rate'] / 100;
            if ($tax > 0) {
                $thueDetails[] = [
                    'label' => $bracket['label'],
                    'taxable' => $taxable,
                    'rate' => $bracket['rate'],
                    'tax' => $tax,
                ];
            }
            $remaining -= $taxable;
            $prevLimit = $bracket['limit'];
        }

        // Tăng ca chi tiết trong tháng
        $tangCas = \App\Models\TangCa::with('loaiTangCa')
            ->where('NhanVienId', $nhanVien->id)
            ->where('TrangThai', 'da_duyet')
            ->whereYear('Ngay', $nam)
            ->whereMonth('Ngay', $thang)
            ->get();
        $ngayCongChuan = 26;
        $gioMoiNgay = 8;
        $luongGio = $luongCoBan / ($ngayCongChuan * $gioMoiNgay);
    @endphp

    {{-- Employee Info --}}
    <div class="card">
        <div
            style="display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; align-items: center; gap: 24px;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($nhanVien->Ten) }}&background=0F5132&color=fff"
                    alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 4px;">{{ $nhanVien->Ten }}</h2>
                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 2px;">
                        Mã NV: {{ $nhanVien->Ma }} | {{ $phongBan }} - {{ $chucVu }}
                    </div>
                    <div style="font-size: 14px; color: #6b7280;">
                        Hợp đồng: {{ $hopDong?->SoHopDong ?? 'Chưa có' }} |
                        Loại: {{ $hopDong?->Loai ?? 'N/A' }}
                    </div>
                </div>
            </div>
            {{-- Chọn kỳ lương --}}
            <form method="GET" action="{{ route('salary.detail', $nhanVien->id) }}"
                style="display: flex; gap: 8px; align-items: flex-end; flex-direction: column;">
                <label style="font-size: 13px; color: #6b7280;">Kỳ lương</label>
                <div style="display: flex; gap: 8px;">
                    <select name="thang" class="form-control"
                        style="padding: 6px 10px; height: auto; font-size: 14px; font-weight: 600;"
                        onchange="this.closest('form').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $thang ? 'selected' : '' }}>
                                Tháng {{ $m }}
                            </option>
                        @endfor
                    </select>
                    <select name="nam" class="form-control"
                        style="padding: 6px 10px; height: auto; font-size: 14px; font-weight: 600;"
                        onchange="this.closest('form').submit()">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Salary Components --}}
    <div class="card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Thành phần lương</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>Khoản mục</th>
                    <th>Giá trị</th>
                    <th>Số tiền (VNĐ)</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                {{-- A. LƯƠNG CƠ BẢN --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>A. LƯƠNG CƠ BẢN</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">Lương cơ bản</td>
                    <td>Hợp đồng</td>
                    <td class="font-medium">{{ number_format($luongCoBan, 0, ',', '.') }}</td>
                    <td>Mức lương chính</td>
                </tr>
                <tr style="background-color: #f0fdf4;">
                    <td><strong>Tổng lương cơ bản</strong></td>
                    <td></td>
                    <td class="text-primary font-bold">{{ number_format($luongCoBan, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- B. PHỤ CẤP --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>B. PHỤ CẤP VÀ THƯỞNG</strong></td>
                </tr>
                @php
                    $allowances = [
                        'Phụ cấp chức vụ' => $hopDong?->PhuCapChucVu ?? 0,
                        'Phụ cấp trách nhiệm' => $hopDong?->PhuCapTrachNhiem ?? 0,
                        'Phụ cấp độc hại' => $hopDong?->PhuCapDocHai ?? 0,
                        'Phụ cấp thâm niên' => $hopDong?->PhuCapThamNien ?? 0,
                        'Phụ cấp khu vực' => $hopDong?->PhuCapKhuVuc ?? 0,
                        'Phụ cấp ăn trưa' => $hopDong?->PhuCapAnTrua ?? 0,
                        'Phụ cấp xăng xe' => $hopDong?->PhuCapXangXe ?? 0,
                        'Phụ cấp điện thoại' => $hopDong?->PhuCapDienThoai ?? 0,
                        'Phụ cấp khác' => $hopDong?->PhuCapKhac ?? 0,
                    ];
                @endphp
                @foreach($allowances as $name => $amount)
                    @if($amount > 0)
                        <tr>
                            <td style="padding-left: 32px;">{{ $name }}</td>
                            <td>Cố định/tháng</td>
                            <td class="font-medium">{{ number_format($amount, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                @if($tongPhuCap == 0)
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #6b7280; font-style: italic;">Không có phụ cấp</td>
                    </tr>
                @endif
                <tr style="background-color: #eff6ff;">
                    <td><strong>Tổng phụ cấp & thưởng</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #3b82f6;">{{ number_format($tongPhuCap, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- C. TĂNG CA --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>C. TĂNG CA (đã duyệt trong tháng {{ $thang }}/{{ $nam }})</strong></td>
                </tr>
                @if($tangCas->isEmpty())
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #6b7280; font-style: italic;">
                            Không có tăng ca đã duyệt trong tháng này
                        </td>
                    </tr>
                @else
                    @foreach($tangCas as $tc)
                        @php
                            $heSo = $tc->loaiTangCa?->HeSo ?? 1.5;
                            $tenLoaiTC = $tc->loaiTangCa?->TenLoai ?? 'Tăng ca';
                            $tienTC = ($tc->Tong ?? 0) * $luongGio * $heSo;
                        @endphp
                        <tr>
                            <td style="padding-left: 32px;">
                                {{ $tenLoaiTC }}
                                <span style="font-size: 12px; color: #6b7280;">({{ $tc->Ngay->format('d/m') }})</span>
                            </td>
                            <td>{{ number_format($tc->Tong ?? 0, 1) }} giờ × {{ number_format($luongGio, 0, ',', '.') }} ×
                                {{ $heSo }}
                            </td>
                            <td class="font-medium">{{ number_format($tienTC, 0, ',', '.') }}</td>
                            <td>Hệ số {{ $heSo }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr style="background-color: #fff7ed;">
                    <td><strong>Tổng tăng ca</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #f97316;">{{ number_format($tongTangCa, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- D. KHẤU TRỪ BẢO HIỂM --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>D. KHẤU TRỪ XÃ HỘI</strong></td>
                </tr>
                @foreach($baoHiems as $bh)
                    @php $deduction = ($luongCoBan * $bh->TiLeNhanVien) / 100; @endphp
                    <tr>
                        <td style="padding-left: 32px;">{{ $bh->TenLoai }} ({{ $bh->TiLeNhanVien }}%)</td>
                        <td>{{ $bh->TiLeNhanVien }}% × {{ number_format($luongCoBan, 0, ',', '.') }}</td>
                        <td class="font-medium" style="color: #dc2626;">-{{ number_format($deduction, 0, ',', '.') }}</td>
                        <td>{{ $bh->GhiChu }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #fee2e2;">
                    <td><strong>Tổng khấu trừ BHXH/BHYT/BHTN</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #dc2626;">-{{ number_format($tongKhauTruBH, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- E. GIẢM TRỪ GIA CẢNH --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>E. GIẢM TRỪ GIA CẢNH (thuế TNCN)</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">Giảm trừ bản thân</td>
                    <td>Cố định theo luật</td>
                    <td class="font-medium" style="color: #16a34a;">{{ number_format($giamTruBanThan, 0, ',', '.') }}</td>
                    <td>Nghị quyết 954/2020/UBTVQH14</td>
                </tr>
                @foreach($nguoiPhuThuocGiamTru as $pt)
                    <tr>
                        <td style="padding-left: 32px;">
                            Người phụ thuộc: <strong>{{ $pt->HoTen }}</strong>
                            <span style="font-size: 12px; color: #6b7280;">({{ $pt->QuanHe }})</span>
                        </td>
                        <td>4.400.000 × 1</td>
                        <td class="font-medium" style="color: #16a34a;">{{ number_format($giamTruMoiNguoi, 0, ',', '.') }}</td>
                        <td>Đã đăng ký MST</td>
                    </tr>
                @endforeach
                @if($soNguoiPT === 0)
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #6b7280; font-style: italic;">
                            Không có người phụ thuộc được đăng ký giảm trừ
                        </td>
                    </tr>
                @endif
                <tr style="background-color: #dcfce7;">
                    <td><strong>Tổng giảm trừ gia cảnh</strong></td>
                    <td><span style="color: #6b7280;">Bản thân + {{ $soNguoiPT }} người phụ thuộc</span></td>
                    <td class="font-bold" style="color: #16a34a;">{{ number_format($tongGiamTru, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- F. THUẾ TNCN --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>F. THUẾ THU NHẬP CÁ NHÂN</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">Thu nhập tính thuế</td>
                    <td>Sau giảm trừ gia cảnh</td>
                    <td class="font-medium">{{ number_format($thuNhapTinhThue, 0, ',', '.') }}</td>
                    <td style="color: #6b7280; font-size: 13px;">
                        = {{ number_format($thuNhapChiuThue, 0, ',', '.') }} −
                        {{ number_format($tongGiamTru, 0, ',', '.') }}
                    </td>
                </tr>
                @foreach($thueDetails as $td)
                    <tr>
                        <td style="padding-left: 48px; font-size: 13px; color: #6b7280;">{{ $td['label'] }}</td>
                        <td style="font-size: 13px;">{{ $td['rate'] }}% × {{ number_format($td['taxable'], 0, ',', '.') }}</td>
                        <td style="font-size: 13px; color: #dc2626;">-{{ number_format($td['tax'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if($thueTNCN == 0)
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #16a34a; font-style: italic;">
                            Thu nhập tính thuế ≤ 0 → Không phát sinh thuế TNCN
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding-left: 32px;"><strong>Thuế TNCN phải nộp</strong></td>
                    <td>Theo bậc lũy tiến</td>
                    <td class="font-medium" style="color: #dc2626;">-{{ number_format($thueTNCN, 0, ',', '.') }}</td>
                    <td>Đã trừ giảm trừ gia cảnh</td>
                </tr>
                <tr style="background-color: #fee2e2;">
                    <td><strong>Tổng khấu trừ</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #dc2626;">-{{ number_format($tongKhauTru, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div class="card" style="background: linear-gradient(135deg, #0BAA4B 0%, #088c3d 100%); color: white;">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <div>
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Tổng thu nhập</div>
                <div style="font-size: 28px; font-weight: 700;">{{ number_format($tongThuNhap, 0, ',', '.') }} đ</div>
                <div style="font-size: 13px; opacity: 0.8; margin-top: 4px;">Cơ bản + Phụ cấp + Tăng ca</div>
            </div>
            <div>
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Tổng khấu trừ</div>
                <div style="font-size: 28px; font-weight: 700;">-{{ number_format($tongKhauTru, 0, ',', '.') }} đ</div>
                <div style="font-size: 13px; opacity: 0.8; margin-top: 4px;">BHXH + BHYT + BHTN + Thuế</div>
            </div>
            <div style="grid-column: span 2;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Lương thực lãnh</div>
                <div style="font-size: 36px; font-weight: 700;">{{ number_format($luongThucNhan, 0, ',', '.') }} đ</div>
                <div style="font-size: 13px; opacity: 0.8; margin-top: 4px;">
                    Số tiền nhận được trong tháng {{ $thang }}/{{ $nam }}
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid rgba(255, 255, 255, 0.3); padding-top: 16px; display: flex; gap: 12px;">
            <button class="btn btn-secondary btn-show-slip" data-nv-id="{{ $nhanVien->id }}" data-thang="{{ $thang }}"
                data-nam="{{ $nam }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                In phiếu lương
            </button>
            <button class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Gửi email cho nhân viên
            </button>
            <a href="{{ route('salary.index', ['thang' => $thang, 'nam' => $nam]) }}" class="btn btn-secondary">
                ← Quay lại danh sách
            </a>
        </div>
    </div>

    {{-- ========== MODAL PHIẾU LƯƠNG ========== --}}
    <div id="slipModal" style="
            display:none; position:fixed; inset:0; z-index:9999;
            background:rgba(0,0,0,0.55); align-items:center; justify-content:center;
            overflow-y:auto; padding:24px 16px;
        ">
        <div style="
                background:#fff; border-radius:12px; width:100%; max-width:860px;
                margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3);
                display:flex; flex-direction:column; max-height:90vh;
            ">
            {{-- Modal Header --}}
            <div style="
                    display:flex; justify-content:space-between; align-items:center;
                    padding:16px 20px; border-bottom:1px solid #e5e7eb;
                    background:linear-gradient(135deg,#0BAA4B,#088c3d);
                    border-radius:12px 12px 0 0;
                ">
                <div style="color:#fff; font-size:16px; font-weight:700;">
                    <i class="bi bi-file-earmark-text"></i>
                    &nbsp;Phiêu Lương
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button id="btnPrintSlip" style="
                            background:#fff; color:#0BAA4B; border:none; border-radius:6px;
                            padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer;
                            display:flex; align-items:center; gap:6px;
                        ">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        In phiếu
                    </button>
                    <button onclick="closeSlipModal()" style="
                            background:rgba(255,255,255,0.2); border:none; border-radius:6px;
                            color:#fff; font-size:20px; cursor:pointer; width:32px; height:32px;
                            display:flex; align-items:center; justify-content:center; line-height:1;
                        ">✕</button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div id="slipContent" style="padding:20px; overflow-y:auto; flex:1;">
                <div style="text-align:center; padding:40px; color:#6b7280;">
                    <div style="font-size:32px; margin-bottom:8px;">⏳</div>
                    <div>Đang tải phiêu lương...</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const slipModal = document.getElementById('slipModal');
                const slipContent = document.getElementById('slipContent');
                const btnPrint = document.getElementById('btnPrintSlip');

                const LOADING_HTML = `
                        <div style="text-align:center;padding:48px;color:#6b7280;">
                            <div style="font-size:36px;margin-bottom:10px;">⏳</div>
                            <div style="font-size:14px;">Đang tải phiêu lương...</div>
                        </div>`;

                function openSlipModal(nvId, thang, nam) {
                    slipContent.innerHTML = LOADING_HTML;
                    slipModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    fetch(`/salary/slip/${nvId}?thang=${thang}&nam=${nam}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(r => {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.text();
                        })
                        .then(html => { slipContent.innerHTML = html; })
                        .catch(err => {
                            slipContent.innerHTML = `
                                <div style="text-align:center;padding:48px;color:#dc2626;">
                                    <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
                                    <div>Không thể tải phiêu lương.<br><small style="color:#9ca3af;">${err.message}</small></div>
                                </div>`;
                        });
                }

                window.closeSlipModal = function () {
                    slipModal.style.display = 'none';
                    document.body.style.overflow = '';
                    slipContent.innerHTML = LOADING_HTML;
                };

                slipModal.addEventListener('click', function (e) {
                    if (e.target === slipModal) window.closeSlipModal();
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && slipModal.style.display === 'flex') {
                        window.closeSlipModal();
                    }
                });

                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.btn-show-slip');
                    if (btn) {
                        const nvId = btn.dataset.nvId;
                        const thang = btn.dataset.thang;
                        const nam = btn.dataset.nam;
                        openSlipModal(nvId, thang, nam);
                    }
                });

                btnPrint.addEventListener('click', function () {
                    const printWin = window.open('', '_blank', 'width=950,height=700');
                    printWin.document.write(`
                            <!DOCTYPE html><html><head>
                            <meta charset="UTF-8">
                            <title>Phiêu Lương</title>
                            <style>
                                body { font-family: Arial, sans-serif; font-size:13px; margin:20px; }
                                @media print { body { margin: 0; } }
                            </style>
                            <\/head><body>${slipContent.innerHTML}<\/body><\/html>`);
                    printWin.document.close();
                    printWin.focus();
                    setTimeout(() => { printWin.print(); }, 500);
                });
            });
        </script>
    @endpush
@endsection
