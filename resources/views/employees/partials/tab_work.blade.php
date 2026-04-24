<div class="tab-content" id="tab-work">
    <div class="detail-section">
        <h2><i class="bi bi-card-checklist"></i> {{ __('Thông tin công việc hiện tại') }}</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">{{ __('Loại nhân viên') }}</div>
                <div class="detail-value">
                    @if($employee->ttCongViec && $employee->ttCongViec->LoaiNhanVien !== null)
                        @if($employee->ttCongViec->LoaiNhanVien == 1)
                            <span class="badge badge-info">{{ __('Văn phòng') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('Công nhân') }}</span>
                        @endif
                    @else
                        {{ __('Chưa có') }}
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Phòng ban') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->phongBan->Ten ?? __('Chưa có') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Chức vụ') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->chucVu->Ten ?? __('Chưa có') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Ngày tuyển dụng') }}</div>
                <div class="detail-value">
                    {{ $employee->ttCongViec && $employee->ttCongViec->NgayTuyenDung ? \Carbon\Carbon::parse($employee->ttCongViec->NgayTuyenDung)->format('d/m/Y') : __('Chưa có') }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Ngày vào biên chế') }}</div>
                <div class="detail-value">
                    {{ $employee->ttCongViec && $employee->ttCongViec->NgayVaoBienChe ? \Carbon\Carbon::parse($employee->ttCongViec->NgayVaoBienChe)->format('d/m/Y') : __('Chưa có') }}
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2><i class="bi bi-mortarboard"></i> {{ __('Trình độ học vấn & Chuyên môn') }}</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">{{ __('Trình độ học vấn') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->TrinhDoHocVan ?? __('Chưa có') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Chuyên ngành') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->ChuyenNganh ?? __('Chưa có') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Trình độ chuyên môn') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->TrinhDoChuyenMon ?? __('Chưa có') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('Ngoại ngữ') }}</div>
                <div class="detail-value">{{ $employee->ttCongViec->NgoaiNgu ?? __('Chưa có') }}</div>
            </div>
        </div>
    </div>
</div>
