@extends('layouts.app')

@section('title', (isset($isRenew) ? 'Tái ký hợp đồng' : 'Tạo hợp đồng mới') . ' - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
            transition: background 0.3s, color 0.3s;
        }

        body.dark-theme .form-section {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-section h2 {
            color: #e8eaf0 !important;
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .form-group label {
            color: #c3c8da !important;
        }

        body.dark-theme .employee-info-card {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        /* Final Dark Mode Polish for inline styles */
        body.dark-theme strong[style*="color: #1f2937"],
        body.dark-theme span[style*="color: #1f2937"],
        body.dark-theme div[style*="color: #1f2937"],
        body.dark-theme strong[style*="color: #374151"],
        body.dark-theme span[style*="color: #374151"],
        body.dark-theme div[style*="color: #374151"],
        body.dark-theme div[style*="color: #1f2937"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme .upload-text {
            color: #e8eaf0 !important;
        }

        body.dark-theme .upload-hint,
        body.dark-theme .help-text {
            color: #8b93a8 !important;
        }

        body.dark-theme input[readonly] {
            background-color: #21263a !important;
            color: #8b93a8 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme #salaryTable span[style*="color: #1f2937"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme #salaryTable span[style*="color: #6b7280"] {
            color: #a0aec0 !important;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 14px;
        }

        .salary-table th {
            text-align: left;
            padding: 12px;
            background: rgba(11, 170, 75, 0.1);
            color: #065f46;
            font-weight: 600;
            border-bottom: 2px solid #0BAA4B;
        }

        .salary-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        body.dark-theme .salary-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .salary-row-label {
            color: #4b5563;
            font-weight: 500;
        }

        body.dark-theme .salary-row-label {
            color: #c3c8da;
        }

        .salary-row-value {
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }

        body.dark-theme .salary-row-value {
            color: #e8eaf0;
        }

        .salary-row-deduction {
            color: #dc2626;
        }

        .salary-row-total {
            background: rgba(11, 170, 75, 0.05);
            font-size: 16px;
        }

        .salary-row-total .salary-row-label {
            color: #065f46;
            font-weight: 700;
        }

        .salary-row-total .salary-row-value {
            color: #0BAA4B;
            font-weight: 800;
            font-size: 18px;
        }

        .form-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0BAA4B;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: #dc2626;
            margin-left: 4px;
        }

        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
            color: #374151; /* Default light mode color */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80px;
        }

        body.dark-theme .file-upload-area {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: #3d445e !important;
            color: #e8eaf0 !important;
        }

        .file-upload-area:hover {
            border-color: #0BAA4B;
            background-color: #f0fdf4;
        }

        body.dark-theme .file-upload-area:hover {
            background-color: rgba(11, 170, 75, 0.08) !important;
            border-color: #0BAA4B !important;
        }

        .file-upload-area svg {
            width: 32px;
            height: 32px;
            margin-right: 12px;
            color: #6b7280;
        }

        .file-upload-compact-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .file-info {
            display: none;
            margin-top: 12px;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 6px;
            text-align: left;
            border: 1px solid #dcfce7;
        }

        body.dark-theme .file-info {
            background: rgba(11, 170, 75, 0.05) !important;
            border-color: rgba(11, 170, 75, 0.2) !important;
            color: #e8eaf0 !important;
        }

        .file-info.show {
            display: block;
        }

        .contract-number-preview {
            background: #f0fdf4;
            border: 1px solid #0BAA4B;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
        }

        .contract-number-preview strong {
            color: #0BAA4B;
            font-size: 18px;
        }

        .employee-info-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
            display: none;
        }

        .employee-info-card.show {
            display: block;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn {
                width: 100%;
            }
        }

        .validation-error {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            padding: 10px 12px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            color: #dc2626;
            font-size: 13px;
        }

        .validation-error i {
            font-size: 16px;
            flex-shrink: 0;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: #1f2937;
        }

        body.dark-theme .select2-container--default .select2-selection--single {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e8eaf0 !important;
        }

        /* Select2 Dropdown Dark Mode */
        body.dark-theme .select2-dropdown {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5) !important;
        }

        body.dark-theme .select2-results__option {
            color: #c3c8da !important;
            padding: 8px 12px !important;
        }

        body.dark-theme .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0BAA4B !important;
            color: #ffffff !important;
        }

        body.dark-theme .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #2e3349 !important;
            color: #ffffff !important;
        }

        body.dark-theme .select2-search--dropdown {
            background-color: #1a1d27 !important;
            padding: 8px !important;
        }

        body.dark-theme .select2-search--dropdown .select2-search__field {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
            border-radius: 6px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 30px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">
                {{ isset($isRenew) ? 'Tái ký hợp đồng' : 'Tạo hợp đồng mới' }}
            </h1>
            <p style="color: #6b7280;">
                {{ isset($isRenew) ? 'Gia hạn hợp đồng mới cho nhân viên dựa trên thông tin cũ' : 'Nhập thông tin để tạo hợp đồng lao động cho nhân viên' }}
            </p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại
        </a>
    </div>

    <form id="contractForm" action="{{ route('hop-dong.tao') }}" method="POST" enctype="multipart/form-data">
        @csrf


        <!-- Thông tin cơ bản & Vị trí & Loại HĐ -->
        <div class="form-section">
            <h2>
                <i class="bi bi-file-earmark-text" style="font-size: 24px;"></i>
                Thông tin chi tiết hợp đồng
            </h2>

            <!-- Hàng 1 -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nhân viên <span class="required">*</span></label>
                    <select name="nhan_vien_id" id="nhanVienSelect" class="form-control select2" required @if(isset($isRenew)) disabled @endif>
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach ($nhanvien as $nv)
                            <option value="{{ $nv->id }}" 
                                @if(isset($isRenew) && $oldContract->NhanVienId == $nv->id) selected @endif
                                data-ma="{{ $nv->Ma }}" data-ten="{{ $nv->Ten }}"
                                data-phongban="{{ $nv->phongBan?->Ten }}" data-chucvu="{{ $nv->chucVu?->Ten }}"
                                data-phongban-id="{{ $nv->ttCongViec?->PhongBanId }}"
                                data-chucvu-id="{{ $nv->ttCongViec?->ChucVuId }}"
                                data-phuthuoc="{{ $nv->phu_thuoc_count }}">
                                {{ $nv->Ma }} - {{ $nv->Ten }} - {{ $nv->phongBan?->Ten }}
                            </option>
                        @endforeach
                    </select>
                    @if(isset($isRenew))
                        <input type="hidden" name="nhan_vien_id" value="{{ $oldContract->NhanVienId }}">
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Người ký hợp đồng <span class="required">*</span></label>
                    <select id="nguoiKySelect" name="NguoiKyId" class="form-control select2" required>
                        <option value="">-- Chọn người ký --</option>
                        @foreach($nguoiKyList as $nv)
                            <option value="{{ $nv->id }}" @if($nv->id == $defaultNguoiKyId) selected @endif>
                                {{ $nv->Ma }} - {{ $nv->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Số hợp đồng <span class="required">*</span></label>
                    <input type="text" name="so_hop_dong" id="soHopDong" class="form-control" placeholder="Tự động tạo"
                        readonly style="background: #f9fafb;">
                </div>
            </div>

            <!-- Hàng 2 -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loại hợp đồng <span class="required">*</span></label>
                    <select name="loai_hop_dong_id" id="loaiHopDongSelect" class="form-control select2" required>
                        <option value="">-- Chọn loại hợp đồng --</option>
                        @foreach($loaiHopDongs as $loai)
                            <option value="{{ $loai->id }}" 
                                @if(isset($isRenew) && $oldContract->loai_hop_dong_id == $loai->id) selected @endif
                                data-ma="{{ $loai->MaLoai }}" 
                                data-loai="{{ $loai->MaLoai }}">
                                {{ $loai->TenLoai }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="loai" id="loaiInput" value="{{ isset($isRenew) ? ($oldContract->Loai ?? '') : '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Phòng ban <span class="required">*</span></label>
                    <select name="phong_ban_id" id="phongBanSelect" class="form-control select2" required @if(isset($isRenew)) disabled @endif>
                        <option value="">-- Chọn phòng ban --</option>
                        @foreach ($phongban as $pb)
                            <option value="{{ $pb->id }}" @if(isset($isRenew) && $oldContract->PhongBanId == $pb->id) selected @endif>{{ $pb->Ten }}</option>
                        @endforeach
                    </select>
                    @if(isset($isRenew))
                        <input type="hidden" name="phong_ban_id" value="{{ $oldContract->PhongBanId }}">
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Chức vụ <span class="required">*</span></label>
                    <select name="chuc_vu_id" id="chucVuSelect" class="form-control select2" required @if(isset($isRenew)) disabled @endif>
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach ($chucvu as $cv)
                            <option value="{{ $cv->id }}" @if(isset($isRenew) && $oldContract->ChucVuId == $cv->id) selected @endif data-phucap="{{ $cv->PhuCapChucVu }}" data-loai="{{ $cv->Loai }}">
                                {{ $cv->Ten }}
                            </option>
                        @endforeach
                    </select>
                    @if(isset($isRenew))
                        <input type="hidden" name="chuc_vu_id" value="{{ $oldContract->ChucVuId }}">
                    @endif
                </div>
            </div>

            <!-- Employee Info Card (Dưới hàng 2) -->
            <div class="employee-info-card" id="employeeInfoCard">
                <div style="display: flex; gap: 16px; align-items: start;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 12px;">Thông tin nhân viên</div>
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 14px;">
                            <span style="color: #6b7280;">Mã NV:</span>
                            <span id="empMa" style="font-weight: 500;">-</span>
                            <span style="color: #6b7280;">Họ tên:</span>
                            <span id="empTen" style="font-weight: 500;">-</span>
                            <span style="color: #6b7280;">Phòng ban:</span>
                            <span id="empPhongBan">-</span>
                            <span style="color: #6b7280;">Chức vụ:</span>
                            <span id="empChucVu">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <h2>
                <i class="bi bi-clock-history" style="font-size: 24px;"></i>
                Thời hạn & Trạng thái
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ngày bắt đầu <span class="required">*</span></label>
                    <input type="text" name="NgayBatDau" id="ngayBatDau" class="form-control datepicker"
                        placeholder="dd/mm/yyyy" required readonly>
                </div>

                <div class="form-group" id="ngayKetThucGroup">
                    <label class="form-label">Ngày kết thúc <span class="required-star" id="ngayKetThucRequired" style="display: none;">*</span></label>
                    <input type="text" name="NgayKetThuc" id="ngayKetThuc" class="form-control datepicker"
                        placeholder="dd/mm/yyyy" readonly>
                    <div class="help-text">Bắt đầu nhập cho các loại HĐ có thời hạn</div>
                </div>

                <input type="hidden" name="trang_thai" value="1">
                <div class="form-group">
                    <label class="form-label">Số ngày phép/năm <span class="required-star">*</span></label>
                    <input type="number" name="ngay_phep_nam" id="ngayPhepNam" class="form-control" value="12" min="0" required>
                    <div class="help-text">Phép chuẩn theo năm</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Phép khả dụng năm nay</label>
                    <input type="number" name="ngay_phep_kha_dung" id="ngayPhepKhaDung" class="form-control" value="9" step="0.1" readonly style="background-color: #f9fafb;">
                    <div class="help-text">Tính từ tháng bắt đầu đến hết năm</div>
                </div>
            </div>

            <div id="durationInfo" class="help-text" style="padding: 12px; background: rgba(11, 170, 75, 0.1); border-radius: 6px; display: none; margin-top: 15px; margin-bottom: 24px; color: #0BAA4B;">
                <strong>Thời hạn hợp đồng:</strong> <span id="durationText">-</span>
            </div>

            <h2>
                <i class="bi bi-cash-coin" style="font-size: 24px;"></i>
                Cấu trúc lương cơ bản
            </h2>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Lương cơ bản (VNĐ) <span class="required">*</span></label>
                    <input type="text" name="luong_co_ban" id="luongCoBan" class="form-control salary-input formatted-number"
                        placeholder="15.000.000" required value="{{ isset($isRenew) ? number_format($oldContract->LuongCoBan, 0, ',', '.') : '' }}" @if(isset($isRenew)) readonly @endif>
                </div>
                <div class="form-group">
                    <label class="form-label">Số người phụ thuộc</label>
                    <input type="number" id="soNguoiPhuThuoc" class="form-control salary-input" value="0" min="0">
                    <div class="help-text">Dùng để tính mức giảm trừ gia cảnh</div>
                </div>
            </div>


            <!-- Các khoản phụ cấp -->
            <div style="margin-top: 24px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #1f2937; font-size: 15px;">Các khoản phụ cấp</strong>
                    <div class="help-text">Nhập tên điều khoản và số tiền phụ cấp</div>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-allowance-btn" style="display: flex; align-items: center; gap: 6px; padding: 6px 16px; background-color: #0BAA4B; border-color: #0BAA4B; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: 500;">
                    <i class="bi bi-plus-circle-fill"></i> Thêm hàng
                </button>
            </div>

            <div id="allowance-container">
                <!-- Initial row if renewing from old dynamic data -->
                @if(isset($isRenew) && !empty($oldContract->PhuCap))
                    @foreach($oldContract->PhuCap as $index => $pc)
                        <div class="form-row allowance-row" style="margin-bottom: 12px;">
                            <div class="form-group" style="grid-column: span 2;">
                                <input type="text" name="phu_cap[{{ $index }}][name]" class="form-control" placeholder="Tên điều khoản (VD: Phụ cấp đi lại)" value="{{ $pc['name'] }}" required>
                            </div>
                            <div class="form-group">
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="phu_cap[{{ $index }}][amount]" class="form-control salary-input formatted-number allowance-amount" placeholder="Số tiền" value="{{ number_format($pc['amount'], 0, ',', '.') }}" required>
                                    <button type="button" class="btn btn-outline-danger btn-remove-allowance">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Hidden input for Total Income (saved to TongLuong) -->
            <input type="hidden" name="tong_luong" id="tongLuongInput" value="0">

            <!-- Diễn giải chi tiết lương (Simulation) -->
            <div id="salaryCalculationArea" style="margin-top: 24px; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px; display: none;">
                <div style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-calculator-fill" style="font-size: 20px; color: #0BAA4B;"></i>
                    Diễn giải chi tiết (VND)
                </div>
                <div class="help-text" style="margin-bottom: 16px;">Mô phỏng thu nhập dựa trên luật hiện hành (2026)</div>

                <table class="salary-table" id="salaryTable">
                    <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th style="text-align: right;">Công thức / Định mức</th>
                            <th style="text-align: right;">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="salary-row-label">Lương Gross</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;">Lương CB + Phụ cấp</td>
                            <td class="salary-row-value" id="resGross">0 ₫</td>
                        </tr>
                        <tr>
                            <td class="salary-row-label">Bảo hiểm (10.5%)</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;">BHXH, BHYT, BHTN</td>
                            <td class="salary-row-value salary-row-deduction" id="resInsurance">-0 ₫</td>
                        </tr>
                        <tr>
                            <td class="salary-row-label">Giảm trừ gia cảnh</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;" id="resDeductionNote">Bản thân + 0 NPT</td>
                            <td class="salary-row-value salary-row-deduction" id="resDeduction">-0 ₫</td>
                        </tr>
                        <tr>
                            <td class="salary-row-label">Thu nhập tính thuế</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;">Gross - BH - Giảm trừ</td>
                            <td class="salary-row-value" id="resTaxable">0 ₫</td>
                        </tr>
                        <tr>
                            <td class="salary-row-label">Thuế TNCN</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;">Biểu thuế lũy tiến</td>
                            <td class="salary-row-value salary-row-deduction" id="resPIT">-0 ₫</td>
                        </tr>
                        <tr>
                            <td class="salary-row-label">Đoàn phí công đoàn</td>
                            <td style="text-align: right; color: #6b7280; font-size: 13px;">1% Lương Gross</td>
                            <td class="salary-row-value salary-row-deduction" id="resUnion">-0 ₫</td>
                        </tr>
                        <tr class="salary-row-total">
                            <td class="salary-row-label">💰 LƯƠNG NET</td>
                            <td style="text-align: right; color: #065f46; font-weight: 500; font-size: 13px;">Thực nhận hàng tháng</td>
                            <td class="salary-row-value" id="resNet">0 ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- File đính kèm -->
        <div class="form-section">
            <h2>
                <i class="bi bi-paperclip" style="font-size: 24px;"></i>
                File hợp đồng
            </h2>

            <div class="form-group">
                <label>Tải lên file hợp đồng (PDF)</label>
                <div class="file-upload-area" onclick="document.getElementById('fileUpload').click()">
                    <div class="file-upload-compact-container">
                        <i class="bi bi-cloud-upload" style="font-size: 32px; color: #0BAA4B;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; font-size: 14px;" class="upload-text">Tải file lên (PDF, DOC, DOCX)</div>
                            <div style="font-size: 12px;" class="upload-hint">Dung lượng tối đa 10MB</div>
                        </div>
                    </div>
                    <input type="file" id="fileUpload" name="file" accept=".pdf,.doc,.docx" style="display: none;"
                        onchange="handleFileSelect(event)">
                </div>

                <div class="file-info" id="fileInfo">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 24px; color: #0BAA4B;"></i>
                        <div style="flex: 1;">
                            <div id="fileName" style="font-weight: 500;" class="upload-text">-</div>
                            <div id="fileSize" style="font-size: 13px;" class="upload-hint">-</div>
                        </div>
                        <button type="button" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;"
                            onclick="removeFile()">
                            <i class="bi bi-x" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-section">
            <div class="form-actions">
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isset($isRenew) ? 'Ký hợp đồng mới' : 'Tạo hợp đồng' }}
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            // Global variables
            let contractCounter = 1;
            const allEmployees = @json($nhanvien);
            const defaultNguoiKyId = {{ $defaultNguoiKyId ?? 'null' }};

            // Initialize flatpickr for date inputs
            const startDatePicker = flatpickr("#ngayBatDau", {
                dateFormat: "d/m/Y",
                altInput: false,
                onChange: function (selectedDates, dateStr, instance) {
                    // Set min date for end date
                    if (selectedDates[0]) {
                        endDatePicker.set('minDate', selectedDates[0]);
                    }
                    calculateDuration();
                    calculateAvailableLeave();
                }
            });

            const endDatePicker = flatpickr("#ngayKetThuc", {
                dateFormat: "d/m/Y",
                altInput: false,
                onChange: function (selectedDates, dateStr, instance) {
                    calculateDuration();
                }
            });

            // Set default date to today (after endDatePicker is initialized)
            startDatePicker.setDate(new Date(), true);

            // Show employee info when selected
            // document.getElementById('nhanVienSelect').addEventListener('change', function () { // Original
            $('#nhanVienSelect').on('change', function () { // Select2 change event
                const option = $(this).find('option:selected'); // Get selected option with jQuery
                const card = document.getElementById('employeeInfoCard');
                const nguoiKySelect = $('#nguoiKySelect'); // Use jQuery for Select2
                const selectedEmployeeId = $(this).val(); // Get value with jQuery

                if (selectedEmployeeId) {
                    // Show employee info
                    document.getElementById('empMa').textContent = option.data('ma') || '-';
                    document.getElementById('empTen').textContent = option.data('ten') || '-';
                    document.getElementById('empPhongBan').textContent = option.data('phongban') || '-';
                    document.getElementById('empChucVu').textContent = option.data('chucvu') || '-';
                    card.classList.add('show');

                    // Auto-fill position info
                    $('#phongBanSelect').val(option.data('phongban-id')).trigger('change');
                    $('#chucVuSelect').val(option.data('chucvu-id')).trigger('change');

                    // Generate contract number
                    generateContractNumber();
                } else {
                    card.classList.remove('show');
                }
            });

            // Generate contract number
            function generateContractNumber() {
                const loaiSelect = $('#loaiHopDongSelect'); // Use jQuery
                const currentYear = new Date().getFullYear();

                if (loaiSelect.val()) { // Use .val() for jQuery
                    const loaiOption = loaiSelect.find('option:selected'); // Get selected option

                    const maLoai = loaiOption.data('ma') || 'HD';

                    // Format: [STT]/[Năm]/[Mã Loại]
                    const soHopDong = `${String(contractCounter).padStart(3, '0')}/${currentYear}/${maLoai}`;

                    document.getElementById('soHopDong').value = soHopDong;
                } else {
                    document.getElementById('soHopDong').value = 'Tự động tạo';
                }
            }

            // Update contract type
            $('#loaiHopDongSelect').on('change', function () {
                const option = $(this).find('option:selected'); // Get selected option
                const loai = option.data('loai') || '';
                const maLoai = option.data('ma') || '';
                
                document.getElementById('loaiInput').value = loai;

                // Handle KXDH vs Others visibility & validation
                const ngayKetThucInput = document.getElementById('ngayKetThuc');
                const ngayKetThucGroup = document.getElementById('ngayKetThucGroup');
                const ngayKetThucRequired = document.getElementById('ngayKetThucRequired');

                if (maLoai === 'KXDH') {
                    $(ngayKetThucGroup).hide();
                    ngayKetThucInput.value = '';
                    ngayKetThucInput.removeAttribute('required');
                    ngayKetThucRequired.style.display = 'none';
                } else {
                    $(ngayKetThucGroup).show();
                    ngayKetThucInput.setAttribute('required', 'required');
                    ngayKetThucRequired.style.display = 'inline';
                }

                // Auto-set dates for TV (Probation)
                if (maLoai === 'TV') {
                    const today = new Date();
                    const nextTwoMonths = new Date();
                    nextTwoMonths.setMonth(today.getMonth() + 2);

                    const formatDate = (date) => {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}/${month}/${year}`;
                    };

                    $('#ngayBatDau').val(formatDate(today)).trigger('change');
                    $('#ngayKetThuc').val(formatDate(nextTwoMonths)).trigger('change');
                }

                generateContractNumber();
                calculateDuration();
            });



            // Calculate contract duration
            function calculateDuration() {
                const startDateStr = document.getElementById('ngayBatDau').value;
                const endDateStr = document.getElementById('ngayKetThuc').value;
                const durationInfo = document.getElementById('durationInfo');
                const durationText = document.getElementById('durationText');

                if (startDateStr && endDateStr) {
                    // Parse dd/mm/yyyy format
                    const startParts = startDateStr.split('/');
                    const endParts = endDateStr.split('/');
                    const start = new Date(startParts[2], startParts[1] - 1, startParts[0]);
                    const end = new Date(endParts[2], endParts[1] - 1, endParts[0]);

                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const months = Math.floor(diffDays / 30);
                    const days = diffDays % 30;

                    let text = '';
                    if (months > 0) {
                        text += `${months} tháng`;
                    }
                    if (days > 0) {
                        if (text) text += ' ';
                        text += `${days} ngày`;
                    }

                    durationText.textContent = text || '0 ngày';
                    durationInfo.style.display = 'block';
                } else if (startDateStr && !endDateStr) {
                    durationText.textContent = 'Không xác định thời hạn';
                    durationInfo.style.display = 'block';
                } else {
                    durationInfo.style.display = 'none';
                }
            }

            // Calculate available leave for the first year
            function calculateAvailableLeave() {
                const startDateStr = document.getElementById('ngayBatDau').value;
                const annualLeave = parseFloat(document.getElementById('ngayPhepNam').value) || 0;
                const khaDungInput = document.getElementById('ngayPhepKhaDung');

                if (startDateStr) {
                    const parts = startDateStr.split('/');
                    if (parts.length === 3) {
                        const startMonth = parseInt(parts[1]);
                        const monthsRemaining = 12 - startMonth + 1;
                        const available = (annualLeave / 12) * monthsRemaining;
                        khaDungInput.value = Math.round(available * 10) / 10;
                    }
                }
            }

            // Listen for changes in annual leave
            document.getElementById('ngayPhepNam').addEventListener('input', calculateAvailableLeave);
            
            // Initial calculation
            setTimeout(calculateAvailableLeave, 500);


            // Handle file upload
            function handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    const fileInfo = document.getElementById('fileInfo');
                    const fileName = document.getElementById('fileName');
                    const fileSize = document.getElementById('fileSize');

                    fileName.textContent = file.name;
                    fileSize.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                    fileInfo.classList.add('show');
                }
            }

            function removeFile() {
                document.getElementById('fileUpload').value = '';
                document.getElementById('fileInfo').classList.remove('show');
            }

            // Form validation
            $('#contractForm').on('submit', function (e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        if ($(field).hasClass('select2')) { // Check if it's a Select2 dropdown
                            $(field).next('.select2-container').find('.select2-selection--single').css('border-color', '#dc2626');
                        } else {
                            field.style.borderColor = '#dc2626';
                        }
                    } else {
                        if ($(field).hasClass('select2')) {
                            $(field).next('.select2-container').find('.select2-selection--single').css('border-color', '#d1d5db');
                        } else {
                            field.style.borderColor = '#d1d5db';
                        }
                    }
                });

                // Reset select2 border on change
                $('.select2').on('change', function () {
                    $(this).next('.select2-container').find('.select2-selection--single').css('border-color', '#d1d5db');
                });

                // Validate date range
                const startDateStr = document.getElementById('ngayBatDau').value;
                const endDateStr = document.getElementById('ngayKetThuc').value;

                if (startDateStr && endDateStr) {
                    const startParts = startDateStr.split('/');
                    const endParts = endDateStr.split('/');
                    const startDate = new Date(startParts[2], startParts[1] - 1, startParts[0]);
                    const endDate = new Date(endParts[2], endParts[1] - 1, endParts[0]);

                    if (startDate >= endDate) {
                        isValid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Ngày kết thúc phải sau ngày bắt đầu!',
                            confirmButtonColor: '#0BAA4B'
                        });
                        e.preventDefault();
                        return;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thông tin chưa đầy đủ',
                        text: 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)',
                        confirmButtonColor: '#0BAA4B'
                    });

                    const firstInvalid = document.querySelector('[required]:invalid, [required][value=""]');
                    if (firstInvalid) {
                        // If it's a Select2, scroll to its container
                        if ($(firstInvalid).hasClass('select2')) {
                            $(firstInvalid).next('.select2-container').get(0).scrollIntoView({ behavior: 'smooth', block: 'center' });
                            $(firstInvalid).next('.select2-container').find('.select2-selection--single').focus();
                        } else {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalid.focus();
                        }
                    }
                }
            });

            // Remove error border on input
            document.querySelectorAll('input:not(.select2), textarea').forEach(field => { // Exclude Select2
                field.addEventListener('input', function () {
                    this.style.borderColor = '#d1d5db';
                });
            });

            // Calculate and display salary breakdown
            function calculateSalary() {
                // Get base salary (unformat first to handle dots)
                const luongCoBan = parseFloat(unformatNumber(document.getElementById('luongCoBan').value)) || 0;
                const soNguoiPhuThuoc = parseInt(document.getElementById('soNguoiPhuThuoc').value) || 0;
                
                // Sum dynamic allowances
                let tongPhuCap = 0;
                document.querySelectorAll('.allowance-amount').forEach(input => {
                    tongPhuCap += parseFloat(unformatNumber(input.value)) || 0;
                });
                
                const grossSalary = luongCoBan + tongPhuCap;

                // 1. Insurance (Basic rule: 10.5% of Gross, capped at 20 * MucLuongCoSo)
                const mucLuongCoBanCap = mucLuongCoSo * 20;
                const salaryForInsurance = Math.min(grossSalary, mucLuongCoBanCap);
                const insurance = Math.round(salaryForInsurance * 0.105);

                // 2. Union Fee (1% of Gross)
                const unionFee = Math.round(grossSalary * 0.01);

                // 3. Family Deductions (2026 Rules from user mockup)
                // Personal: 15,500,000
                // Dependent: 4,400,000 (Assuming same increment ratio or standard)
                const personalDeduction = 15500000;
                const dependentDeduction = 4400000;
                const totalDeduction = personalDeduction + (soNguoiPhuThuoc * dependentDeduction);

                // 4. Taxable Income
                const taxableIncome = Math.max(0, grossSalary - insurance - totalDeduction);

                // 5. Personal Income Tax (PIT) - Progressive Brackets
                const calculatePIT = (taxable) => {
                    if (taxable <= 0) return 0;
                    if (taxable <= 5000000) return taxable * 0.05;
                    if (taxable <= 10000000) return taxable * 0.1 - 250000;
                    if (taxable <= 18000000) return taxable * 0.15 - 750000;
                    if (taxable <= 32000000) return taxable * 0.2 - 1650000;
                    if (taxable <= 52000000) return taxable * 0.25 - 3250000;
                    if (taxable <= 80000000) return taxable * 0.3 - 5850000;
                    return taxable * 0.35 - 9850000;
                };
                const pit = Math.round(calculatePIT(taxableIncome));

                // 6. Net Salary
                const netSalary = grossSalary - insurance - pit - unionFee;

                // Format and display
                const formatVND = (amount) => amount.toLocaleString('vi-VN') + ' ₫';
                const formatVNDDeduction = (amount) => '-' + amount.toLocaleString('vi-VN') + ' ₫';

                document.getElementById('resGross').textContent = formatVND(grossSalary);
                document.getElementById('resInsurance').textContent = formatVNDDeduction(insurance);
                document.getElementById('resDeduction').textContent = formatVNDDeduction(totalDeduction);
                document.getElementById('resDeductionNote').textContent = `Bản thân + ${soNguoiPhuThuoc} NPT`;
                document.getElementById('resTaxable').textContent = formatVND(taxableIncome);
                document.getElementById('resPIT').textContent = formatVNDDeduction(pit);
                document.getElementById('resUnion').textContent = formatVNDDeduction(unionFee);
                document.getElementById('resNet').textContent = formatVND(netSalary);

                // Sync to hidden input for database saving
                document.getElementById('tongLuongInput').value = Math.round(grossSalary);

                // Show calculation area if base salary is entered
                const calculationArea = document.getElementById('salaryCalculationArea');
                if (grossSalary > 0) {
                    calculationArea.style.display = 'block';
                } else {
                    calculationArea.style.display = 'none';
                }
            }

            // Add event listeners to all salary inputs
            document.querySelectorAll('.salary-input').forEach(input => {
                input.addEventListener('input', calculateSalary);
            });
            
            // Auto-fill dependents when employee is selected
            $('#nhanVienSelect').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    const dependents = selectedOption.data('phuthuoc') || 0;
                    document.getElementById('soNguoiPhuThuoc').value = dependents;
                    calculateSalary();
                }
            });


            // Number formatting helper
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatNumber(str) {
                return str.toString().replace(/\./g, '');
            }

            // Base salary from server
            const mucLuongCoSo = {{ $mucLuongCoSo }};

            // Dynamic allowance row management
            let allowanceIndex = {{ isset($isRenew) && !empty($oldContract->PhuCap) ? count($oldContract->PhuCap) : 0 }};
            
            document.getElementById('add-allowance-btn').addEventListener('click', function() {
                const container = document.getElementById('allowance-container');
                const row = document.createElement('div');
                row.className = 'form-row allowance-row';
                row.style.marginBottom = '12px';
                row.innerHTML = `
                    <div class="form-group" style="grid-column: span 2;">
                        <input type="text" name="phu_cap[${allowanceIndex}][name]" class="form-control" placeholder="Tên điều khoản (VD: Phụ cấp đi lại)" required>
                    </div>
                    <div class="form-group">
                        <div style="display: flex; gap: 8px;">
                            <input type="text" name="phu_cap[${allowanceIndex}][amount]" class="form-control salary-input formatted-number allowance-amount" placeholder="Số tiền" required>
                            <button type="button" class="btn btn-outline-danger btn-remove-allowance">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(row);
                
                // Initialize events for new row
                const amountInput = row.querySelector('.allowance-amount');
                amountInput.addEventListener('input', function(e) {
                    formatInput(this);
                    calculateSalary();
                });
                
                row.querySelector('.btn-remove-allowance').addEventListener('click', function() {
                    row.remove();
                    calculateSalary();
                });
                
                allowanceIndex++;
            });

            // Handle removal of existing rows (for Renew)
            document.querySelectorAll('.btn-remove-allowance').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.allowance-row').remove();
                    calculateSalary();
                });
            });

            function formatInput(input) {
                let value = input.value.replace(/\D/g, '');
                if (value) {
                    input.value = formatNumber(value);
                }
            }

            // Auto-fill position allowance when position is selected
            const chucVuSelect = $('#chucVuSelect');
            if (chucVuSelect) {
                chucVuSelect.on('change', function () {
                    const selectedOption = $(this).find('option:selected');
                    const phuCapAmount = parseFloat(selectedOption.data('phucap')) || 0;

                    if (phuCapAmount > 0) {
                        // Check if "Phụ cấp chức vụ" row already exists
                        let exists = false;
                        document.querySelectorAll('#allowance-container input[name$="[name]"]').forEach(input => {
                            if (input.value === 'Phụ cấp chức vụ') {
                                const amountInput = input.closest('.allowance-row').querySelector('.allowance-amount');
                                amountInput.value = formatNumber(Math.round(phuCapAmount));
                                exists = true;
                            }
                        });

                        if (!exists) {
                            // Add new row for position allowance
                            document.getElementById('add-allowance-btn').click();
                            const lastRow = document.querySelector('#allowance-container .allowance-row:last-child');
                            lastRow.querySelector('input[name$="[name]"]').value = 'Phụ cấp chức vụ';
                            lastRow.querySelector('.allowance-amount').value = formatNumber(Math.round(phuCapAmount));
                        }
                    }
                    
                    calculateSalary();
                    checkPositionAvailability();
                });
            }

            // Apply real-time formatting to all formatted-number inputs
            document.querySelectorAll('.formatted-number').forEach(input => {
                // Format while typing
                input.addEventListener('input', function (e) {
                    let cursorPosition = this.selectionStart;
                    let oldLength = this.value.length;

                    // Remove all non-digits
                    let value = this.value.replace(/\D/g, '');

                    // Format with thousand separators
                    if (value) {
                        let formatted = formatNumber(value);
                        this.value = formatted;

                        // Adjust cursor position after formatting
                        let newLength = formatted.length;
                        let diff = newLength - oldLength;
                        this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                    }

                    // Trigger salary calculation
                    calculateSalary();
                });

                // Remove leading zeros on blur
                input.addEventListener('blur', function () {
                    if (this.value) {
                        let num = unformatNumber(this.value);
                        if (num && num !== '0') {
                            this.value = formatNumber(num);
                        } else {
                            this.value = '0';
                        }
                    }
                });
            });

            // Check if manager position is already filled in department
            function checkPositionAvailability() {
                const phongBanSelect = $('#phongBanSelect'); // Use jQuery
                const chucVuSelect = $('#chucVuSelect'); // Use jQuery

                if (!phongBanSelect.val() || !chucVuSelect.val()) { // Use .val()
                    hideChucVuError();
                    return;
                }

                // Get selected position type (Loai)
                const selectedOption = chucVuSelect.find('option:selected'); // Get selected option
                const loai = selectedOption.data('loai');

                // Only check for Loai 1 (manager positions)
                if (loai != '1') {
                    hideChucVuError();
                    return;
                }

                // Call API to check
                fetch('/api/check-chuc-vu-ton-tai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phong_ban_id: phongBanSelect.val(), // Use .val()
                        chuc_vu_id: chucVuSelect.val(), // Use .val()
                        nhan_vien_id: null // Always null for contract creation
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            showChucVuError(data.message);
                        } else {
                            hideChucVuError();
                        }
                    })
                    .catch(error => {
                        console.error('Error checking position availability:', error);
                    });
            }

            function showChucVuError(message) {
                const errorDiv = document.getElementById('chuc-vu-error');
                const errorMessage = document.getElementById('chuc-vu-error-message');

                if (errorDiv && errorMessage) {
                    errorMessage.textContent = message;
                    errorDiv.style.display = 'flex';

                    // Disable submit button
                    const submitBtn = document.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                }
            }

            function hideChucVuError() {
                const errorDiv = document.getElementById('chuc-vu-error');

                if (errorDiv) {
                    errorDiv.style.display = 'none';

                    // Enable submit button
                    const submitBtn = document.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = false;
                }
            }

            // Handle form submission with AJAX and SweetAlert2
            const contractForm = document.getElementById('contractForm');
            if (contractForm) {
                // contractForm.addEventListener('submit', function (e) { // Original
                $('#contractForm').on('submit', function (e) { // jQuery for form submission
                    e.preventDefault();

                    // Check for validation errors
                    const chucVuError = document.getElementById('chuc-vu-error');
                    if (chucVuError && chucVuError.style.display !== 'none') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Vui lòng sửa các lỗi trước khi tiếp tục',
                            confirmButtonColor: '#0BAA4B'
                        });
                        return;
                    }

                    // Disable submit button
                    const submitBtn = $(this).find('button[type="submit"]'); // Use jQuery
                    if (submitBtn) {
                        submitBtn.prop('disabled', true); // Use prop for jQuery
                        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...'); // Use html for jQuery
                    }

                    // Unformat all formatted-number inputs before submission
                    const formattedInputs = this.querySelectorAll('.formatted-number');
                    formattedInputs.forEach(input => {
                        if (input.value) {
                            input.value = unformatNumber(input.value);
                        }
                    });

                    // Create FormData
                    const formData = new FormData(this);

                    // Debug: Log form data
                    console.log('Form Data:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ': ' + value);
                    }

                    // Send AJAX request
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw data;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message || 'Hợp đồng đã được tạo thành công và thông tin nhân viên đã được cập nhật!',
                                confirmButtonColor: '#0BAA4B',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                // Redirect to contracts.index
                                window.location.href = "{{ route('contracts.index') }}";
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            let errorMessage = 'Có lỗi xảy ra khi tạo hợp đồng. Vui lòng thử lại.';

                            // Handle validation errors
                            if (error.errors) {
                                const errorList = Object.values(error.errors).flat();
                                errorMessage = errorList.join('<br>');
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                html: errorMessage,
                                confirmButtonColor: '#dc2626',
                                confirmButtonText: 'Đóng'
                            });

                            // Re-enable submit button
                            if (submitBtn) {
                                submitBtn.prop('disabled', false); // Use prop for jQuery
                                submitBtn.html('<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Tạo hợp đồng'); // Use html for jQuery
                            }
                        });
                });
            }

            // Auto-select employee if nhan_vien_id is in URL (for normal creation from profile)
            const urlParams = new URLSearchParams(window.location.search);
            const nhanVienId = urlParams.get('nhan_vien_id');

            if (nhanVienId) {
                $(document).ready(function() {
                    const nhanVienSelect = $('#nhanVienSelect');
                    if (nhanVienSelect.length) {
                        nhanVienSelect.val(nhanVienId).trigger('change');
                        nhanVienSelect.prop('disabled', true).addClass('locked-field');
                        nhanVienSelect.next('.select2-container').find('.select2-selection--single').css({
                            'background-color': '#f3f4f6',
                            'pointer-events': 'none'
                        });

                    }
                });
            }

            // Renewal initialization
            @if(isset($isRenew))
                $(document).ready(function() {
                    $('#nhanVienSelect').trigger('change');
                    
                    // Set next start date
                    @if($oldContract->NgayKetThuc)
                        const oldEndDate = "{{ $oldContract->NgayKetThuc }}";
                        const nextDate = new Date(oldEndDate);
                        nextDate.setDate(nextDate.getDate() + 1);
                        startDatePicker.setDate(nextDate, true);
                    @endif

                    // Trigger salary calculation
                    calculateSalary();

                    // Apply locked styles to Select2
                    const lockedFields = ['#nhanVienSelect', '#phongBanSelect', '#chucVuSelect'];
                    lockedFields.forEach(selector => {
                        $(selector).next('.select2-container').find('.select2-selection--single').css({
                            'background-color': '#f3f4f6',
                            'pointer-events': 'none'
                        });
                    });

                    // Add notice
                    const positionSection = document.querySelector('.form-section:nth-of-type(2)');
                    if (positionSection) {
                        const notice = document.createElement('div');
                        notice.style.fontSize = '12px';
                        notice.style.color = '#0BAA4B';
                        notice.style.marginTop = '8px';
                        notice.style.fontStyle = 'italic';
                        notice.innerHTML = '<i class="bi bi-info-circle"></i> Thông tin nhân viên và vị trí được cố định từ hợp đồng cũ.';
                        positionSection.appendChild(notice);
                    }
                });
            @endif
        </script>
    @endpush
@endsection
