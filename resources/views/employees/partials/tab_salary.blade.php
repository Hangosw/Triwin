<div class="tab-content" id="tab-salary">
    <div class="detail-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: var(--text-primary);">
                <i class="bi bi-file-earmark-text me-2"></i>Lịch sử phiếu lương
            </h2>
            <div class="d-flex gap-2">
                <select id="monthFilter" class="form-select form-select-sm" style="width: 130px; background-color: var(--bg-card); color: var(--text-primary); border-color: var(--border-color);">
                    <option value="">Tháng (Tất cả)</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">Tháng {{ $m }}</option>
                    @endfor
                </select>
                <select id="yearFilter" class="form-select form-select-sm" style="width: 120px; background-color: var(--bg-card); color: var(--text-primary); border-color: var(--border-color);">
                    <option value="">Năm (Tất cả)</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= 2022; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        @if($employee->luongs->isEmpty())
            <div class="py-5 text-center" style="background-color: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border-color);">
                <i class="bi bi-receipt mb-3 d-block" style="font-size: 48px; color: var(--text-secondary); opacity: 0.3;"></i>
                <div style="color: var(--text-secondary);">Chưa có dữ liệu phiếu lương cho nhân viên này.</div>
            </div>
        @else
            <div class="table-responsive premium-table">
                <table class="table table-hover align-middle mb-0" id="salarySlipsTable" style="background-color: var(--bg-card); color: var(--text-primary);">
                    <thead>
                        <tr style="background-color: rgba(0,0,0,0.02); border-bottom: 2px solid var(--border-color);">
                            <th class="py-3 px-3" style="width: 120px;">Kỳ lương</th>
                            <th class="py-3">Lương cơ bản</th>
                            <th class="py-3">Phụ cấp</th>
                            <th class="py-3">Thưởng</th>
                            <th class="py-3">Khấu trừ</th>
                            <th class="py-3">Tạm ứng</th>
                            <th class="py-3 fw-bold" style="color: var(--accent-color);">Thực nhận</th>
                            <th class="py-3 text-center">Trạng thái</th>
                            <th class="py-3 text-end">In phiếu</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: none;">
                        @foreach($employee->luongs as $l)
                            @php
                                $dt = \Carbon\Carbon::parse($l->ThoiGian);
                                // Tổng các loại khấu trừ
                                $tongKhauTru = (float)$l->KhauTruBaoHiem + (float)$l->ThueTNCN + (float)$l->KyLuat;
                            @endphp
                            <tr data-month="{{ $dt->month }}" data-year="{{ $dt->year }}" style="border-bottom: 1px solid var(--border-color);">
                                <td class="px-3">
                                    <div class="fw-bold fs-6">Tháng {{ $dt->month }}</div>
                                    <div class="text-muted small">Năm {{ $dt->year }}</div>
                                </td>
                                <td>{{ number_format($l->LuongCoBan, 0, ',', '.') }} đ</td>
                                <td>{{ number_format($l->PhuCap, 0, ',', '.') }} đ</td>
                                <td>
                                    @if($l->KhenThuong > 0)
                                        <span class="text-success fw-medium">+{{ number_format($l->KhenThuong, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tongKhauTru > 0)
                                        <span class="text-danger fw-medium">-{{ number_format($tongKhauTru, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($l->TamUng > 0)
                                        <span class="text-muted fw-medium">{{ number_format($l->TamUng, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="fw-bold" style="color: var(--accent-color); font-size: 1.05rem;">
                                    {{ number_format($l->Luong, 0, ',', '.') }} đ
                                </td>
                                <td class="text-center">
                                    @if($l->TrangThai == 1)
                                        <span class="badge badge-success" style="background-color: rgba(11, 170, 75, 0.15); color: #0BAA4B; border: 1px solid rgba(11, 170, 75, 0.2);">Đã thanh toán</span>
                                    @else
                                        <span class="badge badge-warning" style="background-color: rgba(255, 193, 7, 0.15); color: #856404; border: 1px solid rgba(255, 193, 7, 0.2);">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-secondary btn-sm btn-show-slip border-0 p-2"
                                        data-nv-id="{{ $employee->id }}"
                                        data-thang="{{ $dt->month }}"
                                        data-nam="{{ $dt->year }}"
                                        style="border-radius: 8px; color: var(--text-secondary);">
                                        <i class="bi bi-printer fs-5"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
