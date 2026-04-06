<div class="tab-content active" id="tab-basic">
    <div class="detail-section">
        <h2><i class="bi bi-person-bounding-box"></i> Thông tin cá nhân</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Mã nhân viên</div>
                <div class="detail-value">{{ $employee->Ma ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Họ và tên</div>
                <div class="detail-value">{{ $employee->Ten }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Số CCCD</div>
                <div class="detail-value">{{ $employee->SoCCCD ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nơi cấp CCCD</div>
                <div class="detail-value">{{ $employee->NoiCap ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ngày cấp</div>
                <div class="detail-value">
                    {{ $employee->NgayCap ? \Carbon\Carbon::parse($employee->NgayCap)->format('d/m/Y') : 'Chưa có' }}
                </div>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <div class="detail-label">Giấy tờ CCCD</div>
                @if($employee->anh_cccd && count($employee->anh_cccd) > 0)
                    <div style="display: flex; gap: 12px; margin-top: 8px; flex-wrap: wrap;">
                        @foreach($employee->anh_cccd as $path)
                            <a href="{{ asset($path) }}" target="_blank" class="document-image-link">
                                <img src="{{ asset($path) }}" alt="CCCD" style="width: 160px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s;">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="detail-value" style="color: #9ca3af; font-style: italic; font-size: 13px;">Chưa có ảnh CCCD</div>
                @endif
            </div>
            <div class="detail-item">
                <div class="detail-label">Ngày sinh</div>
                <div class="detail-value">
                    {{ $employee->NgaySinh ? \Carbon\Carbon::parse($employee->NgaySinh)->format('d/m/Y') : 'Chưa có' }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Giới tính</div>
                <div class="detail-value">{{ $employee->GioiTinh == 1 ? 'Nam' : 'Nữ' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Dân tộc</div>
                <div class="detail-value">{{ $employee->DanToc ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tôn giáo</div>
                <div class="detail-value">{{ $employee->TonGiao ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Quốc tịch</div>
                <div class="detail-value">{{ $employee->QuocTich ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tình trạng hôn nhân</div>
                <div class="detail-value">{{ $employee->TinhTrangHonNhan ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <div class="detail-label">Địa chỉ thường trú</div>
                <div class="detail-value">{{ $employee->DiaChi ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <div class="detail-label">Quê quán</div>
                <div class="detail-value">{{ $employee->QueQuan ?? 'Chưa có' }}</div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2><i class="bi bi-telephone-outbound"></i> Thông tin liên hệ</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Số điện thoại</div>
                <div class="detail-value">{{ $employee->SoDienThoai ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $employee->Email ?? 'Chưa có' }}</div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2><i class="bi bi-bank"></i> Thông tin ngân hàng</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Tên ngân hàng</div>
                <div class="detail-value">{{ $employee->TenNganHang ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Số tài khoản</div>
                <div class="detail-value">{{ $employee->SoTaiKhoan ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Chi nhánh</div>
                <div class="detail-value">{{ $employee->ChiNhanhNganHang ?? 'Chưa có' }}</div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2><i class="bi bi-shield-check"></i> Bảo hiểm</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Số BHXH</div>
                <div class="detail-value">{{ $employee->BHXH ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nơi cấp BHXH</div>
                <div class="detail-value">{{ $employee->NoiCapBHXH ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Số BHYT</div>
                <div class="detail-value">{{ $employee->BHYT ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nơi cấp BHYT</div>
                <div class="detail-value">{{ $employee->NoiCapBHYT ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <div class="detail-label">Giấy tờ BHXH</div>
                @if($employee->anh_bhxh && count($employee->anh_bhxh) > 0)
                    <div style="display: flex; gap: 12px; margin-top: 8px; flex-wrap: wrap;">
                        @foreach($employee->anh_bhxh as $path)
                            <a href="{{ asset($path) }}" target="_blank" class="document-image-link">
                                <img src="{{ asset($path) }}" alt="BHXH" style="width: 160px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s;">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="detail-value" style="color: #9ca3af; font-style: italic; font-size: 13px;">Chưa có ảnh BHXH</div>
                @endif
            </div>
        </div>
    </div>

    @if($employee->Note)
        <div class="detail-section">
            <h2><i class="bi bi-sticky"></i> Ghi chú</h2>
            <div class="detail-value">{{ $employee->Note }}</div>
        </div>
    @endif
</div>
