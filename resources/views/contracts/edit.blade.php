@extends('layouts.app')

@section('title', 'Chỉnh sửa hợp đồng - Vietnam Rubber Group')

@push('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 24px;
            margin-bottom: 24px;
            transition: background 0.3s, color 0.3s;
            border: 1px solid rgba(0,0,0,.05);
        }

        body.dark-theme .form-section {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        .form-section h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0BAA4B;
            display: flex;
            align-items: center;
        }

        .form-section h2 i {
            width: 32px;
            display: inline-flex;
            justify-content: flex-start;
            font-size: 22px;
            color: #0BAA4B;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        body.dark-theme .form-label {
            color: #c3c8da !important;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.375rem 0.75rem;
            width: 100%;
            transition: all 0.2s;
        }

        body.dark-theme .form-control {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        .form-control:focus {
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 45px !important;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        body.dark-theme .select2-container--default .select2-selection--single {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            color: #1f2937;
            padding-left: 0;
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

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .required-star {
            color: #dc2626;
            margin-left: 2px;
        }

        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        body.dark-theme .help-text {
            color: #8b93a8 !important;
        }

        .file-upload-area svg {
            width: 32px;
            height: 32px;
            margin-right: 12px;
            color: #0BAA4B;
        }

        .file-upload-compact-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
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
            border-color: #3d445e !important;
            background: rgba(255, 255, 255, 0.02) !important;
            color: #e8eaf0 !important;
        }

        .file-upload-area:hover {
            border-color: #0BAA4B;
            background: rgba(11, 170, 75, 0.05);
        }

        body.dark-theme .file-upload-area:hover {
            background-color: rgba(11, 170, 75, 0.08) !important;
            border-color: #0BAA4B !important;
        }

        .file-info-card-custom {
            display: none;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 8px;
            border: 1px solid #dcfce7;
            margin-top: 15px;
        }

        body.dark-theme .file-info-card-custom {
            background: rgba(11, 170, 75, 0.05) !important;
            border-color: rgba(11, 170, 75, 0.2) !important;
            color: #e8eaf0 !important;
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gray-800">Chỉnh sửa hợp đồng</h1>
            <p class="text-muted mb-0">Cập nhật thông tin hợp đồng cho nhân viên: <strong>{{ $hopDong->nhanVien->Ten }}</strong></p>
        </div>
        <a href="{{ route('hop-dong.info', $hopDong->id) }}" class="btn btn-secondary shadow-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại chi tiết
        </a>
    </div>

    <form action="{{ route('hop-dong.cap-nhat', $hopDong->id) }}" method="POST" enctype="multipart/form-data" id="contractForm">
        @csrf


        <!-- Thông tin chi tiết hợp đồng -->
        <div class="form-section shadow-sm">
            <h2>
                <i class="bi bi-person-badge"></i>
                Thông tin chi tiết hợp đồng
            </h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nhân viên <span class="required-star">*</span></label>
                            <input type="hidden" name="nhan_vien_id" value="{{ $hopDong->NhanVienId }}">
                            <select id="nhanVienSelect" class="form-control select2" disabled>
                                <option value="">-- Chọn nhân viên --</option>
                        @foreach($nhanvien as $nv)
                            <option value="{{ $nv->id }}" {{ $hopDong->NhanVienId == $nv->id ? 'selected' : '' }}
                                data-ma="{{ $nv->Ma }}"
                                data-ten="{{ $nv->Ten }}"
                                data-phongban="{{ $nv->phongBan->Ten ?? '' }}"
                                data-chucvu="{{ $nv->chucVu->Ten ?? '' }}"
                                data-phongban-id="{{ $nv->ttCongViec->PhongBanId ?? '' }}"
                                data-chucvu-id="{{ $nv->ttCongViec->ChucVuId ?? '' }}"
                                data-phuthuoc="{{ $nv->phu_thuoc_count }}">
                                {{ $nv->Ma }} - {{ $nv->Ten }}
                            </option>
                        @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Người ký hợp đồng <span class="required-star">*</span></label>
                            <input type="hidden" name="NguoiKyId" value="{{ $hopDong->NguoiKyId }}">
                            <select id="nguoiKySelect" class="form-control select2" disabled>
                                @foreach($nguoiKyList as $nv)
                                    <option value="{{ $nv->id }}" {{ $hopDong->NguoiKyId == $nv->id ? 'selected' : '' }}>
                                        {{ $nv->Ma }} - {{ $nv->Ten }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="NguoiKyId" value="{{ $hopDong->NguoiKyId }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số hợp đồng <span class="required-star">*</span></label>
                            <input type="text" name="so_hop_dong" class="form-control" value="{{ $hopDong->SoHopDong }}" required>
                        </div>
                    </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loại hợp đồng <span class="required-star">*</span></label>
                    <select name="loai_hop_dong_id" id="loaiHopDongSelect" class="form-control select2" required>
                        <option value="">-- Chọn loại hợp đồng --</option>
                        @foreach($loaiHopDongs as $loai)
                            <option value="{{ $loai->id }}" 
                                {{ $hopDong->loai_hop_dong_id == $loai->id ? 'selected' : '' }}
                                data-ma="{{ $loai->MaLoai }}" 
                                data-loai="{{ $loai->MaLoai }}">
                                {{ $loai->TenLoai }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="loai" id="loaiInput" value="{{ $hopDong->Loai }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phòng ban <span class="required-star">*</span></label>
                    <select name="phong_ban_id" id="phongBanSelect" class="form-control select2" required>
                        <option value="">-- Chọn phòng ban --</option>
                        @foreach($phongban as $pb)
                            <option value="{{ $pb->id }}" {{ $hopDong->PhongBanId == $pb->id ? 'selected' : '' }}>{{ $pb->Ten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Chức vụ <span class="required-star">*</span></label>
                    <select name="chuc_vu_id" id="chucVuSelect" class="form-control select2" required>
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach($chucvu as $cv)
                            <option value="{{ $cv->id }}" 
                                data-phucap="{{ $cv->HeSoPhuCap }}" 
                                data-loai="{{ $cv->Loai }}"
                                {{ $hopDong->ChucVuId == $cv->id ? 'selected' : '' }}>
                                {{ $cv->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Employee Info Card (Dưới hàng 2) -->
            <div class="employee-info-card {{ $hopDong->NhanVienId ? 'show' : '' }}" id="employeeInfoCard" style="display: {{ $hopDong->NhanVienId ? 'block' : 'none' }}; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-top: 12px; margin-bottom: 24px;">
                <div style="display: flex; gap: 16px; align-items: start;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 12px; color: #1f2937;">Thông tin nhân viên</div>
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 14px;">
                            <span style="color: #6b7280;">Mã NV:</span>
                            <span id="empMa" style="font-weight: 500; color: #1f2937;">{{ $hopDong->nhanVien->Ma ?? '-' }}</span>
                            <span style="color: #6b7280;">Họ tên:</span>
                            <span id="empTen" style="font-weight: 500; color: #1f2937;">{{ $hopDong->nhanVien->Ten ?? '-' }}</span>
                            <span style="color: #6b7280;">Phòng ban:</span>
                            <span id="empPhongBan" style="color: #1f2937;">{{ $hopDong->nhanVien->phongBan->Ten ?? '-' }}</span>
                            <span style="color: #6b7280;">Chức vụ:</span>
                            <span id="empChucVu" style="color: #1f2937;">{{ $hopDong->nhanVien->chucVu->Ten ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <h2>
                <i class="bi bi-clock-history"></i>
                Thời hạn & Trạng thái
            </h2>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ngày bắt đầu <span class="required-star">*</span></label>
                    <input type="text" name="NgayBatDau" id="ngayBatDau" class="form-control datepicker" 
                        value="{{ \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') }}" required>
                </div>
                <div class="form-group" id="ngayKetThucGroup">
                    <label class="form-label">Ngày kết thúc <span class="required-star" id="ngayKetThucRequired" style="display: {{ $hopDong->loaiHopDong->MaLoai === 'KXDH' ? 'none' : 'inline' }};">*</span></label>
                    <input type="text" name="NgayKetThuc" id="ngayKetThuc" class="form-control datepicker" 
                        value="{{ $hopDong->NgayKetThuc ? \Carbon\Carbon::parse($hopDong->NgayKetThuc)->format('d/m/Y') : '' }}"
                        {{ $hopDong->loaiHopDong->MaLoai === 'KXDH' ? '' : 'required' }}>
                    <div class="help-text">Để trống nếu là "Không xác định thời hạn"</div>
                </div>
                <input type="hidden" name="trang_thai" value="{{ $hopDong->TrangThai }}">
                <div class="form-group">
                    <label class="form-label">Số ngày phép/năm <span class="required-star">*</span></label>
                    <input type="number" name="ngay_phep_nam" id="ngayPhepNam" class="form-control" value="{{ $hopDong->NgayPhepNam ?? 12 }}" min="0" required>
                    <div class="help-text">Phép chuẩn theo năm</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Phép khả dụng năm nay</label>
                    <input type="number" name="ngay_phep_kha_dung" id="ngayPhepKhaDung" class="form-control" value="{{ $hopDong->NgayPhepKhaDung ?? 0 }}" step="0.1" readonly style="background-color: #f9fafb;">
                    <div class="help-text">Tính từ tháng bắt đầu đến hết năm</div>
                </div>
            </div>

            <h2>
                <i class="bi bi-cash-coin"></i>
                Cấu trúc lương cơ bản
            </h2>
                    
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">Lương cơ bản <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="luong_co_ban" id="luongCoBan" class="form-control formatted-number salary-input" 
                                        value="{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }}" required>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số người phụ thuộc</label>
                                <input type="number" id="soNguoiPhuThuoc" class="form-control salary-input" value="0" min="0">
                                <div class="help-text">Dùng để tính mức giảm trừ gia cảnh</div>
                            </div>
                        </div>

            <div style="margin-top: 24px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #1f2937; font-size: 15px;">💰 Các khoản phụ cấp</strong>
                    <div class="help-text">Nhập tên điều khoản và số tiền phụ cấp</div>
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-allowance-btn" style="display: flex; align-items: center; gap: 6px; padding: 6px 16px; background-color: #0BAA4B; border-color: #0BAA4B; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: 500;">
                    <i class="bi bi-plus-circle-fill"></i> Thêm hàng
                </button>
            </div>

            <div id="allowance-container">
                <!-- Pre-fill with dynamic allowances from PhuCap column -->
                @if(!empty($hopDong->PhuCap))
                    @foreach($hopDong->PhuCap as $index => $pc)
                        <div class="form-row allowance-row" style="margin-bottom: 12px;">
                            <div class="form-group" style="grid-column: span 2;">
                                <input type="text" name="phu_cap[{{ $index }}][name]" class="form-control" placeholder="Tên điều khoản" value="{{ $pc['name'] }}" required>
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
                <input type="hidden" name="tong_luong" id="tongLuongInput" value="{{ $hopDong->TongLuong }}">
            </div>
    </div>

    <!-- File đính kèm -->
    <div class="form-section shadow-sm">
            <h2>
                <i class="bi bi-paperclip"></i>
                Hợp đồng đính kèm
            </h2>
            
            @if($hopDong->File)
                <div style="padding: 12px; background: rgba(11, 170, 75, 0.1); border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; border: 1px solid rgba(11, 170, 75, 0.2);">
                    <i class="bi bi-file-earmark-check" style="font-size: 24px; color: #0BAA4B;"></i>
                    <div style="flex: 1;">
                        <div style="font-size: 12px; color: #6b7280;">File hiện tại:</div>
                        <a href="/{{ $hopDong->File }}" target="_blank" style="font-weight: 600; color: #0BAA4B; text-decoration: none;">Xem Hợp đồng</a>
                    </div>
                </div>
            @endif

            <div class="file-upload-area" onclick="document.getElementById('fileUpload').click()">
                <div class="file-upload-compact-container">
                    <i class="bi bi-cloud-upload" style="font-size: 32px; color: #0BAA4B;"></i>
                    <div style="text-align: left;">
                        <div style="font-weight: 600; font-size: 14px;" class="upload-text">Tải file lên (PDF, DOC, DOCX)</div>
                        <div style="font-size: 12px;" class="upload-hint">Dung lượng tối đa 10MB</div>
                    </div>
                </div>
                <input type="file" name="file" id="fileUpload" class="d-none" accept=".pdf,.doc,.docx" onchange="handleFileSelect(this)">
            </div>

            <div id="fileInfo" class="mt-3 file-info-card-custom">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-file-earmark-arrow-up" style="color: #0BAA4B;"></i>
                    <span id="fileName" style="font-size: 13px; font-weight: 500; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" class="upload-text"></span>
                    <button type="button" class="btn btn-sm p-0" onclick="removeFile()" style="color: #dc2626;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="form-section shadow-sm">
            <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('hop-dong.info', $hopDong->id) }}" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 on all select elements
            $('.select2').select2({
                width: '100%',
                placeholder: 'Chọn một mục',
                allowClear: true
            });

            // Tự động điền phòng ban/chức vụ & cập nhật thẻ thông tin khi chọn nhân viên
            $('#nhanVienSelect').on('change', function () {
                const option = $(this).find('option:selected');
                const phongBanId = option.data('phongban-id');
                const chucVuId = option.data('chucvu-id');
                
                if (phongBanId) {
                    $('#phongBanSelect').val(phongBanId).trigger('change');
                }
                if (chucVuId) {
                    $('#chucVuSelect').val(chucVuId).trigger('change');
                }

                if ($(this).val()) {
                    $('#empMa').text(option.data('ma') || '-');
                    $('#empTen').text(option.data('ten') || '-');
                    $('#empPhongBan').text(option.data('phongban') || '-');
                    $('#empChucVu').text(option.data('chucvu') || '-');
                    $('#employeeInfoCard').fadeIn().addClass('show');
                } else {
                    $('#employeeInfoCard').fadeOut().removeClass('show');
                }
            });

            // Handle file upload preview
            window.handleFileSelect = function (input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('fileInfo').style.display = 'block';
                }
            }

            window.removeFile = function () {
                document.getElementById('fileUpload').value = '';
                document.getElementById('fileInfo').style.display = 'none';
            }

            // Form validation
            document.getElementById('contractForm').addEventListener('submit', function (e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        // For select2, we need to find the container
                        if ($(field).hasClass('select2-hidden-accessible')) {
                            $(field).next('.select2-container').find('.select2-selection').css('border-color', '#dc2626');
                        } else {
                            field.style.borderColor = '#dc2626';
                        }
                    } else {
                        if ($(field).hasClass('select2-hidden-accessible')) {
                            $(field).next('.select2-container').find('.select2-selection').css('border-color', '#d1d5db');
                        } else {
                            field.style.borderColor = '#d1d5db';
                        }
                    }
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
                }
            });

            // Reset select2 border on change
            $('.select2').on('change', function() {
                $(this).next('.select2-container').find('.select2-selection').css('border-color', '#d1d5db');
                calculateAvailableLeave();
            });

            $('#loaiHopDongSelect').on('change', function() {
                const option = $(this).find('option:selected');
                const maLoai = option.data('ma') || '';
                
                const ngayKetThucInput = document.getElementById('ngayKetThuc');
                const ngayKetThucGroup = document.getElementById('ngayKetThucGroup');
                const ngayKetThucRequired = document.getElementById('ngayKetThucRequired');

                if (maLoai === 'KXDH') {
                    $(ngayKetThucGroup).hide();
                    if (ngayKetThucInput) {
                        ngayKetThucInput.value = '';
                        ngayKetThucInput.removeAttribute('required');
                    }
                    if (ngayKetThucRequired) ngayKetThucRequired.style.display = 'none';
                } else {
                    $(ngayKetThucGroup).show();
                    if (ngayKetThucInput) {
                        ngayKetThucInput.setAttribute('required', 'required');
                    }
                    if (ngayKetThucRequired) ngayKetThucRequired.style.display = 'inline';
                }
            });

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
            document.getElementById('ngayBatDau').addEventListener('change', calculateAvailableLeave);

            // Initial calculation
            setTimeout(calculateAvailableLeave, 500);

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

                // 3. Family Deductions (2026 Rules)
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

            // Trigger initial state
            if ($('#nhanVienSelect').val()) {
                const selectedOption = $('#nhanVienSelect').find('option:selected');
                const dependents = selectedOption.data('phuthuoc') || 0;
                document.getElementById('soNguoiPhuThuoc').value = dependents;
                // calculateSalary will be called by initial calculation in document.ready
            }

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatNumber(str) {
                return str.toString().replace(/\./g, '');
            }

            const mucLuongCoSo = {{ $mucLuongCoSo }};

            // Dynamic allowance row management
            let allowanceIndex = {{ !empty($hopDong->PhuCap) ? count($hopDong->PhuCap) : 0 }};
            
            document.getElementById('add-allowance-btn').addEventListener('click', function() {
                const container = document.getElementById('allowance-container');
                const row = document.createElement('div');
                row.className = 'form-row allowance-row';
                row.style.marginBottom = '12px';
                row.innerHTML = `
                    <div class="form-group" style="grid-column: span 2;">
                        <input type="text" name="phu_cap[${allowanceIndex}][name]" class="form-control" placeholder="Tên điều khoản" required>
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
                amountInput.addEventListener('input', function() {
                    formatInput(this);
                    calculateSalary();
                });
                
                row.querySelector('.btn-remove-allowance').addEventListener('click', function() {
                    row.remove();
                    calculateSalary();
                });
                
                allowanceIndex++;
            });

            // Handle removal of initial/existing rows
            $(document).on('click', '.btn-remove-allowance', function() {
                $(this).closest('.allowance-row').remove();
                calculateSalary();
            });

            function formatInput(input) {
                let value = input.value.replace(/\D/g, '');
                if (value) {
                    input.value = formatNumber(value);
                }
            }

            $('#chucVuSelect').on('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const heSoPhuCap = parseFloat(selectedOption.getAttribute('data-phucap')) || 0;
                const phuCapAmount = heSoPhuCap * mucLuongCoSo;
                
                if (phuCapAmount > 0) {
                    let exists = false;
                    document.querySelectorAll('#allowance-container input[name$="[name]"]').forEach(input => {
                        if (input.value === 'Phụ cấp chức vụ') {
                            const amountInput = input.closest('.allowance-row').querySelector('.allowance-amount');
                            amountInput.value = formatNumber(Math.round(phuCapAmount));
                            exists = true;
                        }
                    });

                    if (!exists) {
                        document.getElementById('add-allowance-btn').click();
                        const lastRow = document.querySelector('#allowance-container .allowance-row:last-child');
                        lastRow.querySelector('input[name$="[name]"]').value = 'Phụ cấp chức vụ';
                        lastRow.querySelector('.allowance-amount').value = formatNumber(Math.round(phuCapAmount));
                    }
                }
                
                calculateSalary();
                checkPositionAvailability();
            });

            document.querySelectorAll('.formatted-number').forEach(input => {
                input.addEventListener('input', function (e) {
                    let cursorPosition = this.selectionStart;
                    let oldLength = this.value.length;
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        let formatted = formatNumber(value);
                        this.value = formatted;
                        let newLength = formatted.length;
                        let diff = newLength - oldLength;
                        this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                    }
                    calculateSalary();
                });
            });

            function checkPositionAvailability() {
                const phongBanId = document.getElementById('phongBanSelect').value;
                const chucVuId = document.getElementById('chucVuSelect').value;
                const nhanVienId = document.getElementById('nhanVienSelect').value;

                if (!phongBanId || !chucVuId) return;

                const loai = $('#chucVuSelect option:selected').data('loai');
                if (loai != '1') {
                    hideChucVuError();
                    return;
                }

                fetch('/api/check-chuc-vu-ton-tai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phong_ban_id: phongBanId,
                        chuc_vu_id: chucVuId,
                        nhan_vien_id: nhanVienId
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
                    .catch(error => console.error('Error:', error));
            }

            function showChucVuError(message) {
                const errorDiv = document.getElementById('chuc-vu-error');
                const errorMessage = document.getElementById('chuc-vu-error-message');
                if (errorDiv && errorMessage) {
                    errorMessage.textContent = message;
                    errorDiv.style.display = 'flex';
                    document.querySelector('button[type="submit"]').disabled = true;
                }
            }

            function hideChucVuError() {
                const errorDiv = document.getElementById('chuc-vu-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    document.querySelector('button[type="submit"]').disabled = false;
                }
            }

            // AJAX Form Submission
            $('#contractForm').on('submit', function (e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...');

                // Unformat numbers
                $('.formatted-number').each(function() {
                    $(this).val(unformatNumber($(this).val()));
                });

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => {
                        if (!response.ok) return response.json().then(data => { throw data; });
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message,
                            confirmButtonColor: '#0BAA4B'
                        }).then(() => {
                            window.location.href = data.redirect_url || "{{ route('hop-dong.info', $hopDong->id) }}";
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = error.message || 'Có lỗi xảy ra.';
                        if (error.errors) errorMessage = Object.values(error.errors).flat().join('<br>');
                        
                        Swal.fire({ icon: 'error', title: 'Lỗi!', html: errorMessage, confirmButtonColor: '#dc2626' });
                        submitBtn.prop('disabled', false).html('<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Lưu thay đổi');
                    });
            });

            // Initial calculation
            calculateSalary();
            // Initial state check for KXDH
            if ($('#loaiHopDongSelect').find('option:selected').data('ma') === 'KXDH') {
                $('#ngayKetThucGroup').hide();
                document.getElementById('ngayKetThuc').removeAttribute('required');
            }
        });
    </script>
@endpush
