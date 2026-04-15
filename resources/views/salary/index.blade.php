@extends('layouts.app')

@section('title', 'Danh sách lương - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Danh sách lương</h1>
        <p>Quản lý và xem danh sách lương của toàn bộ nhân viên</p>
    </div>

    <style>
        /* Đồng bộ chiều cao tất cả các filter */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
        }

        .action-bar {
            padding: 16px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .filter-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
            gap: 16px;
            flex-wrap: wrap;
        }

        .buttons-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        body.dark-theme .buttons-row {
            border-top-color: #2e3349;
        }

        /* Custom Colors for Salary Redesign */
        .col-phu-cap {
            background-color: #e0f2fe !important;
            /* Light Blue */
        }

        .col-khau-tru {
            background-color: #fee2e2 !important;
            /* Light Red */
            cursor: pointer;
            transition: all 0.2s;
        }

        .col-khau-tru:hover {
            background-color: #fecaca !important;
        }

        .col-thuc-nhan {
            background-color: #dcfce7 !important;
            /* Light Green */
            color: #166534 !important;
            cursor: pointer;
            font-weight: 700;
            text-align: center;
            transition: all 0.2s;
        }

        .col-thuc-nhan:hover {
            background-color: #bbf7d0 !important;
        }

        .col-thuc-nhan strong {
            color: #166534 !important;
        }

        /* Insurance Detail Columns */
        .ins-detail {
            background-color: #fff1f2 !important;
            /* Lighter Red for details */
            font-size: 12px;
            text-align: right;
            color: #9f1239;
        }

        .group-header-row {
            background-color: #f8fafc !important;
        }

        body.dark-theme .group-header-row {
            background-color: #1a1e2e !important;
        }

        .custom-filter-dropdown {
            min-width: 150px;
        }

        .custom-filter-dropdown .form-control {
            cursor: pointer;
            height: 38px;
            background-color: #fff;
            padding: 0.375rem 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        body.dark-theme .custom-filter-dropdown .form-control {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        .search-bar {
            flex: 1;
            min-width: 300px;
        }

        /* Dark mode specific fixes */
        body.dark-theme .col-thuc-nhan {
            background-color: #064e3b !important;
            color: #6ee7b7 !important;
        }

        body.dark-theme .col-thuc-nhan strong {
            color: #6ee7b7 !important;
        }

        /* Table overrides */
        .table-container {
            overflow-x: auto;
        }

        .salary-table {
            border-collapse: collapse !important;
            width: 100% !important;
            table-layout: fixed !important;
            border: 1px solid #e5e7eb !important;
        }

        .salary-table th,
        .salary-table td {
            border: 1px solid #e5e7eb !important;
            padding: 5px 7px !important;
            vertical-align: middle !important;
            font-size: 12px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .salary-table th {
            background-color: #f8fafc;
            font-weight: 700;
            text-align: center;
            font-size: 11px !important;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .salary-table .salary-row td {
            line-height: 1.4;
        }

        /* Ki luong plain text style */
        .ki-luong-text {
            font-size: 11px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 0.04em;
        }

        body.dark-theme .ki-luong-text {
            color: #818cf8;
        }

        body.dark-theme .salary-table,
        body.dark-theme .salary-table th,
        body.dark-theme .salary-table td {
            border-color: #2e3349 !important;
        }

        /* Fix text alignment in dropdown grid */
        .custom-filter-dropdown .dropdown-menu button {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 34px;
        }

        /* Dark mode specific fixes */
        body.dark-theme .custom-filter-dropdown .dropdown-menu {
            background-color: #1a1e2e !important;
            border-color: #2e3349 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-theme .custom-filter-dropdown .dropdown-text {
            color: #e8eaf0 !important;
        }

        body.dark-theme .custom-filter-dropdown .form-control .bi-calendar3 {
            color: #9ca3af !important;
        }

        body.dark-theme .dropdown-menu .mb-2 span {
            color: #9ca3af !important;
        }

        body.dark-theme .dropdown-menu .mb-2 {
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .btn-light {
            background-color: #21263a !important;
            color: #e8eaf0 !important;
            border: none !important;
        }

        body.dark-theme .btn-light:hover {
            background-color: #2d334d !important;
        }

        /* Search bar focus and placeholder */
        body.dark-theme #salarySearch {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme #salarySearch::placeholder {
            color: #6b7280 !important;
        }

        body.dark-theme #salarySearch:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25) !important;
        }

        /* Summary Cards Dark Mode */
        body.dark-theme .card div[style*="color: #6b7280"] {
            color: #8b93a8 !important;
        }

        body.dark-theme .card div[style*="color: #1e293b"] {
            color: #e8eaf0 !important;
        }

        /* Table Dark Mode Fixes */
        body.dark-theme .salary-row td[style*="color: #374151"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme .salary-row div[style*="color: #6b7280"] {
            color: #8b93a8 !important;
        }

        body.dark-theme th {
            color: #8b93a8 !important;
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .table-container {
            border-color: #2e3349;
        }

        body.dark-theme .salary-row {
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .form-label[style*="color: #6b7280"] {
            color: #8b93a8 !important;
        }

        body.dark-theme .dropdown-menu span[style*="color: #4b5563"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme .card[style*="color: #6b7280"] {
            color: #8b93a8 !important;
        }
    </style>

    <div class="card">
        <div class="action-bar">
            {{-- Row 1: Filters & Search --}}
            <div class="filter-row">
                <form method="GET" action="{{ route('salary.index') }}" id="filterForm"
                    style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                    {{-- Tháng --}}
                    <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                        <label class="form-label"
                            style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tháng</label>
                        <div class="dropdown custom-filter-dropdown">
                            <input type="hidden" name="thang" id="inputMonth" value="{{ $thang }}">
                            <div class="form-control d-flex justify-content-between align-items-center"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="monthDropdownText" class="dropdown-text">Tháng {{ $thang }}</span>
                                <i class="bi bi-calendar3 ms-2" style="font-size: 14px;"></i>
                            </div>
                            <div class="dropdown-menu p-2 shadow"
                                style="min-width: 250px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                    <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN THÁNG</span>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;">
                                    @for($m = 1; $m <= 12; $m++)
                                        <button type="button"
                                            class="btn btn-sm {{ (int) $thang === $m ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                            onclick="selectFilter('inputMonth', '{{ $m }}')"
                                            style="padding: 6px 0; font-size: 13px; border-radius: 6px; border: none; @if((int) $thang === $m) background-color: #3b82f6; color: #fff; @endif transition: all 0.2s;">
                                            Tháng {{ $m }}
                                        </button>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Năm --}}
                    <div class="form-group" style="margin-bottom: 0; min-width: 120px;">
                        <label class="form-label"
                            style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Năm</label>
                        <div class="dropdown custom-filter-dropdown">
                            <input type="hidden" name="nam" id="inputYear" value="{{ $nam }}">
                            <div class="form-control d-flex justify-content-between align-items-center"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="yearDropdownText" class="dropdown-text">Năm {{ $nam }}</span>
                                <i class="bi bi-calendar3 ms-2" style="font-size: 14px;"></i>
                            </div>
                            <div class="dropdown-menu p-2 shadow"
                                style="min-width: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                    <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN NĂM</span>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px;">
                                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                        <button type="button"
                                            class="btn btn-sm {{ (int) $nam === $y ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                            onclick="selectFilter('inputYear', '{{ $y }}')"
                                            style="padding: 6px 0; font-size: 13px; border-radius: 6px; border: none; @if((int) $nam === $y) background-color: #3b82f6; color: #fff; @endif transition: all 0.2s;">
                                            {{ $y }}
                                        </button>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="search-bar">
                    <label class="form-label"
                        style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tìm
                        kiếm</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="width: 18px; height: 18px; position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="salarySearch" class="form-control"
                            placeholder="Tìm kiếm nhân viên, mã nhân viên..."
                            style="padding-left: 42px; height: 38px; border-radius: 8px; width: 100%;">
                    </div>
                </div>
            </div>

            {{-- Row 2: Actions --}}
            <div class="buttons-row">

                <button id="btnXuatBaoCao" class="btn btn-primary d-flex align-items-center gap-2"
                    style="background-color: #1D6F42; border-color: #1D6F42;">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                    <span>Xuất báo cáo</span>
                </button>
                <button id="btnTinhLuongHangLoat" class="btn btn-primary d-flex align-items-center gap-2"
                    style="background-color: #0BAA4B; border-color: #0BAA4B;">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Tính lương tự động</span>
                </button>
                <button id="btnGuiMailLuong" class="btn btn-info d-flex align-items-center gap-2"
                    style="background-color: #3b82f6; border-color: #3b82f6; color: white;">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Gửi Email phiếu lương</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Tổng quan kỳ lương --}}
    @php
        $tongThucNhan = $luongs->sum('Luong');
        $tongLuongCoBan = $luongs->sum('LuongCoBan');
        $soNhanVien = $luongs->count();
    @endphp
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
        <div class="card"
            style="padding: 16px 20px; margin-bottom: 0; background: linear-gradient(135deg, #0BAA4B, #088c3d); color: white;">
            <div style="font-size: 13px; opacity: 0.85;">Tổng thực nhận tháng {{ $thang }}/{{ $nam }}</div>
            <div style="font-size: 22px; font-weight: 700; margin-top: 4px;">
                {{ number_format($tongThucNhan, 0, ',', '.') }} đ
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 13px; color: #6b7280;">Tổng lương cơ bản</div>
            <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-top: 4px;">
                {{ number_format($tongLuongCoBan, 0, ',', '.') }} đ
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 13px; color: #6b7280;">Số nhân viên</div>
            <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ $soNhanVien }} người</div>
        </div>
    </div>

    @if($luongs->isEmpty())
        <div class="card" style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Chưa có dữ liệu lương tháng
                {{ $thang }}/{{ $nam }}
            </div>
            <div style="font-size: 14px;">Bấm <strong>"Tính lương tự động"</strong> để tính và lưu dữ liệu kỳ này.</div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="table salary-table" id="salaryTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 7%;">Kỳ lương</th>
                            <th style="width: 20%;">Nhân viên</th>
                            <th style="width: 13%;">Lương cơ bản</th>
                            <th class="col-phu-cap" style="width: 10%;">Phụ cấp</th>
                            <th class="col-khau-tru" style="width: 10%;">Khấu trừ</th>
                            {{-- Detailed Columns --}}
                            <th class="ins-detail" style="width: 9%;">BHXH</th>
                            <th class="ins-detail" style="width: 7%;">BHYT</th>
                            <th class="ins-detail" style="width: 7%;">BHTN</th>
                            <th class="ins-detail" style="width: 9%;">Thuế TNCN</th>
                            <th class="col-thuc-nhan" style="width: 13%;">Thực nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedLuongs = $luongs->sortBy(function ($l) {
                                return $l->nhanVien?->ttCongViec?->chucVu?->Ten ?? '—';
                            })->groupBy(function ($l) {
                                return $l->nhanVien?->ttCongViec?->chucVu?->Ten ?? '—';
                            });
                        @endphp

                        @foreach ($groupedLuongs as $chucVu => $groupLuongs)
                            <tr class="group-header-row">
                                <td colspan="10" class="pos-header"
                                    style="padding: 7px 16px; font-weight: 700; color: #1e293b; background: #f1f5f9; font-size: 12px;">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width: 4px; height: 14px; background: #0BAA4B; border-radius: 2px; margin-right: 8px;">
                                        </div>
                                        {{ strtoupper($chucVu) }}
                                    </div>
                                </td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                            </tr>

                            @foreach ($groupLuongs as $luong)
                                @php
                                    $nv = $luong->nhanVien;

                                    $insuranceDetail = $insuranceDetails[$nv?->id] ?? [];
                                    $bhxh = 0;
                                    $bhyt = 0;
                                    $bhtn = 0;
                                    foreach ($insuranceDetail as $detail) {
                                        $t = strtoupper($detail['ten'] ?? '');
                                        if (str_contains($t, 'BHXH'))
                                            $bhxh = $detail['so_tien'];
                                        if (str_contains($t, 'BHYT'))
                                            $bhyt = $detail['so_tien'];
                                        if (str_contains($t, 'BHTN'))
                                            $bhtn = $detail['so_tien'];
                                    }
                                @endphp
                                <tr class="salary-row">
                                    <td style="text-align: center;">
                                        <span class="ki-luong-text">{{ $thang }}/{{ $nam }}</span>
                                    </td>
                                    <td>
                                        <div
                                            style="font-weight: 600; color: #1e293b; font-size: 12px; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $nv?->Ten ?? '—' }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">{{ $nv?->Ma }}</div>
                                    </td>
                                    <td style="text-align: right; font-weight: 500;">
                                        {{ number_format($luong->LuongCoBan, 0, ',', '.') }} đ
                                    </td>
                                    <td class="col-phu-cap" style="text-align: right; font-weight: 500; color: #0369a1;">
                                        {{ number_format($luong->PhuCap, 0, ',', '.') }} đ
                                    </td>
                                    <td class="col-khau-tru" style="text-align: right; font-weight: 600; color: #be123c;">
                                        -{{ number_format(($luong->KhauTruBaoHiem ?? 0) + ($luong->ThueTNCN ?? 0), 0, ',', '.') }} đ
                                    </td>

                                    {{-- Detailed Insurance Cells --}}
                                    <td class="ins-detail" style="color: #9f1239;">{{ number_format($bhxh, 0, ',', '.') }} đ</td>
                                    <td class="ins-detail" style="color: #9f1239;">{{ number_format($bhyt, 0, ',', '.') }} đ</td>
                                    <td class="ins-detail" style="color: #9f1239;">{{ number_format($bhtn, 0, ',', '.') }} đ</td>
                                    <td class="ins-detail" style="color: #9f1239;">
                                        {{ number_format($luong->ThueTNCN ?? 0, 0, ',', '.') }} đ
                                    </td>

                                    <td class="col-thuc-nhan btn-show-slip" data-nv-id="{{ $nv?->id }}" data-thang="{{ $thang }}"
                                        data-nam="{{ $nam }}">
                                        <strong>{{ number_format($luong->Luong, 0, ',', '.') }} đ</strong>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ========== MODAL PHIẾU LƯƠNG ========== --}}
    <div id="slipModal"
        style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; overflow-y:auto; padding:24px 16px;">
        <div
            style="background:#fff; border-radius:12px; width:100%; max-width:860px; margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3); display:flex; flex-direction:column; max-height:90vh;">
            {{-- Modal Header --}}
            <div
                style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:linear-gradient(135deg,#0BAA4B,#088c3d); border-radius:12px 12px 0 0;">
                <div style="color:#fff; font-size:16px; font-weight:700;">
                    <i class="bi bi-file-earmark-text"></i>
                    &nbsp;Phiếu Lương
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button id="btnPrintSlip"
                        style="background:#fff; color:#0BAA4B; border:none; border-radius:6px; padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        In phiếu
                    </button>
                    <button onclick="closeSlipModal()"
                        style="background:rgba(255,255,255,0.2); border:none; border-radius:6px; color:#fff; font-size:20px; cursor:pointer; width:32px; height:32px; display:flex; align-items:center; justify-content:center; line-height:1;">✕</button>
                </div>
            </div>
            {{-- Modal Body --}}
            <div id="slipContent" style="padding:20px; overflow-y:auto; flex:1;">
                <div style="text-align:center; padding:40px; color:#6b7280;">
                    <div style="font-size:32px; margin-bottom:8px;">⏳</div>
                    <div>Đang tải phiếu lương...</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function selectFilter(inputId, val) {
                document.getElementById(inputId).value = val;
                document.getElementById('filterForm').submit();
            }
        </script>
        <script>
            $(document).ready(function () {
                // Initialize DataTable without automatic sorting to respect our PHP grouping
                const table = $('#salaryTable').DataTable({
                    language: {
                        "sProcessing": "Đang xử lý...",
                        "sLengthMenu": "Hiển thị _MENU_ mục",
                        "sZeroRecords": "Không tìm thấy dữ liệu",
                        "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                        "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                        "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                        "sSearch": "Tìm kiếm:",
                        "oPaginate": {
                            "sFirst": "Đầu", "sPrevious": "Trước", "sNext": "Tiếp", "sLast": "Cuối"
                        }
                    },
                    responsive: false,
                    autoWidth: false,
                    pageLength: 100,
                    dom: 'rtip',
                    ordering: false, // Disable ordering to keep PHP groups together
                    columnDefs: [
                        { searchable: false, targets: [0] }
                    ]
                });

                // Deduction columns are now always visible
                // Removed toggle logic per user request

                // Custom Search
                $('#salarySearch').on('keyup', function () {
                    table.search(this.value).draw();
                });

                // Nút tính lương hàng loạt
                document.getElementById('btnTinhLuongHangLoat').addEventListener('click', function () {
                    const thang = document.getElementById('inputMonth').value;
                    const nam = document.getElementById('inputYear').value;

                    Swal.fire({
                        title: `Tính lương tháng ${thang}/${nam}?`,
                        html: `Hệ thống sẽ tính lương tự động cho <strong>toàn bộ nhân viên có hợp đồng</strong> trong kỳ <strong>tháng ${thang}/${nam}</strong>.<br><br>Nếu đã tồn tại dữ liệu, sẽ <span style="color:#f97316;font-weight:600;">cập nhật lại</span>.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0BAA4B',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bi bi-lightning-charge-fill"></i> Xác nhận tính lương',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        Swal.fire({
                            title: 'Đang tính lương...',
                            html: `Đang xử lý kỳ lương tháng ${thang}/${nam}, vui lòng chờ.`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading(),
                        });

                        fetch('{{ route('salary.tinh-luong-hang-loat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ thang, nam }),
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success && data.thanh_cong > 0) {
                                    Swal.fire({
                                        title: 'Hoàn thành!',
                                        html: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0BAA4B',
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire('Lỗi', data.message ?? 'Có lỗi xảy ra.', 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                            });
                    });
                });

                // Gửi mail logic
                document.getElementById('btnGuiMailLuong').addEventListener('click', function () {
                    Swal.fire('Tính năng này đang được bảo trì');
                });

                // Modal Slice detail
                const slipModal = document.getElementById('slipModal');
                const slipContent = document.getElementById('slipContent');
                const btnPrint = document.getElementById('btnPrintSlip');

                function openSlipModal(nvId, thang, nam) {
                    slipContent.innerHTML = '<div style="text-align:center;padding:48px;color:#6b7280;"><div style="font-size:36px;margin-bottom:10px;">⏳</div><div style="font-size:14px;">Đang tải phiếu lương...</div></div>';
                    slipModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    fetch(`/salary/slip/${nvId}?thang=${thang}&nam=${nam}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(r => r.text())
                        .then(html => { slipContent.innerHTML = html; })
                        .catch(err => {
                            slipContent.innerHTML = '<div style="text-align:center;padding:48px;color:#dc2626;"><div style="font-size:32px;margin-bottom:8px;">⚠️</div><div>Không thể tải phiếu lương.</div></div>';
                        });
                }

                window.closeSlipModal = function () {
                    slipModal.style.display = 'none';
                    document.body.style.overflow = '';
                };

                $(document).on('click', '.btn-show-slip', function () {
                    const nvId = $(this).data('nvId');
                    const thang = $(this).data('thang');
                    const nam = $(this).data('nam');
                    openSlipModal(nvId, thang, nam);
                });

                btnPrint.addEventListener('click', function () {
                    const printWin = window.open('', '_blank', 'width=950,height=700');
                    var printStyle = '<style>body{font-family:Arial,sans-serif;font-size:13px;margin:20px;}<' + '/style>';
                    var printHtml = '<' + '!DOCTYPE html><html><head><meta charset="UTF-8"><title>Phieu Luong<' + '/title>' + printStyle + '<' + '/head><body>' + slipContent.innerHTML + '<' + '/body><' + '/html>';
                    printWin.document.write(printHtml);
                    printWin.document.close();
                    printWin.focus();
                    setTimeout(function () { printWin.print(); }, 500);
                });
            });
        </script>
    @endpush
@endsection