@extends('layouts.app')

@section('title', 'Tính lương thủ công - ' . $nv->Ten)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Tính lương thủ công</h1>
        <p>Điều chỉnh các khoản thu nhập và khấu trừ cho <strong>{{ $nv->Ten }}</strong> — Tháng {{ $thang }}/{{ $nam }}</p>
    </div>
    <a href="{{ route('salary.index', ['thang' => $thang, 'nam' => $nam]) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<form action="{{ route('salary.update-manual', $luong->id) }}" method="POST" id="manualSalaryForm">
    @csrf
    <div class="row">
        {{-- THÔNG TIN NHÂN VIÊN & TỔNG HỢP --}}
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $nv->HinhAnh ? asset('storage/' . $nv->HinhAnh) : 'https://ui-avatars.com/api/?name='.urlencode($nv->Ten).'&background=0BAA4B&color=fff&size=128' }}" 
                             class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $nv->Ten }}</h4>
                    <p class="text-muted mb-3">{{ $nv->ttCongViec?->chucVu?->Ten ?? '—' }} ({{ $nv->Ma }})</p>
                    
                    <hr>

                    <div class="net-pay-card p-3 rounded" style="background: linear-gradient(135deg, #0BAA4B, #088c3d); color: white;">
                        <div class="small opacity-75 mb-1">THỰC LĨNH CUỐI CÙNG</div>
                        <div class="h2 fw-bold mb-0" id="displayNetPay">0 đ</div>
                    </div>

                    <div class="mt-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Tổng thu nhập:</span>
                            <span class="fw-bold text-success" id="displayTotalEarnings">0 đ</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Tổng khấu trừ:</span>
                            <span class="fw-bold text-danger" id="displayTotalDeductions">0 đ</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-save"></i> LƯU THAY ĐỔI
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- CÁC KHOẢN CHI TIẾT --}}
        <div class="col-lg-8">
            {{-- A. THU NHẬP --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-plus-circle me-2"></i>A. CÁC KHOẢN THU NHẬP</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Lương cơ bản --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lương cơ bản (Theo công)</label>
                            <div class="input-group mb-2">
                                <input type="text" name="LuongCoBan" id="inputLuongCoBan" class="form-control fw-bold" 
                                       value="{{ (float)$luong->LuongCoBan }}" required>
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-info">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Hợp đồng & Chấm công</small>
                                <small class="d-block text-muted"><strong>Công thức:</strong> (Lương HĐ / {{ $auto['ngay_cong_chuan'] ?? 26 }}) * {{ $auto['ngay_cong_thuc_te'] ?? 0 }}</small>
                                <small class="text-info d-block mt-1">Hệ thống tính: {{ number_format($auto['luong_ngay_cong'] ?? 0, 0, ',', '.') }} đ</small>
                            </div>
                        </div>

                        {{-- Phụ cấp --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tổng phụ cấp</label>
                            <div class="input-group mb-2">
                                <input type="text" name="PhuCap" id="inputPhuCap" class="form-control fw-bold" 
                                       value="{{ (float)$luong->PhuCap }}" required>
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-info">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Hợp đồng & Phụ lục</small>
                                <small class="d-block text-muted"><strong>Chi tiết:</strong> Ăn trưa, Điện thoại, Đi lại...</small>
                                <small class="text-info d-block mt-1">Hệ thống tính: {{ number_format($auto['tong_phu_cap'] ?? 0, 0, ',', '.') }} đ</small>
                            </div>
                        </div>

                        {{-- Khen thưởng --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thưởng / Thu nhập khác</label>
                            <div class="input-group mb-2">
                                <input type="text" name="KhenThuong" id="inputKhenThuong" class="form-control fw-bold text-success" 
                                       value="{{ (float)$luong->KhenThuong }}">
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div id="wrapperLyDoKhenThuong" class="mt-2" style="display: none;">
                                <label class="text-xs fw-bold text-success mb-1">LÝ DO KHEN THƯỞNG <span class="text-danger">*</span></label>
                                <input type="text" name="LyDoKhenThuong" id="inputLyDoKhenThuong" class="form-control form-control-sm border-success" 
                                       placeholder="VD: Thưởng hoàn thành dự án A, Thưởng chuyên cần...">
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-success mt-2">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Nhập tay / Quyết định thưởng</small>
                                <small class="d-block text-muted">Tiền thưởng dự án, lễ tết hoặc hoàn thành KPIs...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- B. KHẤU TRỪ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-danger fw-bold"><i class="bi bi-dash-circle me-2"></i>B. CÁC KHOẢN KHẤU TRỪ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Bảo hiểm --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Khấu trừ Bảo hiểm (10.5%)</label>
                            <div class="input-group mb-2">
                                <input type="text" name="KhauTruBaoHiem" id="inputKhauTruBaoHiem" class="form-control fw-bold text-danger" 
                                       value="{{ (float)$luong->KhauTruBaoHiem }}" required>
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-info">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Chính sách Bảo hiểm</small>
                                <small class="d-block text-muted"><strong>Công thức:</strong> Lương đóng BH * (8% BHXH + 1.5% BHYT + 1% BHTN)</small>
                                <small class="text-info d-block mt-1">Hệ thống tính: {{ number_format($auto['tong_khau_tru_bh'] ?? 0, 0, ',', '.') }} đ</small>
                            </div>
                        </div>

                        {{-- Thuế TNCN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thuế thu nhập cá nhân</label>
                            <div class="input-group mb-2">
                                <input type="text" name="ThueTNCN" id="inputThueTNCN" class="form-control fw-bold text-danger" 
                                       value="{{ (float)$luong->ThueTNCN }}" required>
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-info">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Biểu thuế lũy tiến</small>
                                <small class="d-block text-muted">Dựa trên thu nhập và {{ $luong->SoNguoiPhuThuoc }} người phụ thuộc</small>
                                <small class="text-info d-block mt-1">Hệ thống tính: {{ number_format($auto['thue_tncn'] ?? 0, 0, ',', '.') }} đ</small>
                            </div>
                        </div>

                        {{-- Tạm ứng --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tạm ứng lương</label>
                            <div class="input-group mb-2">
                                <input type="text" name="TamUng" id="inputTamUng" class="form-control fw-bold text-danger" 
                                       value="{{ (float)$luong->TamUng }}" required>
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-info">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Phiếu tạm ứng trong tháng</small>
                                <small class="text-info d-block mt-1">Hệ thống tính: {{ number_format($auto['tam_ung'] ?? 0, 0, ',', '.') }} đ</small>
                            </div>
                        </div>

                        {{-- Kỷ luật --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kỷ luật / Phạt</label>
                            <div class="input-group mb-3">
                                <input type="text" name="KyLuat" id="inputKyLuat" class="form-control fw-bold text-danger" 
                                       value="{{ (float)$luong->KyLuat }}">
                                <span class="input-group-text text-muted small">VNĐ</span>
                            </div>
                            <div id="wrapperLyDoKyLuat" class="mt-n2 mb-3" style="display: none;">
                                <label class="text-xs fw-bold text-danger mb-1">LÝ DO KỶ LUẬT <span class="text-danger">*</span></label>
                                <input type="text" name="LyDoKyLuat" id="inputLyDoKyLuat" class="form-control form-control-sm border-danger" 
                                       placeholder="VD: Vi phạm nội quy công ty, Làm hỏng tài sản...">
                            </div>
                            <div class="source-info p-2 rounded bg-light border-start border-4 border-danger">
                                <small class="d-block"><strong>Nguồn gốc:</strong> Nhập tay / Vi phạm nội quy</small>
                                <small class="d-block text-muted">Đi muộn, làm hỏng thiết bị, hoặc các khoản trừ khác...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GHI CHÚ --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label fw-bold">Ghi chú điều chỉnh</label>
                    <textarea name="GhiChu" class="form-control" rows="3" placeholder="Lý do điều chỉnh hoặc ghi chú thêm...">{{ $luong->GhiChu }}</textarea>
                </div>
            </div>

            {{-- LỊCH SỬ ĐIỀU CHỈNH --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-clock-history me-2"></i>LỊCH SỬ ĐIỀU CHỈNH</h5>
                    <span class="badge bg-light text-muted fw-normal">{{ count($logs) }} lượt điều chỉnh</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($logs as $log)
                            @php
                                $oldData = json_decode($log->DuLieuCu, true) ?? [];
                                $newData = json_decode($log->DuLieuMoi, true) ?? [];
                                $labels = [
                                    'LuongCoBan' => 'Lương cơ bản',
                                    'PhuCap' => 'Phụ cấp',
                                    'KhauTruBaoHiem' => 'Bảo hiểm',
                                    'ThueTNCN' => 'Thuế TNCN',
                                    'TamUng' => 'Tạm ứng',
                                    'KhenThuong' => 'Thưởng',
                                    'KyLuat' => 'Kỷ luật',
                                ];
                            @endphp
                            <div class="list-group-item py-3">
                                <div class="row align-items-start">
                                    <div class="col-md-3 border-end">
                                        <div class="fw-bold text-dark">{{ $log->TenNguoiDung ?? 'Hệ thống' }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($log->CreatedAt)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="col-md-5 px-4">
                                        <div class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">CÁC THAY ĐỔI</div>
                                        @foreach($newData as $field => $newVal)
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="text-secondary small me-2" style="width: 100px;">{{ $labels[$field] ?? $field }}:</span>
                                                <span class="text-muted small text-decoration-line-through">{{ number_format($oldData[$field] ?? 0, 0, ',', '.') }}</span>
                                                <i class="bi bi-chevron-right mx-2 text-primary small"></i>
                                                <span class="fw-bold text-primary">{{ number_format($newVal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-uppercase fw-bold text-muted mb-2" style="letter-spacing: 0.5px; font-size: 0.7rem;">CĂN CỨ / GHI CHÚ</div>
                                        <div class="text-muted italic small bg-light p-2 rounded border-start border-3 border-secondary" style="font-style: italic;">
                                            {{ $log->MoTa }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-info-circle display-6 text-muted mb-3 d-block"></i>
                                <div class="text-muted">Chưa có lịch sử điều chỉnh nào cho phiếu lương này.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .source-info {
        font-size: 0.75rem;
    }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputIds = [
            'inputLuongCoBan', 'inputPhuCap', 'inputKhenThuong',
            'inputKhauTruBaoHiem', 'inputThueTNCN', 'inputTamUng', 'inputKyLuat'
        ];

        function formatCurrency(val) {
            return new Intl.NumberFormat('vi-VN').format(val) + ' đ';
        }

        // Helper to format number with dots
        function formatNumberWithDots(n) {
            if (!n) return '';
            return n.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Helper to get raw number
        function getRawNumber(val) {
            if (typeof val !== 'string') val = val.toString();
            return parseFloat(val.replace(/\./g, '')) || 0;
        }

        function calculatePayload() {
            let luongCoBan = getRawNumber(document.getElementById('inputLuongCoBan').value);
            let phuCap = getRawNumber(document.getElementById('inputPhuCap').value);
            let khenThuong = getRawNumber(document.getElementById('inputKhenThuong').value);
            
            let khauTruBH = getRawNumber(document.getElementById('inputKhauTruBaoHiem').value);
            let thueTNCN = getRawNumber(document.getElementById('inputThueTNCN').value);
            let tamUng = getRawNumber(document.getElementById('inputTamUng').value);
            let kyLuat = getRawNumber(document.getElementById('inputKyLuat').value);

            let totalEarnings = luongCoBan + phuCap + khenThuong;
            let totalDeductions = khauTruBH + thueTNCN + tamUng + kyLuat;
            let netPay = Math.max(0, totalEarnings - totalDeductions);

            document.getElementById('displayTotalEarnings').innerText = formatCurrency(totalEarnings);
            document.getElementById('displayTotalDeductions').innerText = formatCurrency(totalDeductions);
            document.getElementById('displayNetPay').innerText = formatCurrency(netPay);
        }

        inputIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // Initialize formatting
                el.value = formatNumberWithDots(el.value);

                el.addEventListener('input', function(e) {
                    // Save cursor position
                    let cursorPosition = this.selectionStart;
                    let oldLength = this.value.length;

                    // Format
                    this.value = formatNumberWithDots(this.value);

                    // Adjust cursor position
                    let newLength = this.value.length;
                    cursorPosition = cursorPosition + (newLength - oldLength);
                    this.setSelectionRange(cursorPosition, cursorPosition);

                    calculatePayload();

                    // Specific logic for rewards/penalties reasons
                    if (id === 'inputKhenThuong' || id === 'inputKyLuat') {
                        const amount = getRawNumber(this.value);
                        const reasonWrapperId = id === 'inputKhenThuong' ? 'wrapperLyDoKhenThuong' : 'wrapperLyDoKyLuat';
                        const reasonInputId = id === 'inputKhenThuong' ? 'inputLyDoKhenThuong' : 'inputLyDoKyLuat';
                        const wrapper = document.getElementById(reasonWrapperId);
                        const reasonInput = document.getElementById(reasonInputId);

                        if (amount > 0) {
                            wrapper.style.display = 'block';
                            reasonInput.setAttribute('required', 'required');
                        } else {
                            wrapper.style.display = 'none';
                            reasonInput.removeAttribute('required');
                            reasonInput.value = '';
                        }
                    }
                });
            }
        });

        // Trigger initial visibility check for reasons if values > 0
        ['inputKhenThuong', 'inputKyLuat'].forEach(id => {
            const el = document.getElementById(id);
            if (el && getRawNumber(el.value) > 0) {
                el.dispatchEvent(new Event('input'));
            }
        });

        // Strip dots before submit
        document.getElementById('manualSalaryForm').addEventListener('submit', function() {
            inputIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = el.value.replace(/\./g, '');
                }
            });
        });

        // First calculation
        calculatePayload();
    });
</script>
@endpush
@endsection
