<div class="tab-content active" id="tab-basic">
    <div class="basic-info-grid">
        {{-- Left Column --}}
        <div class="basic-info-col">
            {{-- Personal Information Card with Profile Overview --}}
            <div class="info-card">
                <div class="profile-overview" style="display: flex; gap: 24px; align-items: center; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                    @php
                        $avatar = $employee->AnhDaiDien
                            ? asset($employee->AnhDaiDien)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($employee->Ten) . '&background=0BAA4B&color=fff&size=128';
                    @endphp
                    <div style="position: relative;">
                        <img src="{{ $avatar }}" alt="{{ $employee->Ten }}" class="profile-avatar"
                            style="width: 100px; height: 100px; border: 3px solid #f0fdf4; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($employee->Ten) }}&background=0BAA4B&color=fff&size=128'">
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                            <h2 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">{{ $employee->Ten }}</h2>
                            @php
                                $status = $employee->TrangThai ?? 'dang_lam';
                                if ($status === 'dang_lam') {
                                    $statusData = ['text' => 'Đang làm việc', 'class' => 'bg-success'];
                                } elseif ($status === 'nghi_thai_san') {
                                    $statusData = ['text' => 'Nghỉ thai sản', 'class' => 'bg-info'];
                                } else {
                                    $statusData = ['text' => 'Nghỉ làm', 'class' => 'bg-secondary'];
                                }
                            @endphp
                            <span class="badge {{ $statusData['class'] }}" style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                                {{ $statusData['text'] }}
                            </span>
                        </div>
                        <p style="color: #64748b; font-size: 14px; margin-bottom: 12px; font-weight: 500;">
                            <i class="bi bi-briefcase" style="margin-right: 4px;"></i>
                            {{ $employee->ttCongViec->chucVu->Ten ?? 'Chưa có chức vụ' }} | {{ $employee->ttCongViec->phongBan->Ten ?? 'Chưa có phòng ban' }}
                        </p>
                        <a href="{{ route('nhan-vien.suaView', $employee->id) }}" class="btn btn-light btn-sm"
                            style="border: 1px solid #e2e8f0; font-weight: 600; color: #0BAA4B; background: white; border-radius: 8px;">
                            <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>
                            Chỉnh sửa hồ sơ
                        </a>
                    </div>
                </div>

                <div class="info-card-header" style="border-bottom: none; margin-bottom: 16px;">
                    <i class="bi bi-person-bounding-box"></i>
                    <h3>Chi tiết cá nhân</h3>
                </div>
                <div class="info-card-grid">
                    <div class="info-card-item">
                        <div class="info-card-label">Mã nhân viên</div>
                        <div class="info-card-value text-primary-hr">{{ $employee->Ma ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Giới tính</div>
                        <div class="info-card-value">{{ $employee->GioiTinh == 1 ? 'Nam' : 'Nữ' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Ngày sinh</div>
                        <div class="info-card-value">
                            {{ $employee->NgaySinh ? \Carbon\Carbon::parse($employee->NgaySinh)->format('d/m/Y') : 'Chưa có' }}
                        </div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Hôn nhân</div>
                        <div class="info-card-value">{{ $employee->TinhTrangHonNhan ?? 'Độc thân' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Dân tộc</div>
                        <div class="info-card-value">{{ $employee->DanToc ?? 'Kinh' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Quốc tịch</div>
                        <div class="info-card-value">{{ $employee->QuocTich ?? 'Việt Nam' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Tôn giáo</div>
                        <div class="info-card-value">{{ $employee->TonGiao ?? 'Không' }}</div>
                    </div>
                </div>
            </div>

            {{-- CCCD Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-card-heading"></i>
                    <h3>Thông tin CCCD</h3>
                </div>
                <div class="info-card-grid">
                    <div class="info-card-item">
                        <div class="info-card-label">Số CCCD</div>
                        <div class="info-card-value">{{ $employee->SoCCCD ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Ngày cấp</div>
                        <div class="info-card-value">
                            {{ $employee->NgayCap ? \Carbon\Carbon::parse($employee->NgayCap)->format('d/m/Y') : 'Chưa có' }}
                        </div>
                    </div>
                    <div class="info-card-item full-width">
                        <div class="info-card-label">Nơi cấp</div>
                        <div class="info-card-value">{{ $employee->NoiCap ?? 'Chưa có' }}</div>
                    </div>
                    
                    <div class="info-card-item full-width" style="margin-top: 10px;">
                        <div class="info-card-label">Ảnh CCCD (Mặt trước & Mặt sau)</div>
                        <div style="display: flex; gap: 12px; margin-top: 8px; flex-wrap: wrap;">
                            @if($employee->anh_cccd && count($employee->anh_cccd) > 0)
                                <div style="text-align: center;">
                                    <div style="font-size: 10px; color: #94a3b8; margin-bottom: 4px;">Mặt trước</div>
                                    <a href="{{ asset($employee->anh_cccd[0]) }}" target="_blank" class="document-image-link">
                                        <img src="{{ asset($employee->anh_cccd[0]) }}" alt="CCCD Mặt trước" style="width: 140px; height: 88px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    </a>
                                </div>
                            @endif

                            @if($employee->anh_cccd_sau)
                                <div style="text-align: center;">
                                    <div style="font-size: 10px; color: #94a3b8; margin-bottom: 4px;">Mặt sau</div>
                                    <a href="{{ asset($employee->anh_cccd_sau) }}" target="_blank" class="document-image-link">
                                        <img src="{{ asset($employee->anh_cccd_sau) }}" alt="CCCD Mặt sau" style="width: 140px; height: 88px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    </a>
                                </div>
                            @endif

                            @if((!$employee->anh_cccd || count($employee->anh_cccd) == 0) && !$employee->anh_cccd_sau)
                                <div style="color: #94a3b8; font-style: italic; font-size: 13px; padding: 20px; background: #f8fafc; border-radius: 8px; width: 100%; text-align: center;">
                                    <i class="bi bi-image" style="font-size: 24px;"></i>
                                    <p style="margin-top: 5px;">Chưa có ảnh CCCD</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($employee->Note)
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-sticky"></i>
                        <h3>Ghi chú</h3>
                    </div>
                    <div class="info-card-value" style="font-weight: 500; line-height: 1.6;">{{ $employee->Note }}</div>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="basic-info-col">
            {{-- Contact Information Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-telephone-outbound" style="background: #fef2f2; color: #ef4444;"></i>
                    <h3 style="color: #ef4444;">Thông tin liên hệ</h3>
                </div>
                <div class="info-card-grid">
                    <div class="info-card-item">
                        <div class="info-card-label">Số điện thoại</div>
                        <div class="info-card-value">{{ $employee->SoDienThoai ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Email</div>
                        <div class="info-card-value">{{ $employee->Email ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item full-width">
                        <div class="info-card-label">Quê quán</div>
                        <div class="info-card-value">{{ $employee->QueQuan ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item full-width">
                        <div class="info-card-label">Địa chỉ thường trú</div>
                        <div class="info-card-value">{{ $employee->DiaChi ?? 'Chưa có' }}</div>
                    </div>
                </div>
            </div>

            {{-- Bank Information Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-bank" style="background: #eff6ff; color: #3b82f6;"></i>
                    <h3 style="color: #3b82f6;">Thông tin ngân hàng</h3>
                </div>
                <div class="info-card-grid">
                    <div class="info-card-item full-width">
                        <div class="info-card-label">Tên ngân hàng</div>
                        <div class="info-card-value">{{ $employee->TenNganHang ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Số tài khoản</div>
                        <div class="info-card-value">{{ $employee->SoTaiKhoan ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Chi nhánh</div>
                        <div class="info-card-value">{{ $employee->ChiNhanhNganHang ?? 'Chưa có' }}</div>
                    </div>
                </div>
            </div>

            {{-- Insurance Card --}}
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-shield-check" style="background: #eef2ff; color: #6366f1;"></i>
                    <h3 style="color: #6366f1;">Bảo hiểm</h3>
                </div>
                <div class="info-card-grid">
                    <div class="info-card-item">
                        <div class="info-card-label">Số BHXH</div>
                        <div class="info-card-value">{{ $employee->BHXH ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item">
                        <div class="info-card-label">Số BHYT</div>
                        <div class="info-card-value">{{ $employee->BHYT ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item full-width">
                        <div class="info-card-label">Nơi cấp BHXH</div>
                        <div class="info-card-value">{{ $employee->NoiCapBHXH ?? 'Chưa có' }}</div>
                    </div>
                    <div class="info-card-item full-width" style="margin-top: 10px;">
                        <div class="info-card-label">Giấy tờ BHXH</div>
                        <div style="display: flex; gap: 12px; margin-top: 8px; flex-wrap: wrap;">
                            @if($employee->anh_bhxh && count($employee->anh_bhxh) > 0)
                                @foreach($employee->anh_bhxh as $path)
                                    <a href="{{ asset($path) }}" target="_blank" class="document-image-link">
                                        <img src="{{ asset($path) }}" alt="BHXH" style="width: 140px; height: 88px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    </a>
                                @endforeach
                            @else
                                <div style="color: #94a3b8; font-style: italic; font-size: 13px; padding: 20px; background: #f8fafc; border-radius: 8px; width: 100%; text-align: center;">
                                    <p>Chưa có ảnh BHXH</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
