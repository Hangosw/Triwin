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

        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
        }

        body.dark-theme .file-upload-area {
            border-color: #3d445e;
            background: rgba(255, 255, 255, 0.02);
        }

        .file-upload-area:hover {
            border-color: #0BAA4B;
            background: rgba(11, 170, 75, 0.05);
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
        <input type="hidden" name="phieu_dieu_chuyen_id" id="phieuDieuChuyenId">

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
                                data-chucvu-id="{{ $nv->ttCongViec->ChucVuId ?? '' }}">
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
                        <option value="1" data-ma="HDTV" data-loai="thu_viec" {{ $hopDong->Loai == 'thu_viec' ? 'selected' : '' }}>Hợp đồng thử việc</option>
                        <option value="2" data-ma="HDLD" data-loai="chinh_thuc_xac_dinh_thoi_han" {{ $hopDong->Loai == 'chinh_thuc_xac_dinh_thoi_han' ? 'selected' : '' }}>Hợp đồng lao động xác định thời hạn</option>
                        <option value="3" data-ma="HDLD" data-loai="chinh_thuc_khong_xac_dinh_thoi_han" {{ $hopDong->Loai == 'chinh_thuc_khong_xac_dinh_thoi_han' ? 'selected' : '' }}>Hợp đồng lao động không xác định thời hạn</option>
                        <option value="4" data-ma="HDKV" data-loai="khoan_viec" {{ $hopDong->Loai == 'khoan_viec' ? 'selected' : '' }}>Hợp đồng khoán việc</option>
                        <option value="5" data-ma="HDTV" data-loai="thoi_vu" {{ $hopDong->Loai == 'thoi_vu' ? 'selected' : '' }}>Hợp đồng thời vụ</option>
                        <option value="7" data-ma="NDA" data-loai="nda" {{ str_starts_with($hopDong->Loai ?? '', 'nda') ? 'selected' : '' }}>Thỏa thuật bảo mật (NDA)</option>
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
                <div class="form-group">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="text" name="NgayKetThuc" id="ngayKetThuc" class="form-control datepicker" 
                        value="{{ $hopDong->NgayKetThuc ? \Carbon\Carbon::parse($hopDong->NgayKetThuc)->format('d/m/Y') : '' }}">
                    <div class="help-text">Để trống nếu là "Không xác định thời hạn"</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái <span class="required-star">*</span></label>
                    <select name="trang_thai" class="form-control select2" required>
                        <option value="1" {{ $hopDong->TrangThai == 1 ? 'selected' : '' }}>Còn hiệu lực</option>
                        <option value="0" {{ $hopDong->TrangThai == 0 ? 'selected' : '' }}>Hết hiệu lực</option>
                        <option value="2" {{ $hopDong->TrangThai == 2 ? 'selected' : '' }}>Bị hủy/Thanh lý</option>
                    </select>
                </div>
            </div>

            <h2>
                <i class="bi bi-cash-coin"></i>
                Cấu trúc lương cơ bản
            </h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ngạch lương</label>
                            <select id="ngachLuongSelect" name="ngach_luong_id" class="form-control select2">
                                <option value="">-- Chọn ngạch lương --</option>
                                @foreach($ngachLuongs as $n)
                                    <option value="{{ $n->id }}" {{ optional($hopDong->dienBienLuong)->NgachLuongId == $n->id ? 'selected' : '' }}>
                                        {{ $n->Ma }} - {{ $n->Ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bậc lương</label>
                            <select id="bacLuongSelect" name="bac_luong_id" class="form-control select2" 
                                {{ optional($hopDong->dienBienLuong)->NgachLuongId ? '' : 'disabled' }}>
                                <option value="">-- Chọn bậc lương --</option>
                                @if(optional($hopDong->dienBienLuong)->NgachLuongId)
                                    @php
                                        $selectedNgach = $ngachLuongs->firstWhere('id', $hopDong->dienBienLuong->NgachLuongId);
                                    @endphp
                                    @foreach($selectedNgach->bacLuongs as $b)
                                        <option value="{{ $b->id }}" data-heso="{{ $b->HeSo }}"
                                            {{ optional($hopDong->dienBienLuong)->BacLuongId == $b->id ? 'selected' : '' }}>
                                            Bậc {{ $b->Bac }} - Hệ số {{ number_format($b->HeSo, 2) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div id="heSoBadge" class="mt-2" style="display: {{ optional($hopDong->dienBienLuong)->BacLuongId ? 'block' : 'none' }};">
                                <span class="badge bg-success">Hệ số: <span id="heSoValue">{{ number_format(optional(optional($hopDong->dienBienLuong)->bacLuong)->HeSo, 2) }}</span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lương cơ bản <span class="required-star">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_co_ban" id="luongCoBan" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }}" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phụ cấp chức vụ</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_chuc_vu" id="phuCapChucVu" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapChucVu, 0, ',', '.') }}" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>

            <div style="margin-top: 24px; margin-bottom: 12px;">
                <strong style="color: #1f2937; font-size: 15px;">📋 Phụ cấp tính BHXH</strong>
            </div>
            <div class="form-row">
                @foreach($dmAllowances->where('is_bhxh', 1) as $dm)
                    <div class="form-group">
                        <label class="form-label">{{ $dm->noi_dung }}</label>
                        <div class="input-group">
                            <input type="text" name="allowances[{{ $dm->id }}]" 
                                class="form-control formatted-number salary-input allowance-bhxh" 
                                value="{{ $hopDong->phuCaps->contains($dm->id) ? number_format($hopDong->phuCaps->find($dm->id)->pivot->so_tien, 0, ',', '.') : '0' }}">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 24px; margin-bottom: 12px;">
                <strong style="color: #1f2937; font-size: 15px;">💼 Phụ cấp KHÔNG đóng BHXH</strong>
            </div>
            <div class="form-row">
                @foreach($dmAllowances->where('is_bhxh', 0) as $dm)
                    <div class="form-group">
                        <label class="form-label">{{ $dm->noi_dung }}</label>
                        <div class="input-group">
                            <input type="text" name="allowances[{{ $dm->id }}]" 
                                class="form-control formatted-number salary-input allowance-ngoai-bhxh" 
                                value="{{ $hopDong->phuCaps->contains($dm->id) ? number_format($hopDong->phuCaps->find($dm->id)->pivot->so_tien, 0, ',', '.') : '0' }}">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>
                @endforeach
            </div>

        <!-- Tóm tắt lương -->
        <div id="salaryCard"
            style="margin-top: 24px; padding: 20px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #0BAA4B; border-radius: 12px; margin-bottom: 24px;">
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
                <i class="bi bi-cloud-upload" style="font-size: 48px; color: #0BAA4B;"></i>
                <div style="font-weight: 500; color: #374151; margin-bottom: 4px;">Click để tải file lên</div>
                <div style="font-size: 13px; color: #6b7280;">PDF, DOC, DOCX (MAX. 10MB)</div>
                <input type="file" name="file" id="fileUpload" class="d-none" accept=".pdf,.doc,.docx" onchange="handleFileSelect(this)">
            </div>

            <div id="fileInfo" class="mt-3" style="display: none; padding: 12px; background: #f0fdf4; border-radius: 8px; border: 1px solid #dcfce7;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-file-earmark-arrow-up" style="color: #0BAA4B;"></i>
                    <span id="fileName" style="font-size: 13px; font-weight: 500; color: #166534; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
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

    <!-- Data cho JS -->
    <script id="ngachBacData" type="application/json">
        {!! json_encode($ngachLuongs->map(fn($n) => [
            'id' => $n->id,
            'bacs' => $n->bacLuongs->map(fn($b) => ['id' => $b->id, 'bac' => $b->Bac, 'heso' => $b->HeSo])
        ])) !!}
    </script>
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
                    document.getElementById('fileInfo').classList.add('show');
                }
            }

            window.removeFile = function () {
                document.getElementById('fileUpload').value = '';
                document.getElementById('fileInfo').classList.remove('show');
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
            });

            // Calculate and display salary breakdown
            function calculateSalary() {
                const luongCoBan = parseFloat(unformatNumber(document.getElementById('luongCoBan').value)) || 0;
                const phuCapChucVu = parseFloat(unformatNumber(document.getElementById('phuCapChucVu').value)) || 0;
                
                let tongPhuCapBHXH = phuCapChucVu;
                document.querySelectorAll('.allowance-bhxh').forEach(input => {
                    tongPhuCapBHXH += parseFloat(unformatNumber(input.value)) || 0;
                });

                let tongPhuCapKhongBHXH = 0;
                document.querySelectorAll('.allowance-ngoai-bhxh').forEach(input => {
                    tongPhuCapKhongBHXH += parseFloat(unformatNumber(input.value)) || 0;
                });

                const luongBHXH = luongCoBan + tongPhuCapBHXH;
                const tongThuNhap = luongBHXH + tongPhuCapKhongBHXH;

                const formatVND = (amount) => amount.toLocaleString('vi-VN') + ' ₫';

                document.getElementById('displayLuongCoBan').textContent = formatVND(luongCoBan);
                document.getElementById('displayPhuCapBHXH').textContent = formatVND(tongPhuCapBHXH);
                document.getElementById('displayLuongBHXH').textContent = formatVND(luongBHXH);
                document.getElementById('displayPhuCapKhongBHXH').textContent = formatVND(tongPhuCapKhongBHXH);
                document.getElementById('displayTongThuNhap').textContent = formatVND(tongThuNhap);

                document.getElementById('tongLuongInput').value = Math.round(tongThuNhap);
            }

            document.querySelectorAll('.salary-input').forEach(input => {
                input.addEventListener('input', calculateSalary);
            });

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatNumber(str) {
                return str.toString().replace(/\./g, '');
            }

            const mucLuongCoSo = {{ $mucLuongCoSo }};

            $('#chucVuSelect').on('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const heSoPhuCap = parseFloat(selectedOption.getAttribute('data-phucap')) || 0;
                const phuCapAmount = heSoPhuCap * mucLuongCoSo;
                document.getElementById('phuCapChucVu').value = formatNumber(Math.round(phuCapAmount));
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

            // Cascade Ngach/Bac Lương
            const ngachData = JSON.parse(document.getElementById('ngachBacData').textContent);
            
            $('#loaiHopDongSelect').on('change', function () {
                const option = $(this).find('option:selected');
                const loai = option.data('loai') || '';
                document.getElementById('loaiInput').value = loai;
            });

            $('#ngachLuongSelect').on('change', function () {
                const ngachId = parseInt($(this).val());
                const bacSelect = $('#bacLuongSelect');
                bacSelect.html('<option value="">-- Chọn bậc lương --</option>').prop('disabled', true);
                $('#heSoBadge').hide();

                if (!ngachId) return;

                const ngach = ngachData.find(n => n.id === ngachId);
                if (ngach && ngach.bacs.length) {
                    ngach.bacs.forEach(b => {
                        bacSelect.append(`<option value="${b.id}" data-heso="${b.heso}">Bậc ${b.bac} – Hệ số ${Number(b.heso).toFixed(2)}</option>`);
                    });
                    bacSelect.prop('disabled', false).trigger('change');
                }
            });

            $('#bacLuongSelect').on('change', function () {
                const opt = $(this).find('option:selected');
                const heso = parseFloat(opt.data('heso'));
                if (!heso) {
                    $('#heSoBadge').hide();
                    return;
                }
                const luong = Math.round(heso * mucLuongCoSo);
                $('#luongCoBan').val(formatNumber(luong)).trigger('input');
                $('#heSoValue').text(heso ? heso.toFixed(2) : '0.00');
                $('#heSoBadge').show();
                calculateSalary();
            });

            // Initial calculation
            calculateSalary();
        });
    </script>
@endpush
