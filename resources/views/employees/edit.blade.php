@extends('layouts.app')

@section('title', 'Chỉnh sửa nhân viên - Vietnam Rubber Group')

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

        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
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

        /* Dark Mode Overrides */
        body.dark-theme h1 {
            color: #e8eaf0 !important;
        }

        body.dark-theme p {
            color: #8b93a8 !important;
        }

        body.dark-theme .form-section {
            background-color: #1a1d27;
            border-color: #2e3349;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        body.dark-theme .form-section h2 {
            color: #e8eaf0;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .form-group label {
            color: #8b93a8;
        }

        body.dark-theme .form-group input,
        body.dark-theme .form-group select,
        body.dark-theme .form-group textarea {
            background-color: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme .form-group input::placeholder,
        body.dark-theme .form-group textarea::placeholder {
            color: #4b5563;
        }

        body.dark-theme .help-text {
            color: #8b93a8;
        }

        body.dark-theme .form-actions {
            border-top-color: #2e3349;
        }

        body.dark-theme .select2-container--default .select2-selection--single {
            background-color: #21263a;
            border-color: #2e3349;
        }

        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e8eaf0;
        }

        body.dark-theme .radio-item label {
            color: #e8eaf0;
        }

        body.dark-theme .modal-content {
            background-color: #1a1d27;
            color: #e8eaf0;
            border-color: #2e3349;
        }

        body.dark-theme .modal-header {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .modal-footer {
            border-top-color: #2e3349;
            background-color: #13161f;
        }

        body.dark-theme #cccd-existing p,
        body.dark-theme #bhxh-existing p {
            color: #8b93a8 !important;
        }

        body.dark-theme #avatarPreview {
            background: #13161f !important;
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
    @php
        $isRestricted = auth()->user()->can('Chỉnh Sửa Một Phần Nhân Viên') && !auth()->user()->can('Sửa Nhân Viên');
    @endphp
    <!-- Header -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 30px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Chỉnh sửa nhân viên</h1>
            <p style="color: #6b7280;">{{ $employee->Ten }} - {{ $employee->Ma ?? 'Chưa có mã' }}</p>
        </div>
        <a href="{{ route('nhan-vien.info', $employee->id) }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Quay lại
        </a>
    </div>

    <form id="editEmployeeForm" action="{{ route('nhan-vien.cap-nhat', $employee->id) }}" method="POST" enctype="multipart/form-data">
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

            <div class="form-row" style="grid-template-columns: 140px 1fr; align-items: start; gap: 30px;">
                <!-- Avatar column -->
                <div class="form-group" style="text-align: center;">
                    <label>Ảnh đại diện</label>
                    <div style="margin-top: 10px; position: relative; display: inline-block;">
                        <img id="avatarPreview" src="{{ $employee->AnhDaiDien ? asset($employee->AnhDaiDien) : 'https://ui-avatars.com/api/?name='.urlencode($employee->Ten).'&background=f0fdf4&color=0BAA4B&size=128' }}" 
                             alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #0BAA4B; padding: 3px; background: white;">
                        <label for="avatarInput" style="position: absolute; bottom: 5px; right: 5px; background: #0BAA4B; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.3); transition: all 0.2s; border: 2px solid white;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Thay đổi ảnh đại diện">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="initCropper(this)">
                        <input type="hidden" name="cropped_avatar" id="croppedAvatarInput">
                    </div>
                    <p class="help-text" style="font-size: 11px; margin-top: 8px;">JPEG, PNG, JPG (Max 2MB)</p>
                </div>

                <!-- Info column -->
                <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="Ten" value="{{ $employee->Ten }}" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="text" name="NgaySinh" class="datepicker" 
                            value="{{ $employee->NgaySinh ? \Carbon\Carbon::parse($employee->NgaySinh)->format('d-m-Y') : '' }}" 
                            placeholder="Chọn ngày sinh">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Giới tính <span class="required">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="male" name="GioiTinh" value="1" {{ $employee->GioiTinh == 1 ? 'checked' : '' }} required>
                            <label for="male">Nam</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="female" name="GioiTinh" value="0" {{ $employee->GioiTinh == 0 ? 'checked' : '' }} required>
                            <label for="female">Nữ</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dân tộc</label>
                    <input type="text" name="DanToc" value="{{ $employee->DanToc }}" placeholder="Kinh">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tôn giáo</label>
                    <input type="text" name="TonGiao" value="{{ $employee->TonGiao }}" placeholder="Không">
                </div>

                <div class="form-group">
                    <label>Quốc tịch</label>
                    <input type="text" name="QuocTich" value="{{ $employee->QuocTich }}" placeholder="Việt Nam">
                </div>

                <div class="form-group">
                    <label>Trạng thái nhân viên</label>
                    <select name="TrangThai" id="TrangThai" class="select2">
                        <option value="dang_lam" {{ ($employee->TrangThai ?? 'dang_lam') == 'dang_lam' ? 'selected' : '' }}>Làm tại công ty</option>
                        <option value="nghi_viec" {{ ($employee->TrangThai ?? 'dang_lam') == 'nghi_viec' ? 'selected' : '' }}>Nghỉ làm</option>
                        <option value="nghi_thai_san" {{ ($employee->TrangThai ?? 'dang_lam') == 'nghi_thai_san' ? 'selected' : '' }}>Nghỉ thai sản</option>
                    </select>
                    <input type="hidden" name="sync_account_status" id="syncAccountStatus" value="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tình trạng hôn nhân</label>
                    <select name="TinhTrangHonNhan" class="select2">
                        <option value="0" {{ $employee->TinhTrangHonNhan == '0' ? 'selected' : '' }}>Độc thân</option>
                        <option value="1" {{ $employee->TinhTrangHonNhan == '1' ? 'selected' : '' }}>Đã kết hôn</option>
                        <option value="2" {{ $employee->TinhTrangHonNhan == '2' ? 'selected' : '' }}>Ly hôn</option>
                        <option value="3" {{ $employee->TinhTrangHonNhan == '3' ? 'selected' : '' }}>Góa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Địa chỉ thường trú</label>
                    <input type="text" name="DiaChi" value="{{ $employee->DiaChi }}" placeholder="Số nhà, đường, phường, quận, thành phố">
                </div>
            </div>

            <div class="form-group">
                <label>Quê quán</label>
                <input type="text" name="QueQuan" value="{{ $employee->QueQuan }}" placeholder="Xã, huyện, tỉnh">
            </div>
        </div>

        <!-- Giấy tờ tùy thân -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                Giấy tờ tùy thân
            </h2>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Số CCCD</label>
                    <input type="text" name="SoCCCD" value="{{ $employee->SoCCCD }}" placeholder="001234567890">
                </div>

                <div class="form-group">
                    <label>Nơi cấp</label>
                    <input type="text" name="NoiCap" value="{{ $employee->NoiCap }}" placeholder="Cục Cảnh sát ĐKQL cư trú và DLQG về dân cư">
                </div>

                <div class="form-group">
                    <label>Ngày cấp</label>
                    <input type="text" name="NgayCap" class="datepicker" 
                        value="{{ $employee->NgayCap ? \Carbon\Carbon::parse($employee->NgayCap)->format('d-m-Y') : '' }}" 
                        placeholder="Chọn ngày cấp">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ảnh CCCD mặt trước</label>
                    <input type="file" name="anh_cccd" id="anh_cccd" accept="image/*" onchange="previewImages(this, 'cccd-front-preview', 'cccd-front-existing')">
                    <div class="help-text">Chọn ảnh mới để thay thế mặt trước (nếu muốn).</div>
                    <div id="cccd-front-preview" class="preview-container"></div>
                    @if($employee->anh_cccd && count($employee->anh_cccd) > 0)
                        <div id="cccd-front-existing" style="margin-top: 10px;">
                            <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Mặt trước hiện tại:</p>
                            <img src="{{ asset($employee->anh_cccd[0]) }}" style="width: 100px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>Ảnh CCCD mặt sau</label>
                    <input type="file" name="anh_cccd_sau" id="anh_cccd_sau" accept="image/*" onchange="previewImages(this, 'cccd-back-preview', 'cccd-back-existing')">
                    <div class="help-text">Chọn ảnh mới để thay thế mặt sau (nếu muốn).</div>
                    <div id="cccd-back-preview" class="preview-container"></div>
                    @if($employee->anh_cccd_sau)
                        <div id="cccd-back-existing" style="margin-top: 10px;">
                            <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Mặt sau hiện tại:</p>
                            <img src="{{ asset($employee->anh_cccd_sau) }}" style="width: 100px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Thông tin liên hệ
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="SoDienThoai" value="{{ $employee->SoDienThoai }}" placeholder="0912345678">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="Email" value="{{ $employee->Email }}" placeholder="example@email.com">
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
                Thông tin ngân hàng
            </h2>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Tên ngân hàng</label>
                    <select name="TenNganHang" class="select2">
                        <option value="">-- Chọn ngân hàng --</option>
                        @php
                            $banks = [
                                'Vietcombank' => 'Vietcombank - Ngân hàng TMCP Ngoại thương Việt Nam',
                                'VietinBank' => 'VietinBank - Ngân hàng TMCP Công thương Việt Nam',
                                'BIDV' => 'BIDV - Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
                                'Agribank' => 'Agribank - Ngân hàng Nông nghiệp và Phát triển Nông thôn',
                                'Techcombank' => 'Techcombank - Ngân hàng TMCP Kỹ thương Việt Nam',
                                'ACB' => 'ACB - Ngân hàng TMCP Á Châu',
                                'MB Bank' => 'MB Bank - Ngân hàng TMCP Quân đội',
                                'VPBank' => 'VPBank - Ngân hàng TMCP Việt Nam Thịnh Vượng',
                                'TPBank' => 'TPBank - Ngân hàng TMCP Tiên Phong',
                                'Sacombank' => 'Sacombank - Ngân hàng TMCP Sài Gòn Thương Tín'
                            ];
                        @endphp
                        @foreach($banks as $value => $label)
                            <option value="{{ $value }}" {{ $employee->TenNganHang == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Số tài khoản</label>
                    <input type="text" name="SoTaiKhoan" value="{{ $employee->SoTaiKhoan }}" placeholder="1234567890">
                </div>

                <div class="form-group">
                    <label>Chi nhánh</label>
                    <input type="text" name="ChiNhanhNganHang" value="{{ $employee->ChiNhanhNganHang }}" placeholder="Chi nhánh Hà Nội">
                </div>
            </div>
        </div>

        <!-- Bảo hiểm -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Bảo hiểm
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Số BHXH</label>
                    <input type="text" name="BHXH" value="{{ $employee->BHXH }}" placeholder="1234567890">
                </div>

                <div class="form-group">
                    <label>Nơi cấp BHXH</label>
                    <input type="text" name="NoiCapBHXH" value="{{ $employee->NoiCapBHXH }}" placeholder="BHXH Hà Nội">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ảnh BHXH (Nhiều ảnh)</label>
                    <input type="file" name="anh_bhxh[]" id="anh_bhxh" multiple accept="image/*" onchange="previewImages(this, 'bhxh-preview', 'bhxh-existing')">
                    <div class="help-text">Chọn ảnh mới để thay thế ảnh cũ (nếu muốn).</div>
                    <div id="bhxh-preview" class="preview-container"></div>
                    @if($employee->anh_bhxh && count($employee->anh_bhxh) > 0)
                        <div id="bhxh-existing" style="margin-top: 10px;">
                            <p style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Ảnh hiện tại:</p>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @foreach($employee->anh_bhxh as $path)
                                    <img src="{{ asset($path) }}" style="width: 100px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Số BHYT</label>
                    <input type="text" name="BHYT" value="{{ $employee->BHYT }}" placeholder="DN1234567890123">
                </div>

                <div class="form-group">
                    <label>Nơi cấp BHYT</label>
                    <input type="text" name="NoiCapBHYT" value="{{ $employee->NoiCapBHYT }}" placeholder="BHXH Hà Nội">
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
                    <label>Loại nhân viên</label>
                    <select name="LoaiNhanVien" class="select2" {{ $isRestricted ? 'disabled' : '' }}>
                        <option value="">-- Chọn loại nhân viên --</option>
                        <option value="1" {{ $employee->ttCongViec && $employee->ttCongViec->LoaiNhanVien == 1 ? 'selected' : '' }}>Văn phòng</option>
                        <option value="0" {{ $employee->ttCongViec && $employee->ttCongViec->LoaiNhanVien == 0 ? 'selected' : '' }}>Công nhân</option>
                    </select>
                    @if($isRestricted)
                        <input type="hidden" name="LoaiNhanVien" value="{{ $employee->ttCongViec->LoaiNhanVien ?? '' }}">
                    @endif
                </div>


            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Phòng ban</label>
                    <select name="PhongBanId" id="phongBanSelect" class="select2" {{ $isRestricted ? 'disabled' : '' }}>
                        <option value="">-- Chọn phòng ban --</option>
                        @foreach($phongBans as $phongBan)
                            <option value="{{ $phongBan->id }}"
                                {{ $employee->ttCongViec && $employee->ttCongViec->PhongBanId == $phongBan->id ? 'selected' : '' }}>
                                {{ $phongBan->Ten }}
                            </option>
                        @endforeach
                    </select>
                    @if($isRestricted)
                        <input type="hidden" name="PhongBanId" value="{{ $employee->ttCongViec->PhongBanId ?? '' }}">
                    @endif
                </div>

                <div class="form-group">
                    <label>Chức vụ</label>
                    <select name="ChucVuId" class="select2" {{ $isRestricted ? 'disabled' : '' }}>
                        <option value="">-- Chọn chức vụ --</option>
                        @foreach($chucVus as $chucVu)
                            <option value="{{ $chucVu->id }}"
                                {{ $employee->ttCongViec && $employee->ttCongViec->ChucVuId == $chucVu->id ? 'selected' : '' }}>
                                {{ $chucVu->Ten }}
                            </option>
                        @endforeach
                    </select>
                    @if($isRestricted)
                        <input type="hidden" name="ChucVuId" value="{{ $employee->ttCongViec->ChucVuId ?? '' }}">
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ngày tuyển dụng</label>
                    <input type="text" name="NgayTuyenDung" class="datepicker" 
                        value="{{ $employee->ttCongViec && $employee->ttCongViec->NgayTuyenDung ? \Carbon\Carbon::parse($employee->ttCongViec->NgayTuyenDung)->format('d-m-Y') : '' }}" 
                        placeholder="Chọn ngày tuyển dụng" {{ $isRestricted ? 'disabled' : '' }}>
                    @if($isRestricted)
                        <input type="hidden" name="NgayTuyenDung" value="{{ $employee->ttCongViec && $employee->ttCongViec->NgayTuyenDung ? \Carbon\Carbon::parse($employee->ttCongViec->NgayTuyenDung)->format('d-m-Y') : '' }}">
                    @endif
                </div>

                <div class="form-group">
                    <label>Ngày vào biên chế</label>
                    <input type="text" name="NgayVaoBienChe" class="datepicker" 
                        value="{{ $employee->ttCongViec && $employee->ttCongViec->NgayVaoBienChe ? \Carbon\Carbon::parse($employee->ttCongViec->NgayVaoBienChe)->format('d-m-Y') : '' }}" 
                        placeholder="Chọn ngày vào biên chế" {{ $isRestricted ? 'disabled' : '' }}>
                    @if($isRestricted)
                        <input type="hidden" name="NgayVaoBienChe" value="{{ $employee->ttCongViec && $employee->ttCongViec->NgayVaoBienChe ? \Carbon\Carbon::parse($employee->ttCongViec->NgayVaoBienChe)->format('d-m-Y') : '' }}">
                    @endif
                </div>
            </div>
        </div>

        <!-- Trình độ học vấn & Chuyên môn -->
        <div class="form-section">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Trình độ học vấn & Chuyên môn
            </h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Trình độ học vấn</label>
                    <select name="TrinhDoHocVan" class="select2">
                        <option value="">-- Chọn trình độ --</option>
                        @php
                            $levels = ['Trung học cơ sở', 'Trung học phổ thông', 'Trung cấp', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Tiến sĩ'];
                        @endphp
                        @foreach($levels as $level)
                            <option value="{{ $level }}" {{ ($employee->ttCongViec->TrinhDoHocVan ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Chuyên ngành</label>
                    <input type="text" name="ChuyenNganh" 
                        value="{{ $employee->ttCongViec->ChuyenNganh ?? '' }}" 
                        placeholder="Công nghệ thông tin">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Trình độ chuyên môn</label>
                    <input type="text" name="TrinhDoChuyenMon" 
                        value="{{ $employee->ttCongViec->TrinhDoChuyenMon ?? '' }}" 
                        placeholder="Kỹ sư">
                </div>

                <div class="form-group">
                    <label>Ngoại ngữ</label>
                    <input type="text" name="NgoaiNgu" 
                        value="{{ $employee->ttCongViec->NgoaiNgu ?? '' }}" 
                        placeholder="Tiếng Anh">
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
                Ghi chú
            </h2>

            <div class="form-group">
                <label>Ghi chú thêm</label>
                <textarea name="Note" placeholder="Nhập ghi chú nếu có...">{{ $employee->Note }}</textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-section">
            <div class="form-actions">
                <a href="{{ route('nhan-vien.info', $employee->id) }}" class="btn btn-secondary">
                    Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Lưu thay đổi
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
                document.getElementById('avatarInput').value = '';
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
                    document.getElementById('avatarPreview').src = croppedDataUrl;
                    document.getElementById('croppedAvatarInput').value = croppedDataUrl;
                    cropperModal.hide();
                }
            });

            // Initialize Flatpickr for date fields
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Chọn một mục',
                    allowClear: true
                });

                flatpickr('.datepicker', {
                    dateFormat: 'd-m-Y',
                    locale: 'vn',
                    allowInput: true
                });

                // Cascading dropdown removed as Unit is removed
            });

            // Form submission
            document.getElementById('editEmployeeForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const statusField = document.getElementById('TrangThai');
                const initialStatus = parseInt("{{ $employee->TrangThai ?? 1 }}");
                const currentStatus = parseInt(statusField.value);

                // Group 1 & 2 together as "Working", 0 as "Off"
                const wasWorking = (initialStatus === 1 || initialStatus === 2);
                const isWorking = (currentStatus === 1 || currentStatus === 2);

                if (wasWorking !== isWorking) {
                    const actionText = currentStatus === 0 ? 'KHÓA' : 'MỞ KHÓA';
                    const result = await Swal.fire({
                        title: 'Đổi trạng thái nhân viên',
                        text: `Bạn đã đổi trạng thái nhân viên. Bạn có muốn đồng thời ${actionText} tài khoản liên kết của nhân viên này luôn không?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0BAA4B',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, thực hiện luôn',
                        cancelButtonText: 'Không, chỉ đổi trạng thái nhân viên'
                    });

                    if (result.isConfirmed) {
                        document.getElementById('syncAccountStatus').value = "1";
                    } else {
                        document.getElementById('syncAccountStatus').value = "0";
                    }
                }

                const formData = new FormData(this);

                // Convert date formats from dd-mm-yyyy or dd/mm/yyyy to yyyy-mm-dd for MySQL
                const dateFields = ['NgaySinh', 'NgayCap', 'NgayTuyenDung', 'NgayVaoBienChe'];
                dateFields.forEach(fieldName => {
                    const dateValue = formData.get(fieldName);
                    if (dateValue) {
                        const separator = dateValue.includes('-') ? '-' : (dateValue.includes('/') ? '/' : null);
                        if (separator) {
                            const parts = dateValue.split(separator);
                            if (parts.length === 3 && parts[0].length === 2 && parts[2].length === 4) {
                                const convertedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                formData.set(fieldName, convertedDate);
                            }
                        }
                    }
                });

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Đang lưu...';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message,
                            confirmButtonColor: '#0BAA4B'
                        }).then(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: data.message,
                            confirmButtonColor: '#0BAA4B'
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi cập nhật thông tin',
                        confirmButtonColor: '#0BAA4B'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });

            function previewImages(input, containerId, existingContainerId) {
                const container = document.getElementById(containerId);
                const existingContainer = document.getElementById(existingContainerId);
                container.innerHTML = '';
                
                if (input.files && input.files.length > 0) {
                    if (existingContainer) existingContainer.style.display = 'none';
                    
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
                } else {
                    if (existingContainer) existingContainer.style.display = 'block';
                }
            }
        </script>
    @endpush
@endsection
