@extends('layouts.app')

@section('title', (isset($isRenew) ? 'Tái ký hợp đồng' : 'Tạo hợp đồng mới') . ' - Vietnam Rubber Group')

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
        body.dark-theme div[style*="color: #1f2937"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme input[readonly] {
            background-color: #21263a !important;
            color: #8b93a8 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme #salaryCard {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%) !important;
            border-color: #0BAA4B !important;
        }

        body.dark-theme #salaryCard span[style*="color: #1f2937"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme #salaryCard span[style*="color: #6b7280"] {
            color: #a0aec0 !important;
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
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .file-upload-area:hover {
            border-color: #0BAA4B;
            background-color: #f0fdf4;
        }

        .file-upload-area svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            color: #6b7280;
        }

        .file-info {
            display: none;
            margin-top: 12px;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 6px;
            text-align: left;
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
        <input type="hidden" name="phieu_dieu_chuyen_id" id="phieuDieuChuyenId">

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
                                data-chucvu-id="{{ $nv->ttCongViec?->ChucVuId }}">
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
                    <select id="nguoiKySelect" class="form-control select2" required disabled>
                        @foreach($nguoiKyList as $nv)
                            <option value="{{ $nv->id }}" @if($nv->id == $defaultNguoiKyId) selected @endif>
                                {{ $nv->Ma }} - {{ $nv->Ten }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="NguoiKyId" value="{{ $defaultNguoiKyId }}">
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
                        <option value="1" @if(isset($isRenew) && $oldContract->Loai == 'thu_viec') selected @endif data-ma="HDTV" data-loai="thu_viec">Hợp đồng thử việc</option>
                        <option value="2" @if(isset($isRenew) && $oldContract->Loai == 'chinh_thuc_xac_dinh_thoi_han') selected @endif data-ma="HDLD" data-loai="chinh_thuc_xac_dinh_thoi_han">Hợp đồng lao động xác định thời hạn</option>
                        <option value="3" @if(isset($isRenew) && $oldContract->Loai == 'chinh_thuc_khong_xac_dinh_thoi_han') selected @endif data-ma="HDLD" data-loai="chinh_thuc_khong_xac_dinh_thoi_han">Hợp đồng lao động không xác định thời hạn</option>
                        <option value="4" @if(isset($isRenew) && $oldContract->Loai == 'khoan_viec') selected @endif data-ma="HDKV" data-loai="khoan_viec">Hợp đồng khoán việc</option>
                        <option value="5" @if(isset($isRenew) && $oldContract->Loai == 'thoi_vu') selected @endif data-ma="HDTV" data-loai="thoi_vu">Hợp đồng thời vụ</option>
                        <option value="7" @if(isset($isRenew) && str_starts_with($oldContract->Loai ?? '', 'nda')) selected @endif data-ma="NDA" data-loai="nda">Thỏa thuận bảo mật (NDA)</option>
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

                <div class="form-group">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="text" name="NgayKetThuc" id="ngayKetThuc" class="form-control datepicker"
                        placeholder="dd/mm/yyyy" readonly>
                    <div class="help-text">Để trống nếu là "Không xác định thời hạn"</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái <span class="required">*</span></label>
                    <select name="trang_thai" class="form-control select2" required>
                        <option value="1" selected>Còn hiệu lực</option>
                        <option value="0">Hết hạn</option>
                        <option value="2">Bị hủy/Thanh lý</option>
                    </select>
                </div>
            </div>

            <div id="durationInfo" class="help-text" style="padding: 12px; background: rgba(11, 170, 75, 0.1); border-radius: 6px; display: none; margin-top: 15px; color: #0BAA4B;">
                <strong>Thời hạn hợp đồng:</strong> <span id="durationText">-</span>
            </div>

            <h2>
                <i class="bi bi-cash-coin" style="font-size: 24px;"></i>
                Cấu trúc lương cơ bản
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ngachLuongSelect">Ngạch lương <span style="font-size: 12px; color: #6b7280;">(tuỳ chọn)</span></label>
                    <select name="ngach_luong_id" id="ngachLuongSelect" class="form-control select2">
                        <option value="">-- Chọn ngạch lương --</option>
                        @foreach($ngachLuongs as $nl)
                            <option value="{{ $nl->id }}" @if(isset($isRenew) && $oldDienBien && $oldDienBien->NgachLuongId == $nl->id) selected @endif data-ma="{{ $nl->Ma }}" data-ten="{{ $nl->Ten }}" data-nhom="{{ $nl->Nhom }}">
                                {{ $nl->Ma }} – {{ $nl->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bacLuongSelect">Bậc lương <span style="font-size: 12px; color: #6b7280;">(chọn ngạch trước)</span></label>
                    <select name="bac_luong_id" id="bacLuongSelect" class="form-control select2" disabled>
                        <option value="">-- Chọn bậc lương --</option>
                    </select>
                    
                    <div id="heSoBadge" style="display:none; margin-top: 8px; padding: 6px 12px; background: rgba(11, 170, 75, 0.1); border: 1px solid #0BAA4B; border-radius: 6px; font-size: 13px; color: #0BAA4B;">
                        <strong id="heSoValue">–</strong>
                        <span style="color: #6b7280;"> × {{ number_format($mucLuongCoSo, 0, ',', '.') }} đ = </span>
                        <strong id="luongTinhTu">–</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Lương cơ bản (VNĐ) <span class="required">*</span></label>
                    <input type="text" name="luong_co_ban" id="luongCoBan" class="form-control salary-input formatted-number"
                        placeholder="15.000.000" required value="{{ isset($isRenew) ? number_format($oldContract->LuongCoBan, 0, ',', '.') : '' }}" @if(isset($isRenew)) readonly @endif>
                    <div class="help-text">Tối thiểu 5.310.000 VNĐ</div>
                </div>
            </div>

            @php
                $ngachBacJson = [];
                foreach ($ngachLuongs as $nl) {
                    $bacs = [];
                    foreach ($nl->bacLuongs->sortBy('Bac') as $b) {
                        $bacs[] = [
                            'id' => $b->id,
                            'bac' => $b->Bac,
                            'heso' => (float) $b->HeSo,
                        ];
                    }
                    $ngachBacJson[] = [
                        'id' => $nl->id,
                        'ma' => $nl->Ma,
                        'ten' => $nl->Ten,
                        'nhom' => $nl->Nhom,
                        'bacs' => $bacs,
                    ];
                }
            @endphp
            <script id="ngachBacData" type="application/json">
                {!! json_encode($ngachBacJson) !!}
            </script>

            <!-- Phụ cấp tính BHXH -->
            <div style="margin-top: 24px; margin-bottom: 12px;">
                <strong style="color: #1f2937; font-size: 15px;">📋 Phụ cấp tính BHXH</strong>
                <div class="help-text">Các khoản phụ cấp được tính vào tiền lương đóng bảo hiểm xã hội</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phụ cấp chức vụ (VNĐ)</label>
                    <input type="text" name="phu_cap_chuc_vu" id="phuCapChucVu"
                        class="form-control salary-input formatted-number" placeholder="0" min="0" value="0" readonly>
                    <div class="help-text">Tự động lấy từ chức vụ đã chọn</div>
                </div>

                @foreach($dmAllowances->where('is_bhxh', 1) as $dm)
                    <div class="form-group">
                        <label>{{ $dm->noi_dung }} (VNĐ)</label>
                        <input type="text" name="allowances[{{ $dm->id }}]" 
                            class="form-control salary-input formatted-number allowance-bhxh" 
                            placeholder="0" min="0" 
                            value="{{ isset($isRenew) && $oldContract->phuCaps->contains($dm->id) ? number_format($oldContract->phuCaps->find($dm->id)->pivot->so_tien, 0, ',', '.') : '0' }}"
                            @if(isset($isRenew)) readonly @endif>
                    </div>
                @endforeach
            </div>

            <!-- Phụ cấp KHÔNG tính BHXH -->
            <div style="margin-top: 24px; margin-bottom: 12px;">
                <strong style="color: #1f2937; font-size: 15px;">💼 Phụ cấp KHÔNG tính BHXH</strong>
                <div class="help-text">Các khoản hỗ trợ đời sống, không tính vào tiền lương đóng bảo hiểm</div>
            </div>

            <div class="form-row">
                @foreach($dmAllowances->where('is_bhxh', 0) as $dm)
                    <div class="form-group">
                        <label>{{ $dm->noi_dung }} (VNĐ)</label>
                        <input type="text" name="allowances[{{ $dm->id }}]" 
                            class="form-control salary-input formatted-number allowance-ngoai-bhxh" 
                            placeholder="0" min="0" 
                            value="{{ isset($isRenew) && $oldContract->phuCaps->contains($dm->id) ? number_format($oldContract->phuCaps->find($dm->id)->pivot->so_tien, 0, ',', '.') : '0' }}"
                            @if(isset($isRenew)) readonly @endif>
                    </div>
                @endforeach
            </div>

            <!-- Hidden input for Total Income (saved to TongLuong) -->
            <input type="hidden" name="tong_luong" id="tongLuongInput" value="0">

            <!-- Tổng hợp lương -->
            <div id="salaryCard"
                style="margin-top: 24px; padding: 20px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #0BAA4B; border-radius: 12px; display: none;">
                <div
                    style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-bar-chart-fill" style="font-size: 20px; color: #0BAA4B;"></i>
                    TỔNG HỢP LƯƠNG
                </div>

                <div style="display: grid; gap: 10px; font-size: 14px;">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: #6b7280;">Lương cơ bản:</span>
                        <span id="displayLuongCoBan" style="font-weight: 600; color: #1f2937;">0 ₫</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: #6b7280;">Tổng phụ cấp tính BHXH:</span>
                        <span id="displayPhuCapBHXH" style="font-weight: 600; color: #1f2937;">0 ₫</span>
                    </div>

                    <div style="border-top: 1px dashed #0BAA4B; margin: 4px 0;"></div>

                    <div
                        style="display: flex; justify-content: space-between; padding: 8px 0; background: rgba(15, 81, 50, 0.1); margin: 0 -12px; padding-left: 12px; padding-right: 12px; border-radius: 6px;">
                        <span style="color: #0BAA4B; font-weight: 600;">Tiền lương đóng BHXH:</span>
                        <span id="displayLuongBHXH" style="font-weight: 700; color: #0BAA4B;">0 ₫</span>
                    </div>

                    <div style="border-top: 1px solid #d1d5db; margin: 8px 0;"></div>

                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span style="color: #6b7280;">Tổng phụ cấp không BHXH:</span>
                        <span id="displayPhuCapKhongBHXH" style="font-weight: 600; color: #1f2937;">0 ₫</span>
                    </div>

                    <div style="border-top: 2px solid #0BAA4B; margin: 8px 0;"></div>

                    <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                        <span style="font-size: 16px; font-weight: 700; color: #1f2937;">💰 TỔNG THU NHẬP:</span>
                        <span id="displayTongThuNhap" style="font-size: 18px; font-weight: 700; color: #0BAA4B;">0 ₫</span>
                    </div>
                </div>
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
                    <i class="bi bi-cloud-upload" style="font-size: 48px; color: #0BAA4B;"></i>
                    <div style="font-weight: 500; color: #374151; margin-bottom: 4px;">
                        Click để tải file lên
                    </div>
                    <div style="font-size: 13px; color: #6b7280;">
                        PDF, DOC, DOCX (MAX. 10MB)
                    </div>
                    <input type="file" id="fileUpload" name="file" accept=".pdf,.doc,.docx" style="display: none;"
                        onchange="handleFileSelect(event)">
                </div>

                <div class="file-info" id="fileInfo">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 24px; color: #0BAA4B;"></i>
                        <div style="flex: 1;">
                            <div id="fileName" style="font-weight: 500; color: #1f2937;">-</div>
                            <div id="fileSize" style="font-size: 13px; color: #6b7280;">-</div>
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
            // document.getElementById('loaiHopDongSelect').addEventListener('change', function () { // Original
            $('#loaiHopDongSelect').on('change', function () { // Select2 change event
                const option = $(this).find('option:selected'); // Get selected option
                const loai = option.data('loai') || '';
                document.getElementById('loaiInput').value = loai;

                generateContractNumber();
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
                // Get all salary values (unformat first to handle dots)
                const luongCoBan = parseFloat(unformatNumber(document.getElementById('luongCoBan').value)) || 0;
                const phuCapChucVu = parseFloat(unformatNumber(document.getElementById('phuCapChucVu').value)) || 0;
                
                // Sum dynamic allowances (BHXH)
                let tongPhuCapBHXH = phuCapChucVu;
                document.querySelectorAll('.allowance-bhxh').forEach(input => {
                    tongPhuCapBHXH += parseFloat(unformatNumber(input.value)) || 0;
                });

                // Sum dynamic allowances (Non-BHXH)
                let tongPhuCapKhongBHXH = 0;
                document.querySelectorAll('.allowance-ngoai-bhxh').forEach(input => {
                    tongPhuCapKhongBHXH += parseFloat(unformatNumber(input.value)) || 0;
                });

                const luongBHXH = luongCoBan + tongPhuCapBHXH;
                const tongThuNhap = luongBHXH + tongPhuCapKhongBHXH;

                // Format and display
                const formatVND = (amount) => amount.toLocaleString('vi-VN') + ' ₫';

                document.getElementById('displayLuongCoBan').textContent = formatVND(luongCoBan);
                document.getElementById('displayPhuCapBHXH').textContent = formatVND(tongPhuCapBHXH);
                document.getElementById('displayLuongBHXH').textContent = formatVND(luongBHXH);
                document.getElementById('displayPhuCapKhongBHXH').textContent = formatVND(tongPhuCapKhongBHXH);
                document.getElementById('displayTongThuNhap').textContent = formatVND(tongThuNhap);

                // Sync to hidden input for database saving
                document.getElementById('tongLuongInput').value = Math.round(tongThuNhap);

                // Show salary card if base salary is entered
                const salaryCard = document.getElementById('salaryCard');
                if (luongCoBan > 0) {
                    salaryCard.style.display = 'block';
                } else {
                    salaryCard.style.display = 'none';
                }

                // Validate minimum wage
                const minWage = 5310000; // Lương tối thiểu vùng I năm 2026
                const luongCoBanInput = document.getElementById('luongCoBan');
                if (luongCoBan > 0 && luongCoBan < minWage) {
                    luongCoBanInput.style.borderColor = '#dc2626';
                    luongCoBanInput.setCustomValidity('Lương cơ bản phải >= 5.310.000 VNĐ (Lương tối thiểu vùng I)');
                } else {
                    luongCoBanInput.style.borderColor = '#d1d5db';
                    luongCoBanInput.setCustomValidity('');
                }
            }

            // Add event listeners to all salary inputs
            document.querySelectorAll('.salary-input').forEach(input => {
                input.addEventListener('input', calculateSalary);
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

            // Auto-fill position allowance when position is selected
            const chucVuSelect = $('#chucVuSelect'); // Use jQuery
            if (chucVuSelect) {
                chucVuSelect.on('change', function () { // Select2 change event
                    const selectedOption = $(this).find('option:selected'); // Get selected option
                    const heSoPhuCap = parseFloat(selectedOption.data('phucap')) || 0;

                    // Calculate allowance amount = coefficient × base salary
                    const phuCapAmount = heSoPhuCap * mucLuongCoSo;

                    document.getElementById('phuCapChucVu').value = formatNumber(Math.round(phuCapAmount));
                    calculateSalary(); // Recalculate salary totals

                    // Check position availability for Loai 1 (manager positions)
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

            // Auto-select employee if nhan_vien_id is in URL (for normal creation from profile/transfer)
            const urlParams = new URLSearchParams(window.location.search);
            const nhanVienId = urlParams.get('nhan_vien_id');
            const phieuId = urlParams.get('phieu_dieu_chuyen_id');

            if (phieuId) {
                const phieuInput = document.getElementById('phieuDieuChuyenId');
                if (phieuInput) phieuInput.value = phieuId;
            }

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

                        // Lock position fields if coming from transfer
                        if (phieuId) {
                            const posFields = ['phongBanSelect', 'chucVuSelect'];
                            posFields.forEach(id => {
                                const el = $('#' + id);
                                if (el.length) {
                                    el.prop('disabled', true).addClass('locked-field');
                                    el.next('.select2-container').find('.select2-selection--single').css({
                                        'background-color': '#f3f4f6',
                                        'pointer-events': 'none'
                                    });
                                }
                            });
                        }
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
            // =============================================
            // Ngạch lương & Bậc lương – cascade logic
            // =============================================
            $(document).ready(function () {
                // Initialize Select2
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Chọn một mục',
                    allowClear: true
                });

                const ngachData = JSON.parse(document.getElementById('ngachBacData').textContent);
                const ngachSelect = $('#ngachLuongSelect'); // Use jQuery
                const bacSelect = $('#bacLuongSelect'); // Use jQuery
                const heSoBadge = $('#heSoBadge'); // Use jQuery
                const heSoValue = $('#heSoValue'); // Use jQuery
                const luongTinhTu = $('#luongTinhTu'); // Use jQuery
                const luongCoBanInput = $('#luongCoBan'); // Use jQuery

                // Khi chọn Ngạch lương → load danh sách Bậc lương
                ngachSelect.on('change', function () { // Select2 change event
                    const ngachId = parseInt($(this).val()); // Use .val()
                    bacSelect.html('<option value="">-- Chọn bậc lương --</option>'); // Use html()
                    heSoBadge.hide(); // Use hide()

                    if (!ngachId) {
                        bacSelect.prop('disabled', true).trigger('change'); // Use prop and trigger
                        return;
                    }

                    const ngach = ngachData.find(n => n.id === ngachId);
                    if (!ngach || !ngach.bacs.length) {
                        bacSelect.prop('disabled', true).trigger('change');
                        return;
                    }

                    ngach.bacs.forEach(b => {
                        const opt = `<option value="${b.id}" data-heso="${b.heso}">Bậc ${b.bac}  –  Hệ số ${Number(b.heso).toFixed(2)}</option>`;
                        bacSelect.append(opt); // Use append
                    });
                    bacSelect.prop('disabled', false).trigger('change'); // Enable and refresh Select2
                });

                // Khi chọn Bậc lương → tính và điền Lương cơ bản
                bacSelect.on('change', function () { // Select2 change event
                    const opt = $(this).find('option:selected'); // Get selected option
                    const heso = parseFloat(opt.data('heso'));

                    if (!heso) {
                        heSoBadge.hide();
                        return;
                    }

                    const luong = Math.round(heso * mucLuongCoSo);
                    const formatted = formatNumber(luong);

                    // Điền vào ô lương cơ bản
                    luongCoBanInput.val(formatted); // Use val()

                    // Cập nhật badge
                    heSoValue.text(heso ? heso.toFixed(2) : '0.00'); // Use text()
                    luongTinhTu.text(formatNumber(luong) + ' đ'); // Use text()
                    heSoBadge.show(); // Use show()

                    // Kích hoạt tính tổng lương
                    calculateSalary();
                });

                // Pre-select Bậc if renewal
                @if(isset($isRenew) && $oldDienBien)
                    const oldNgachId = "{{ $oldDienBien->NgachLuongId }}";
                    const oldBacId = "{{ $oldDienBien->BacLuongId }}";
                    if (oldNgachId) {
                        ngachSelect.val(oldNgachId).trigger('change');
                        if (oldBacId) {
                            // Wait a bit for the cascade to finish
                            setTimeout(() => {
                                bacSelect.val(oldBacId).trigger('change');
                            }, 500);
                        }
                    }
                @endif
            });
        </script>
    @endpush
@endsection
