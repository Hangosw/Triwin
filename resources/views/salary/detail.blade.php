@extends('layouts.app')

@section('title', __('Employee Salary Details') . ' - ' . \App\Models\SystemConfig::getValue('company_name'))
@push('styles')
    <style>
        /* Dark Mode Overrides */
        body.dark-theme {
            --surface: #1a1d2d;
            --text-main: #e8eaf0;
            --text-muted: #8b93a8;
            --bg-body: #11131f;
        }

        body.dark-theme .card {
            background: #1a1d2d;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme h1, 
        body.dark-theme h2, 
        body.dark-theme h3 {
            color: #e8eaf0;
        }

        body.dark-theme .table thead th {
            background: #21263a;
            color: #c3c8da;
            border-color: #2e3349;
        }

        body.dark-theme .table td {
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .table tr[style*="background-color: #f9fafb"] {
            background-color: #21263a !important;
        }

        body.dark-theme .table tr[style*="background-color: #f0fdf4"],
        body.dark-theme .table tr[style*="background-color: #eff6ff"],
        body.dark-theme .table tr[style*="background-color: #fff7ed"],
        body.dark-theme .table tr[style*="background-color: #fee2e2"],
        body.dark-theme .table tr[style*="background-color: #dcfce7"] {
            background-color: rgba(255, 255, 255, 0.03) !important;
            filter: brightness(1.2);
        }

        body.dark-theme .form-control {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .btn-secondary, 
        body.dark-theme a.btn-secondary[style*="background-color: #6b7280"] {
            background-color: #2e3349 !important;
            color: #e8eaf0 !important;
            border: 1px solid #3f4662 !important;
        }

        body.dark-theme .btn-secondary:hover {
            background-color: #39405a !important;
        }

        body.dark-theme #slipModal > div {
            background: #1a1d2d !important;
        }

        body.dark-theme .alert {
            background: #1a1d2d;
            border-color: #2e3349;
        }

        body.dark-theme div[style*="border-bottom: 1px solid #e5e7eb"] {
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme div[style*="background: #f9fafb"] {
            background: #1a1d2d !important;
            border-color: #2e3349 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1>{{ __('Employee Salary Details') }}</h1>
            <p>{{ __('View detailed salary sheet for each employee by month') }}</p>
        </div>
        <a href="{{ route('salary.index', ['thang' => $thang, 'nam' => $nam]) }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; background-color: #6b7280; border: none; padding: 8px 16px; border-radius: 8px;">
            <i class="bi bi-arrow-left"></i>
            {{ __('Back to List') }}
        </a>
    </div>

    {{-- Hiển thị thông báo thành công hoặc lỗi --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @php
        $chucVu = $nhanVien->ttCongViec?->chucVu?->Ten ?? __('Not Available');
        $phongBan = $nhanVien->ttCongViec?->phongBan?->Ten ?? __('Not Available');

        // Dữ liệu từ LuongService (breakdown cho view chi tiết)
        $luongCoBan = $luong['luong_co_ban'] ?? 0;
        $tongPhuCap = $luong['tong_phu_cap'] ?? 0;
        $tongThuNhap = $luong['tong_thu_nhap'] ?? 0;
        $tongKhauTruBH = $luong['tong_khau_tru_bh'] ?? 0;
        $soNguoiPT = $luong['so_nguoi_phu_thuoc'] ?? 0;
        $tongGiamTru = $luong['tong_giam_tru'] ?? 0;
        $thuNhapChiuThue = $luong['thu_nhap_chiu_thue'] ?? 0;
        $thuNhapTinhThue = $luong['thu_nhap_tinh_thue'] ?? 0;
        $thueTNCN = $luong['thue_tncn'] ?? 0;
        $tongKhauTru = $luong['tong_khau_tru'] ?? 0;
        $luongThucNhan = $luong['luong_thuc_nhan'] ?? 0;

        // Ưu tiên hiển thị con số chính xác đã chốt trong Database (luongRecord)
        if (isset($luongRecord)) {
            $luongCoBan = $luongRecord->LuongCoBan ?? $luongCoBan;
            $tongPhuCap = $luongRecord->PhuCap ?? $tongPhuCap;
            $tongKhauTruBH = $luongRecord->KhauTruBaoHiem ?? $tongKhauTruBH;
            $thueTNCN = $luongRecord->ThueTNCN ?? $thueTNCN;
            $luongThucNhan = $luongRecord->Luong ?? $luongThucNhan;
            $soNguoiPT = $luongRecord->SoNguoiPhuThuoc ?? $soNguoiPT;
            $tamUng = $luongRecord->TamUng ?? 0;
        } else {
            $tamUng = $luong['tam_ung'] ?? 0;
        }

        $giamTruBanThan = \App\Services\LuongService::GIAM_TRU_BAN_THAN;
        $giamTruMoiNguoi = \App\Services\LuongService::GIAM_TRU_MOI_NGUOI;

        // Người phụ thuộc đã đăng ký giảm trừ gia cảnh
        $nguoiPhuThuocGiamTru = $thanNhans?->where('LaGiamTruGiaCanh', 1) ?? collect();

        // Chi tiết thuế lũy tiến (dùng cho hiển thị)
        $thueDetails = [];
        $bracketRates = [
            ['limit' => 5_000_000, 'rate' => 5, 'label' => __('Level 1 (≤ 5M)')],
            ['limit' => 10_000_000, 'rate' => 10, 'label' => __('Level 2 (5–10M)')],
            ['limit' => 18_000_000, 'rate' => 15, 'label' => __('Level 3 (10–18M)')],
            ['limit' => 32_000_000, 'rate' => 20, 'label' => __('Level 4 (18–32M)')],
            ['limit' => 52_000_000, 'rate' => 25, 'label' => __('Level 5 (32–52M)')],
            ['limit' => 80_000_000, 'rate' => 30, 'label' => __('Level 6 (52–80M)')],
            ['limit' => PHP_INT_MAX, 'rate' => 35, 'label' => __('Level 7 (> 80M)')],
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
                        {{ __('Employee ID') }}: {{ $nhanVien->Ma }} | {{ __($phongBan) }} - {{ __($chucVu) }}
                    </div>
                    <div style="font-size: 14px; color: #6b7280;">
                        {{ __('Contract') }}: {{ $hopDong?->SoHopDong ?? __('Not Available') }} |
                        {{ __('Type') }}: {{ $hopDong?->Loai ?? 'N/A' }}
                    </div>
                </div>
            </div>
            {{-- Chọn kỳ lương --}}
            <form method="GET" action="{{ route('salary.detail', $nhanVien->id) }}"
                style="display: flex; gap: 8px; align-items: flex-end; flex-direction: column;">
                <label style="font-size: 13px; color: #6b7280;">{{ __('Salary Period') }}</label>
                <div style="display: flex; gap: 8px;">
                    <select name="thang" class="form-control"
                        style="padding: 6px 10px; height: auto; font-size: 14px; font-weight: 600;"
                        onchange="this.closest('form').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $thang ? 'selected' : '' }}>
                                {{ __('Month') }} {{ $m }}
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
    @if($luongRecord)
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">{{ __('Salary Components') }}</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Value') }}</th>
                    <th>{{ __('Amount (VND)') }}</th>
                    <th>{{ __('Notes') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- A. LƯƠNG CƠ BẢN / LƯƠNG HỢP ĐỒNG --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4">
                        <strong>
                            A. {{ str_contains($luong['loai_nhan_vien_text'] ?? '', 'hợp đồng') ? __('Total contract salary') : __('Basic Salary') }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">
                        {{ str_contains($luong['loai_nhan_vien_text'] ?? '', 'hợp đồng') ? __('Total contract salary') : __('Basic Salary') }}
                    </td>
                    <td>
                        {{ str_contains($luong['loai_nhan_vien_text'] ?? '', 'hợp đồng') ? __('Fixed') : __('Salary based on workdays') }}
                    </td>
                    <td class="font-medium">{{ number_format($luong['luong_ngay_cong'] ?? $luongCoBan, 0, ',', '.') }}</td>
                    <td>
                        @if(str_contains($luong['loai_nhan_vien_text'] ?? '', 'hợp đồng'))
                            {{ __('Includes allowances') }}
                        @else
                            {{ number_format($luong['ngay_cong_thuc_te'], 2) }}/{{ $luong['ngay_cong_chuan'] }} {{ __('days') }}
                        @endif
                    </td>
                </tr>
                <tr style="background-color: #f0fdf4;">
                    <td><strong>{{ __('Total (A)') }}</strong></td>
                    <td></td>
                    <td class="text-primary font-bold">{{ number_format($luong['luong_ngay_cong'] ?? $luongCoBan, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- B. PHỤ CẤP --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>{{ __('B. ALLOWANCES AND BONUSES') }}</strong></td>
                </tr>
                @php
                    $allowances = [
                        __('Position Allowance') => $hopDong?->PhuCapChucVu ?? 0,
                        __('Responsibility Allowance') => $hopDong?->PhuCapTrachNhiem ?? 0,
                        __('Hazardous Allowance') => $hopDong?->PhuCapDocHai ?? 0,
                        __('Seniority Allowance') => $hopDong?->PhuCapThamNien ?? 0,
                        __('Area Allowance') => $hopDong?->PhuCapKhuVuc ?? 0,
                        __('Lunch Allowance') => $hopDong?->PhuCapAnTrua ?? 0,
                        __('Travel Allowance') => $hopDong?->PhuCapXangXe ?? 0,
                        __('Phone Allowance') => $hopDong?->PhuCapDienThoai ?? 0,
                        __('Other Allowance') => $hopDong?->PhuCapKhac ?? 0,
                    ];
                @endphp
                @foreach($allowances as $name => $amount)
                    @if($amount > 0)
                        <tr>
                            <td style="padding-left: 32px;">{{ $name }}</td>
                            <td>{{ __('Fixed/month') }}</td>
                            <td class="font-medium">{{ number_format($amount, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                @if($tongPhuCap == 0)
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #6b7280; font-style: italic;">{{ __('No allowances') }}</td>
                    </tr>
                @endif
                <tr style="background-color: #eff6ff;">
                    <td><strong>{{ __('Total allowances & bonuses') }}</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #3b82f6;">{{ number_format($tongPhuCap, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- C. KHẤU TRỪ BẢO HIỂM --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>{{ __('C. SOCIAL DEDUCTIONS') }}</strong></td>
                </tr>
                @foreach($baoHiems as $bh)
                    @php $deduction = ($luongCoBan * $bh->TiLeNhanVien) / 100; @endphp
                    <tr>
                        <td style="padding-left: 32px;">{{ __($bh->TenLoai) }} ({{ $bh->TiLeNhanVien }}%)</td>
                        <td>{{ $bh->TiLeNhanVien }}% × {{ number_format($luongCoBan, 0, ',', '.') }}</td>
                        <td class="font-medium" style="color: #dc2626;">-{{ number_format($deduction, 0, ',', '.') }}</td>
                        <td>{{ $bh->GhiChu }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #fee2e2;">
                    <td><strong>{{ __('Total SI/HI/UI deductions') }}</strong></td>
                    <td></td>
                    <td class="font-bold" style="color: #dc2626;">-{{ number_format($tongKhauTruBH, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- D. GIẢM TRỪ GIA CẢNH --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>{{ __('D. PERSONAL DEDUCTIONS (PIT)') }}</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">{{ __('Personal deduction') }}</td>
                    <td>{{ __('Fixed by law') }}</td>
                    <td class="font-medium" style="color: #16a34a;">{{ number_format($giamTruBanThan, 0, ',', '.') }}</td>
                    <td>{{ __('Resolution 954/2020/UBTVQH14') }}</td>
                </tr>
                @foreach($nguoiPhuThuocGiamTru as $pt)
                    <tr>
                        <td style="padding-left: 32px;">
                            {{ __('Dependent:') }} <strong>{{ $pt->HoTen }}</strong>
                            <span style="font-size: 12px; color: #6b7280;">({{ __($pt->QuanHe) }})</span>
                        </td>
                        <td>4.400.000 × 1</td>
                        <td class="font-medium" style="color: #16a34a;">{{ number_format($giamTruMoiNguoi, 0, ',', '.') }}</td>
                        <td>{{ __('Tax ID registered') }}</td>
                    </tr>
                @endforeach
                @if($soNguoiPT === 0)
                    <tr>
                        <td colspan="4" style="padding-left: 32px; color: #6b7280; font-style: italic;">
                            {{ __('No dependents registered for deduction') }}
                        </td>
                    </tr>
                @endif
                <tr style="background-color: #dcfce7;">
                    <td><strong>{{ __('Total family deductions') }}</strong></td>
                    <td><span style="color: #6b7280;">{{ __('Self + :count dependents', ['count' => $soNguoiPT]) }}</span></td>
                    <td class="font-bold" style="color: #16a34a;">{{ number_format($tongGiamTru, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                {{-- E. THUẾ TNCN --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="4"><strong>{{ __('E. PERSONAL INCOME TAX') }}</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 32px;">{{ __('Taxable Income') }}</td>
                    <td>{{ __('After family deductions') }}</td>
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
                            {{ __('Taxable income ≤ 0 → No PIT incurred') }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding-left: 32px;"><strong>{{ __('PIT Payable') }}</strong></td>
                    <td>{{ __('By progressive rates') }}</td>
                    <td class="font-medium" style="color: #dc2626;">-{{ number_format($thueTNCN, 0, ',', '.') }}</td>
                    <td>{{ __('After deductions') }}</td>
                </tr>
                {{-- F. TẠM ỨNG --}}
                @if($tamUng > 0)
                    <tr style="background-color: #f9fafb;">
                        <td colspan="4"><strong>{{ __('F. SALARY ADVANCE') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 32px;">{{ __('Salary advanced during the month') }}</td>
                        <td>{{ __('Approved payment') }}</td>
                        <td class="font-medium" style="color: #dc2626;">-{{ number_format($tamUng, 0, ',', '.') }}</td>
                        <td>{{ __('Paid in :month/:year', ['month' => $thang, 'year' => $nam]) }}</td>
                    </tr>
                @endif

                <tr style="background-color: #fee2e2;">
                    <td><strong>{{ __('Total Deductions') }}</strong></td>
                    <td>{{ __('SI + Tax + Advance') }}</td>
                    <td class="font-bold" style="color: #dc2626;">-{{ number_format($tongKhauTruBH + $thueTNCN + $tamUng, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- G. TỔNG CỘNG THỰC LĨNH --}}
    <div class="card" style="background: linear-gradient(135deg, #0BAA4B, #088c3d); color: white; margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
            <div>
                <h3 style="color: white; margin-bottom: 4px; font-size: 18px;">{{ __('Final Net Pay') }}</h3>
                <p style="font-size: 13px; opacity: 0.9; margin: 0;">{{ __('Actual amount paid via bank transfer/cash') }}</p>
            </div>
            <div style="text-align: right;">
                <h2 style="color: white; font-size: 32px; font-weight: 800; margin: 0;">{{ number_format($luongThucNhan, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>

        </div>
    </div>
    @else
        <div class="card" style="text-align: center; padding: 60px 20px; border: 2px dashed #e5e7eb; background: #f9fafb;">
            <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.5;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 80px; height: 80px; margin: 0 auto; color: #9ca3af;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 600; color: #374151; margin-bottom: 8px;">{{ __('No salary data for period :period', ['period' => "$thang/$nam"]) }}</h3>
            <p style="color: #6b7280; max-width: 400px; margin: 0 auto 24px;">
                {!! __('Salary data for employee <strong>:name</strong> for this month has not been finalized or initialized.', ['name' => $nhanVien->Ten]) !!}
            </p>
            
            <div style="display: flex; gap: 12px; justify-content: center;">
                @can('Xem Danh Sách Lương')
                    <form action="{{ route('salary.update-single', $nhanVien->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="thang" value="{{ $thang }}">
                        <input type="hidden" name="nam" value="{{ $nam }}">
                        <button type="submit" class="btn" style="background-color: #0BAA4B; color: white;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('Initialize & Calculate Now') }}
                        </button>
                    </form>
                @endcan
                <a href="{{ route('salary.index', ['thang' => $thang, 'nam' => $nam]) }}" class="btn btn-secondary">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    @endif

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
                    &nbsp;{{ __('Salary Slip') }}
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
                        {{ __('Print Slip') }}
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
                    <div>{{ __('Loading salary slip...') }}</div>
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
                            <div style="font-size:14px;">{{ __('Loading salary slip...') }}</div>
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
                                    <div>{{ __('Failed to load salary slip.') }}<br><small style="color:#9ca3af;">${err.message}</small></div>
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
                            <title>{{ __('Salary Slip') }}</title>
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
