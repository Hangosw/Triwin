@extends('layouts.app')

@section('title', 'Thêm nhân viên mới - Vietnam Rubber Group')

@push('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-item input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #0BAA4B;
        }

        .radio-item label {
            margin-bottom: 0 !important;
            cursor: pointer;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-area:hover {
            border-color: #0BAA4B;
            background-color: #f9fafb;
        }

        .upload-area svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            color: #6b7280;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin: 16px auto 0;
            display: none;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }

        .preview-item {
            position: relative;
            width: 120px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
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

        .validation-error svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {

            .form-row,
            .form-row-3 {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn {
                width: 100%;
            }
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

        .select2-container {
            width: 100% !important;
        }

        /* Cropper Custom Styling */
        .img-container {
            max-height: 500px;
            overflow: hidden;
        }
        
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }

        /* Modal Custom Styling (since project doesn't have global Bootstrap CSS) */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055;
            display: none;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            overscroll-behavior-y: contain;
            outline: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal.show {
            display: flex !important;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: none;
            max-width: 800px;
            width: 100%;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            outline: 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            background: linear-gradient(135deg, #0BAA4B 0%, #088c3d 100%);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
            font-weight: 600;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            flex-shrink: 0;
            align-items: center;
            justify-content: flex-end;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
            gap: 10px;
        }

        .btn-close {
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
            border: 0;
            padding: 0.5rem;
            opacity: 0.8;
            cursor: pointer;
        }

        .btn-close:hover {
            opacity: 1;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
@endpush

@section('content')
    <!-- Header -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 30px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Thêm nhân viên mới</h1>
            <p style="color: #6b7280;">Nhập thông tin đầy đủ để thêm nhân viên vào hệ thống</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại
        </a>
    </div>

    <form action="{{ route('nhan-vien.tao') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Thông tin cá nhân -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Thông tin cá nhân
            </h2>

            <!-- Ảnh đại diện -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label>Ảnh đại diện</label>
                <div class="upload-area" onclick="document.getElementById('avatar').click()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>Độc thân
                    <div style="font-weight: 500; color: #374151; margin-bottom: 4px;">
                        Click để tải ảnh lên
                    </div>
                    <div class="help-text">PNG, JPG hoặc GIF (MAX. 2MB)</div>
                    <input type="file" id="avatar" accept="image/*" style="display: none;"
                        onchange="initCropper(this)">
                    <input type="hidden" name="cropped_avatar" id="croppedAvatarInput">
                    <img id="preview" class="preview-image">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ và tên <span class="required">*</span></label>
                    <input type="text" name="Ten" placeholder="Nguyễn Văn A" required>
                </div>

                <div class="form-group">
                    <label>Ngày sinh <span class="required">*</span></label>
                    <input type="text" name="NgaySinh" class="datepicker" placeholder="Chọn ngày sinh" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Giới tính <span class="required">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="1" name="GioiTinh" value="1" checked>
                            <label for="1">Nam</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="0" name="GioiTinh" value="0">
                            <label for="0">Nữ</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số CCCD/CMND <span class="required">*</span></label>
                    <input type="text" name="SoCCCD" id="SoCCCD" placeholder="001234567890" required>

                    <!-- Thông báo lỗi validation CCCD -->
                    <div id="cccd-error" class="validation-error" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span id="cccd-error-message"></span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ảnh CCCD (2 ảnh) <span class="required">*</span></label>
                    <input type="file" name="anh_cccd[]" id="anh_cccd" multiple accept="image/*" required onchange="previewImages(this, 'cccd-preview')">
                    <div class="help-text">Tải lên mặt trước và mặt sau của CCCD</div>
                    <div id="cccd-preview" class="preview-container"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ngày cấp CCCD</label>
                    <input type="text" name="NgayCap" class="datepicker" placeholder="Chọn ngày cấp">
                </div>

                <div class="form-group">
                    <label>Nơi cấp CCCD</label>
                    <input type="text" name="NoiCap" placeholder="Công an TP. Hồ Chí Minh">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Địa chỉ thường trú <span class="required">*</span></label>
                <textarea name="DiaChi" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                    required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Số điện thoại <span class="required">*</span></label>
                    <input type="tel" name="SoDienThoai" placeholder="0901234567" required>
                </div>

                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="Email" placeholder="nhanvien@vietamrubber.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Dân tộc</label>
                    <input type="text" name="DanToc" placeholder="Kinh" value="Kinh">
                </div>

                <div class="form-group">
                    <label>Tôn giáo</label>
                    <input type="text" name="TonGiao" placeholder="Không">
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Quốc tịch</label>
                    <input type="text" name="QuocTich" placeholder="Việt Nam" value="Việt Nam">
                </div>

                <div class="form-group">
                    <label>Tình trạng hôn nhân</label>
                    <select name="TinhTrangHonNhan" class="select2">
                        <option value="0">Độc thân</option>
                        <option value="1">Đã kết hôn</option>
                        <option value="2">Ly hôn</option>
                        <option value="3">Góa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Trạng thái nhân viên</label>
                    <select name="TrangThai" class="select2">
                        <option value="1" selected>Làm tại công ty</option>
                        <option value="0">Nghỉ làm</option>
                        <option value="2">Làm từ xa (WFH)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Thông tin công việc -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Thông tin công việc
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Mã nhân viên <span style="color: #6b7280; font-weight: normal; font-size: 13px;">(Tự động
                            tạo)</span></label>
                    <input type="text" disabled value="Mã sẽ được tạo tự động"
                        style="background-color: #f3f4f6; color: #6b7280;">
                </div>

                <div class="form-group">
                    <label>Loại nhân viên <span class="required">*</span></label>
                    <select name="Nhom" class="select2" required>
                        <option value="">-- Chọn loại nhân viên --</option>
                        <option value="van_phong">Văn phòng</option>
                        <option value="cong_nhan">Công nhân</option>
                    </select>
                </div>
            </div>

            <div class="form-row">


                <div class="form-group">
                    <label>Phòng ban <span class="required">*</span></label>
                    <select name="PhongBanId" id="PhongBanId" class="select2" required>
                        <option value="">-- Chọn phòng ban --</option>
                        @foreach ($phongBans as $phongBan)
                            <option value="{{ $phongBan->id }}">{{ $phongBan->Ten }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Chức vụ <span class="required">*</span></label>
                    <select name="ChucVuId" id="ChucVuId" class="select2" required disabled>
                        <option value="">-- Chọn phòng ban trước --</option>
                        @foreach ($chucVus as $chucVu)
                            <option value="{{ $chucVu->id }}" data-loai="{{ $chucVu->Loai }}">{{ $chucVu->Ten }}</option>
                        @endforeach
                    </select>

                    <!-- Thông báo lỗi validation -->
                    <div id="chuc-vu-error" class="validation-error" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span id="chuc-vu-error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ngày vào làm <span class="required">*</span></label>
                    <input type="text" name="NgayTuyenDung" class="datepicker" placeholder="Chọn ngày vào làm" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Trình độ học vấn</label>
                    <select name="TrinhDoHocVan" class="select2">
                        <option value="">-- Chọn trình độ --</option>
                        <option value="Trung học cơ sở">Trung học cơ sở</option>
                        <option value="Trung học phổ thông">Trung học phổ thông</option>
                        <option value="Trung cấp">Trung cấp</option>
                        <option value="Cao đẳng">Cao đẳng</option>
                        <option value="Đại học">Đại học</option>
                        <option value="Thạc sĩ">Thạc sĩ</option>
                        <option value="Tiến sĩ">Tiến sĩ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Chuyên ngành</label>
                    <input type="text" name="ChuyenNganh"
                        placeholder="Quản trị kinh doanh, Kế toán, Công nghệ thông tin...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Trình độ chuyên môn</label>
                    <input type="text" name="TrinhDoChuyenMon" placeholder="Kỹ sư, Cử nhân, Kỹ thuật viên...">
                </div>

                <div class="form-group">
                    <label>Ngoại ngữ</label>
                    <input type="text" name="NgoaiNgu" placeholder="Tiếng Anh, Tiếng Nhật...">
                </div>
            </div>
        </div>

        <!-- Thông tin ngân hàng -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Thông tin ngân hàng (để chi trả lương)
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Tên ngân hàng</label>
                    <select name="TenNganHang" class="select2">
                        <option value="">-- Chọn ngân hàng --</option>
                        <option value="Vietcombank">Vietcombank - Ngân hàng TMCP Ngoại thương Việt Nam</option>
                        <option value="VietinBank">VietinBank - Ngân hàng TMCP Công thương Việt Nam</option>
                        <option value="BIDV">BIDV - Ngân hàng TMCP Đầu tư và Phát triển Việt Nam</option>
                        <option value="Agribank">Agribank - Ngân hàng Nông nghiệp và Phát triển Nông thôn</option>
                        <option value="Techcombank">Techcombank - Ngân hàng TMCP Kỹ thương Việt Nam</option>
                        <option value="ACB">ACB - Ngân hàng TMCP Á Châu</option>
                        <option value="MB Bank">MB Bank - Ngân hàng TMCP Quân đội</option>
                        <option value="VPBank">VPBank - Ngân hàng TMCP Việt Nam Thịnh Vượng</option>
                        <option value="TPBank">TPBank - Ngân hàng TMCP Tiên Phong</option>
                        <option value="Sacombank">Sacombank - Ngân hàng TMCP Sài Gòn Thương Tín</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Số tài khoản</label>
                    <input type="text" name="SoTaiKhoan" placeholder="1234567890">
                </div>
            </div>

            <div class="form-group">
                <label>Chi nhánh ngân hàng</label>
                <input type="text" name="ChiNhanhNganHang" placeholder="Chi nhánh TP. Hồ Chí Minh">
            </div>
        </div>

        <!-- Thông tin BHXH -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Thông tin bảo hiểm xã hội
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Số BHXH</label>
                    <input type="text" name="BHXH" placeholder="1234567890">
                </div>

                <div class="form-group">
                    <label>Ảnh BHXH (Nhiều ảnh)</label>
                    <input type="file" name="anh_bhxh[]" id="anh_bhxh" multiple accept="image/*" onchange="previewImages(this, 'bhxh-preview')">
                    <div class="help-text">Tải lên các ảnh liên quan đến BHXH (nếu có)</div>
                    <div id="bhxh-preview" class="preview-container"></div>
                </div>

                <div class="form-group">
                    <label>Nơi cấp BHXH</label>
                    <input type="text" name="NoiCapBHXH" placeholder="BHXH TP. Hồ Chí Minh">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Số thẻ BHYT</label>
                    <input type="text" name="BHYT" placeholder="DN1234567890123">
                </div>

                <div class="form-group">
                    <label>Nơi đăng ký KCB ban đầu</label>
                    <input type="text" name="NoiDangKyKCB" placeholder="Bệnh viện Chợ Rẫy">
                </div>
            </div>
        </div>

        <!-- Người liên hệ khẩn cấp -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Người liên hệ khẩn cấp
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ tên người liên hệ</label>
                    <input type="text" name="nguoi_lien_he_khan_cap" placeholder="Nguyễn Văn B">
                </div>

                <div class="form-group">
                    <label>Quan hệ</label>
                    <select name="quan_he_nguoi_lien_he" class="select2">
                        <option value="">-- Chọn quan hệ --</option>
                        <option value="Bố">Bố</option>
                        <option value="Mẹ">Mẹ</option>
                        <option value="Vợ">Vợ</option>
                        <option value="Chồng">Chồng</option>
                        <option value="Anh/Chị/Em ruột">Anh/Chị/Em ruột</option>
                        <option value="Con">Con</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Số điện thoại người liên hệ</label>
                    <input type="tel" name="sdt_nguoi_lien_he" placeholder="0901234567">
                </div>

                <div class="form-group">
                    <label>Địa chỉ người liên hệ</label>
                    <input type="text" name="dia_chi_nguoi_lien_he" placeholder="Địa chỉ liên hệ">
                </div>
            </div>
        </div>

        <!-- Ghi chú -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Thông tin bổ sung
            </h2>

            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="Note" placeholder="Nhập các thông tin bổ sung khác (nếu có)..."
                    style="min-height: 120px;"></textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-section">
            <div class="form-actions">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Lưu thông tin
                </button>
            </div>
        </div>
    </form>

    <!-- Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true" data-bs-backdrop="static" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Cắt ảnh đại diện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="cropAndSave">Cắt và Lưu</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
        <script>
            let cropper;
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            const imageToCrop = document.getElementById('imageToCrop');

            function initCropper(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageToCrop.src = e.target.result;
                        cropperModal.show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    guides: false,
                    autoCropArea: 1,
                    dragMode: 'move',
                    background: false,
                    ready: function() {
                        // Success callback
                    }
                });
            });

            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                // Clear input to allow re-selecting the same file
                document.getElementById('avatar').value = '';
            });

            document.getElementById('cropAndSave').addEventListener('click', function() {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 400,
                        height: 400,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    const preview = document.getElementById('preview');
                    preview.src = croppedDataUrl;
                    preview.style.display = 'block';
                    document.getElementById('croppedAvatarInput').value = croppedDataUrl;
                    cropperModal.hide();
                }
            });

            // Wait for DOM to be fully loaded
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Select2
                $('.select2').select2({
                    width: '100%',
                    placeholder: function () {
                        $(this).data('placeholder');
                    },
                    allowClear: true
                });

                // Auto format phone number
                document.querySelectorAll('input[type="tel"]').forEach(input => {
                    input.addEventListener('input', function (e) {
                        let value = e.target.value.replace(/\D/g, '');
                        e.target.value = value;
                    });
                });

                // Enable/disable chức vụ select khi chọn phòng ban
                const phongBanSelect = $('#PhongBanId');
                const chucVuSelect = $('#ChucVuId');

                // Helper function to get CSRF token safely
                function getCsrfToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) {
                        return meta.content;
                    }
                    // Fallback: try to get from form
                    const csrfInput = document.querySelector('input[name="_token"]');
                    return csrfInput ? csrfInput.value : '';
                }

                if (phongBanSelect.length && chucVuSelect.length) {
                    phongBanSelect.on('change', function () {
                        if (this.value) {
                            chucVuSelect.prop('disabled', false).trigger('change');
                            chucVuSelect.find('option[value=""]').text('-- Chọn chức vụ --');
                        } else {
                            chucVuSelect.prop('disabled', true).val('').trigger('change');
                            chucVuSelect.find('option[value=""]').text('-- Chọn phòng ban trước --');
                            hideChucVuError();
                        }
                    });

                    // Kiểm tra chức vụ khi chọn
                    chucVuSelect.on('change', function () {
                        const phongBanId = phongBanSelect.val();
                        const chucVuId = this.value;

                        if (!phongBanId || !chucVuId) {
                            hideChucVuError();
                            return;
                        }

                        // Gọi API kiểm tra
                        fetch('/api/check-chuc-vu-ton-tai', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                PhongBanId: phongBanId,
                                ChucVuId: chucVuId,
                                nhan_vien_id: null // Null khi tạo mới
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
                                console.error('Error:', error);
                            });
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

                // Real-time CCCD validation
                const cccdInput = document.getElementById('SoCCCD');
                if (cccdInput) {
                    let cccdTimeout;
                    cccdInput.addEventListener('input', function () {
                        clearTimeout(cccdTimeout);
                        const value = this.value.trim();

                        // Auto format - chỉ cho phép số
                        this.value = value.replace(/\D/g, '');

                        if (this.value.length >= 12) {
                            cccdTimeout = setTimeout(() => {
                                checkCCCDExists(this.value);
                            }, 500); // Debounce 500ms
                        } else {
                            hideCCCDError();
                        }
                    });
                }

                function checkCCCDExists(cccd) {
                    fetch('/api/check-cccd-exists', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({ SoCCCD: cccd, nhan_vien_id: null })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                showCCCDError(data.message);
                            } else {
                                hideCCCDError();
                            }
                            updateSubmitButton();
                        })
                        .catch(error => {
                            console.error('Error checking CCCD:', error);
                        });
                }

                function showCCCDError(message) {
                    const errorDiv = document.getElementById('cccd-error');
                    const errorMessage = document.getElementById('cccd-error-message');

                    if (errorDiv && errorMessage) {
                        errorMessage.textContent = message;
                        errorDiv.style.display = 'flex';
                    }
                }

                function hideCCCDError() {
                    const errorDiv = document.getElementById('cccd-error');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                }

                // Helper function to check if there are validation errors
                function hasValidationErrors() {
                    const errorDivs = [
                        'cccd-error',
                        'chuc-vu-error'
                    ];

                    return errorDivs.some(id => {
                        const div = document.getElementById(id);
                        return div && div.style.display !== 'none';
                    });
                }

                // Update submit button state
                function updateSubmitButton() {
                    const submitBtn = document.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = hasValidationErrors();
                    }
                }

                // Form submission with AJAX
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        // Check for validation errors
                        if (hasValidationErrors()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Vui lòng sửa các lỗi trước khi tiếp tục',
                                confirmButtonColor: '#0BAA4B'
                            });
                            return;
                        }

                        // Basic required field validation
                        const requiredFields = document.querySelectorAll('[required]');
                        let isValid = true;

                        requiredFields.forEach(field => {
                            if (!field.value.trim()) {
                                isValid = false;
                                field.style.borderColor = '#dc2626';
                            } else {
                                field.style.borderColor = '#d1d5db';
                            }
                        });

                        if (!isValid) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Thiếu thông tin!',
                                text: 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)',
                                confirmButtonColor: '#0BAA4B'
                            });

                            // Scroll to first invalid field
                            const firstInvalid = Array.from(requiredFields).find(f => !f.value.trim());
                            if (firstInvalid) {
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                firstInvalid.focus();
                            }
                            return;
                        }

                        // Show loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng đợi',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form via AJAX
                        const formData = new FormData(this);

                        // Convert date formats from dd-mm-yyyy or dd/mm/yyyy to yyyy-mm-dd for MySQL
                        const dateFields = ['NgaySinh', 'NgayCap', 'NgayTuyenDung'];
                        dateFields.forEach(fieldName => {
                            const dateValue = formData.get(fieldName);
                            if (dateValue) {
                                // Support both - and / separators
                                const separator = dateValue.includes('-') ? '-' : (dateValue.includes('/') ? '/' : null);
                                if (separator) {
                                    const parts = dateValue.split(separator);
                                    if (parts.length === 3 && parts[0].length === 2 && parts[2].length === 4) {
                                        // Convert from dd/mm/yyyy to yyyy-mm-dd
                                        const convertedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                        formData.set(fieldName, convertedDate);
                                    }
                                }
                            }
                        });

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: data.message,
                                        confirmButtonColor: '#0BAA4B',
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        window.location.href = data.redirect;
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi!',
                                        text: data.message || 'Có lỗi xảy ra khi thêm nhân viên',
                                        confirmButtonColor: '#0BAA4B'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: 'Có lỗi xảy ra khi thêm nhân viên. Vui lòng thử lại.',
                                    confirmButtonColor: '#0BAA4B'
                                });
                            });
                    });
                }

                // Remove error border on input
                document.querySelectorAll('input, select, textarea').forEach(field => {
                    field.addEventListener('input', function () {
                        this.style.borderColor = '#d1d5db';
                    });
                });
            });

            function previewImages(input, containerId) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
                
                if (input.files) {
                    Array.from(input.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'preview-item';
                            div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                            container.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            }
        </script>
    @endpush
@endsection
