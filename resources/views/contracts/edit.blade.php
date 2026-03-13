@extends('layouts.app')

@section('title', 'Chỉnh sửa hợp đồng - Vietnam Rubber Group')

@push('styles')
    <style>
        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #0F5132;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #0F5132;
            box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1);
        }

        .required-star {
            color: #dc2626;
        }

        .salary-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }

        .salary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .salary-item:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
            font-weight: 700;
            font-size: 16px;
            color: #0F5132;
        }

        .file-upload-wrapper {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-wrapper:hover {
            border-color: #0F5132;
            background: #f0f7f4;
        }

        .file-info {
            display: none;
            margin-top: 12px;
            padding: 8px 12px;
            background: #d1fae5;
            border-radius: 6px;
            font-size: 13px;
            color: #065f46;
        }

        .file-info.show {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .locked-field {
            background-color: #f3f4f6 !important;
            cursor: not-allowed;
        }

        /* Select2 Custom Styling */
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

        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin chung -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="bi bi-person-badge"></i>
                        Thông tin cơ bản
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nhân viên <span class="required-star">*</span></label>
                            <select name="nhan_vien_id" id="nhanVienSelect" class="form-control select2" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($nhanvien as $nv)
                                    <option value="{{ $nv->id }}" {{ $hopDong->NhanVienId == $nv->id ? 'selected' : '' }}
                                        data-donvi="{{ $nv->ttCongViec->DonViId ?? '' }}"
                                        data-phongban="{{ $nv->ttCongViec->PhongBanId ?? '' }}"
                                        data-chucvu="{{ $nv->ttCongViec->ChucVuId ?? '' }}">
                                        {{ $nv->MaNhanVien }} - {{ $nv->Ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Người ký hợp đồng <span class="required-star">*</span></label>
                            <select name="NguoiKyId" id="nguoiKySelect" class="form-control select2" required>
                                <option value="">-- Chọn người ký --</option>
                                @foreach($nhanvien as $nv)
                                    <option value="{{ $nv->id }}" {{ $hopDong->NguoiKyId == $nv->id ? 'selected' : '' }}>
                                        {{ $nv->MaNhanVien }} - {{ $nv->Ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số hợp đồng <span class="required-star">*</span></label>
                            <input type="text" name="so_hop_dong" class="form-control" value="{{ $hopDong->SoHopDong }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại hợp đồng <span class="required-star">*</span></label>
                            <select name="loai_hop_dong_id" id="loaiHopDongSelect" class="form-control select2" required>
                                <option value="">-- Chọn loại hợp đồng --</option>
                                <option value="1" data-loai="thu_viec" {{ $hopDong->loaiHopDong?->id == 1 ? 'selected' : '' }}>Hợp đồng thử việc</option>
                                <option value="2" data-loai="chinh_thuc_xac_dinh_thoi_han" {{ $hopDong->loaiHopDong?->id == 2 ? 'selected' : '' }}>Hợp đồng lao động xác định thời hạn</option>
                                <option value="3" data-loai="chinh_thuc_khong_xac_dinh_thoi_han" {{ $hopDong->loaiHopDong?->id == 3 ? 'selected' : '' }}>Hợp đồng lao động không xác định thời hạn</option>
                                <option value="4" data-loai="khoan_viec" {{ $hopDong->loaiHopDong?->id == 4 ? 'selected' : '' }}>Hợp đồng khoán việc</option>
                                <option value="5" data-loai="thoi_vu" {{ $hopDong->loaiHopDong?->id == 5 ? 'selected' : '' }}>Hợp đồng thời vụ</option>
                            </select>
                            <input type="hidden" name="loai" id="loaiInput" value="{{ $hopDong->Loai }}">
                        </div>
                    </div>
                </div>

                <!-- Đơn vị & Công việc -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="bi bi-building"></i>
                        Vị trí công tác
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Đơn vị <span class="required-star">*</span></label>
                            <select name="don_vi_id" id="donViSelect" class="form-control select2" required>
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach($donvi as $dv)
                                    <option value="{{ $dv->id }}" {{ $hopDong->DonViId == $dv->id ? 'selected' : '' }}>{{ $dv->TenDonVi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phòng ban <span class="required-star">*</span></label>
                            <select name="phong_ban_id" id="phongBanSelect" class="form-control select2" required>
                                <option value="">-- Chọn phòng ban --</option>
                                @foreach($phongban as $pb)
                                    <option value="{{ $pb->id }}" {{ $hopDong->PhongBanId == $pb->id ? 'selected' : '' }}>{{ $pb->TenPhongBan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chức vụ <span class="required-star">*</span></label>
                            <select name="chuc_vu_id" id="chucVuSelect" class="form-control select2" required>
                                <option value="">-- Chọn chức vụ --</option>
                                @foreach($chucvu as $cv)
                                    <option value="{{ $cv->id }}" 
                                        data-phucap="{{ $cv->HeSoPhuCap }}" 
                                        data-loai="{{ $cv->Loai }}"
                                        {{ $hopDong->ChucVuId == $cv->id ? 'selected' : '' }}>
                                        {{ $cv->TenChucVu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12" id="chuc-vu-error" style="display: none;">
                            <div class="alert alert-danger d-flex align-items-center mb-0 mt-2">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div id="chuc-vu-error-message"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thời hạn & Hiệu lực -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="bi bi-calendar-check"></i>
                        Thời hạn & Trạng thái
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ngày bắt đầu <span class="required-star">*</span></label>
                            <input type="text" name="NgayBatDau" id="ngayBatDau" class="form-control datepicker" 
                                value="{{ \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="text" name="NgayKetThuc" id="ngayKetThuc" class="form-control datepicker" 
                                value="{{ $hopDong->NgayKetThuc ? \Carbon\Carbon::parse($hopDong->NgayKetThuc)->format('d/m/Y') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái <span class="required-star">*</span></label>
                            <select name="trang_thai" id="trangThaiSelect" class="form-control select2" required>
                                <option value="1" {{ $hopDong->TrangThai == 1 ? 'selected' : '' }}>Đang hiệu lực</option>
                                <option value="0" {{ $hopDong->TrangThai == 0 ? 'selected' : '' }}>Hết hiệu lực</option>
                                <option value="2" {{ $hopDong->TrangThai == 2 ? 'selected' : '' }}>Bị hủy/Thanh lý</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Lương & Phụ cấp -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="bi bi-cash-stack"></i>
                        Thông tin lương & Phụ cấp
                    </h2>
                    
                    <!-- Ngạch/Bậc Lương (Cascade) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                                <span class="badge badge-success">Hệ số: <span id="heSoValue">{{ number_format(optional(optional($hopDong->dienBienLuong)->bacLuong)->HeSo, 2) }}</span></span>
                                <small class="text-muted ms-2">→ Lương tính: <span id="luongTinhTu" class="fw-bold">{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }} đ</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Lương cơ bản <span class="required-star">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_co_ban" id="luongCoBan" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->LuongCoBan, 0, ',', '.') }}" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phụ cấp chức vụ</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_chuc_vu" id="phuCapChucVu" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapChucVu, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C trách nhiệm</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_trach_nhiem" id="phuCapTrachNhiem" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapTrachNhiem, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C độc hại</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_doc_hai" id="phuCapDocHai" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapDocHai, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C thâm niên</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_tham_nien" id="phuCapThamNien" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapThamNien, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C khu vực</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_khu_vuc" id="phuCapKhuVuc" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapKhuVuc, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Các khoản phụ cấp khác (Không tính đóng BHXH)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">P.C ăn trưa</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_an_trua" id="phuCapAnTrua" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapAnTrua, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C xăng xe</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_xang_xe" id="phuCapXangXe" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapXangXe, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C điện thoại</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_dien_thoai" id="phuCapDienThoai" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapDienThoai, 0, ',', '.') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">P.C hỗ trợ nhà ở</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_nha_o" id="phuCapNhaO" class="form-control formatted-number salary-input" 
                                    value="{{ number_format($hopDong->PhuCapKhac, 0, ',', '.') }}"> <!-- Pre-fill from PhuCapKhac if used for housing -->
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">P.C khác</label>
                            <div class="input-group">
                                <input type="text" name="phu_cap_khac" id="phuCapKhac" class="form-control formatted-number salary-input" 
                                    value="0">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Tóm tắt lương -->
                <div class="salary-card mb-4" id="salaryCard">
                    <h5 class="fw-bold mb-3">Tóm tắt thu nhập</h5>
                    <div class="salary-item">
                        <span>Lương cơ bản:</span>
                        <span id="displayLuongCoBan">0 ₫</span>
                    </div>
                    <div class="salary-item">
                        <span>P.C đóng BHXH:</span>
                        <span id="displayPhuCapBHXH">0 ₫</span>
                    </div>
                    <div class="salary-item" style="color: #6366f1;">
                        <span>Lương đóng BHXH:</span>
                        <span id="displayLuongBHXH">0 ₫</span>
                    </div>
                    <div class="salary-item">
                        <span>P.C khác:</span>
                        <span id="displayPhuCapKhongBHXH">0 ₫</span>
                    </div>
                    <div class="salary-item">
                        <span>TỔNG THU NHẬP:</span>
                        <span id="displayTongThuNhap">0 ₫</span>
                    </div>
                    <input type="hidden" name="tong_luong" id="tongLuongInput" value="{{ $hopDong->TongLuong }}">
                </div>

                <!-- Đính kèm file -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        Hợp đồng đính kèm
                    </h2>
                    @if($hopDong->File)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-file-earmark-check me-2"></i>
                            <a href="/{{ $hopDong->File }}" target="_blank" class="text-decoration-none">Xem file hiện tại</a>
                        </div>
                    @endif
                    <div class="file-upload-wrapper" onclick="document.getElementById('fileUpload').click()">
                        <div class="text-muted">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin-bottom: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mb-0">Kéo thả hoặc nhấn để tải lên file hợp đồng mới</p>
                            <small>(PDF, Word. Tối đa 10MB)</small>
                        </div>
                    </div>
                    <input type="file" name="file" id="fileUpload" class="d-none" accept=".pdf,.doc,.docx" onchange="handleFileSelect(this)">
                    <div id="fileInfo" class="file-info mt-2">
                        <span id="fileName"></span>
                        <button type="button" class="btn btn-sm text-danger p-0" onclick="removeFile()">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-4 g-2 row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm py-3 fw-bold">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('hop-dong.info', $hopDong->id) }}" class="btn btn-secondary w-100 py-3 fw-bold">
                            Hủy
                        </a>
                    </div>
                </div>
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
                            confirmButtonColor: '#0F5132'
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
                        confirmButtonColor: '#0F5132'
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
                const phuCapTrachNhiem = parseFloat(unformatNumber(document.getElementById('phuCapTrachNhiem').value)) || 0;
                const phuCapDocHai = parseFloat(unformatNumber(document.getElementById('phuCapDocHai').value)) || 0;
                const phuCapThamNien = parseFloat(unformatNumber(document.getElementById('phuCapThamNien').value)) || 0;
                const phuCapKhuVuc = parseFloat(unformatNumber(document.getElementById('phuCapKhuVuc').value)) || 0;

                const phuCapAnTrua = parseFloat(unformatNumber(document.getElementById('phuCapAnTrua').value)) || 0;
                const phuCapXangXe = parseFloat(unformatNumber(document.getElementById('phuCapXangXe').value)) || 0;
                const phuCapDienThoai = parseFloat(unformatNumber(document.getElementById('phuCapDienThoai').value)) || 0;
                const phuCapNhaO = parseFloat(unformatNumber(document.getElementById('phuCapNhaO').value)) || 0;
                const phuCapKhac = parseFloat(unformatNumber(document.getElementById('phuCapKhac').value)) || 0;

                const tongPhuCapBHXH = phuCapChucVu + phuCapTrachNhiem + phuCapDocHai + phuCapThamNien + phuCapKhuVuc;
                const tongPhuCapKhongBHXH = phuCapAnTrua + phuCapXangXe + phuCapDienThoai + phuCapNhaO + phuCapKhac;
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
                            confirmButtonColor: '#0F5132'
                        }).then(() => {
                            window.location.href = "{{ route('hop-dong.info', $hopDong->id) }}";
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
                        bacSelect.append(`<option value="${b.id}" data-heso="${b.heso}">Bậc ${b.bac} – Hệ số ${b.heso.toFixed(2)}</option>`);
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
                $('#heSoValue').text(heso.toFixed(2));
                $('#luongTinhTu').text(formatNumber(luong) + ' đ');
                $('#heSoBadge').show();
                calculateSalary();
            });

            // Initial calculation
            calculateSalary();
        });
    </script>
@endpush
