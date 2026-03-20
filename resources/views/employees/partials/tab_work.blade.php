<div class="tab-content" id="tab-work">
    <div class="detail-section">
        <h2><i class="bi bi-card-checklist"></i> Thông tin công việc hiện tại</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Loại nhân viên</div>
                <div class="detail-value">
                    @if($employee->ttCongViec && $employee->ttCongViec->LoaiNhanVien !== null)
                        @if($employee->ttCongViec->LoaiNhanVien == 1)
                            <span class="badge badge-info">Văn phòng</span>
                        @else
                            <span class="badge badge-warning">Công nhân</span>
                        @endif
                    @else
                        Chưa có
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phòng ban</div>
                <div class="detail-value">{{ $employee->ttCongViec->phongBan->Ten ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Chức vụ</div>
                <div class="detail-value">{{ $employee->ttCongViec->chucVu->Ten ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ngày tuyển dụng</div>
                <div class="detail-value">
                    {{ $employee->ttCongViec && $employee->ttCongViec->NgayTuyenDung ? \Carbon\Carbon::parse($employee->ttCongViec->NgayTuyenDung)->format('d/m/Y') : 'Chưa có' }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ngày vào biên chế</div>
                <div class="detail-value">
                    {{ $employee->ttCongViec && $employee->ttCongViec->NgayVaoBienChe ? \Carbon\Carbon::parse($employee->ttCongViec->NgayVaoBienChe)->format('d/m/Y') : 'Chưa có' }}
                </div>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2><i class="bi bi-mortarboard"></i> Trình độ học vấn & Chuyên môn</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Trình độ học vấn</div>
                <div class="detail-value">{{ $employee->ttCongViec->TrinhDoHocVan ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Chuyên ngành</div>
                <div class="detail-value">{{ $employee->ttCongViec->ChuyenNganh ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Trình độ chuyên môn</div>
                <div class="detail-value">{{ $employee->ttCongViec->TrinhDoChuyenMon ?? 'Chưa có' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ngoại ngữ</div>
                <div class="detail-value">{{ $employee->ttCongViec->NgoaiNgu ?? 'Chưa có' }}</div>
            </div>
        </div>
    </div>
</div>
