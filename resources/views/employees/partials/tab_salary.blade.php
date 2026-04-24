<div class="tab-content" id="tab-salary">
    <div class="detail-section">
        <div class="row align-items-center mb-4 g-3 p-3 rounded-3" style="background-color: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="col-12 col-md-6 col-lg-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(11, 170, 75, 0.1); color: #0BAA4B;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-1" style="color: var(--text-primary); font-size: 1.15rem; font-weight: 700;">{{ __('Lịch sử phiếu lương') }}</h2>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">{{ __('Quản lý và tra cứu phiếu lương hàng tháng') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-5">
                <div class="d-flex flex-column flex-sm-row gap-2 w-100">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <select id="monthFilter" class="form-select w-100" style="border-radius: 8px; border-color: var(--border-color); font-size: 0.9rem;" data-placeholder="{{ __('Tất cả các tháng') }}">
                            <option value=""></option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ app()->getLocale() === 'en' ? \Carbon\Carbon::create(null, $m, 1)->format('M') : __('Tháng') . ' ' . $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <select id="yearFilter" class="form-select w-100" style="border-radius: 8px; border-color: var(--border-color); font-size: 0.9rem;" data-placeholder="{{ __('Tất cả các năm') }}">
                            <option value=""></option>
                            @php $currentYear = date('Y'); @endphp
                            @for($y = $currentYear; $y >= 2022; $y--)
                                <option value="{{ $y }}">{{ __('Năm') }} {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($employee->luongs->isEmpty())
            <div class="py-5 text-center" style="background-color: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border-color);">
                <i class="bi bi-receipt mb-3 d-block" style="font-size: 48px; color: var(--text-secondary); opacity: 0.3;"></i>
                <div style="color: var(--text-secondary);">{{ __('Chưa có dữ liệu phiếu lương cho nhân viên này.') }}</div>
            </div>
        @else
            <div class="table-responsive premium-table">
                <table class="table table-hover align-middle mb-0" id="salarySlipsTable" style="background-color: var(--bg-card); color: var(--text-primary);">
                    <thead>
                        <tr style="background-color: rgba(0,0,0,0.02); border-bottom: 2px solid var(--border-color);">
                            <th class="py-3 px-3" style="width: 120px;">{{ __('Kỳ lương') }}</th>
                            <th class="py-3">{{ __('Lương cơ bản') }}</th>
                            <th class="py-3">{{ __('Phụ cấp') }}</th>
                            <th class="py-3">{{ __('Thưởng') }}</th>
                            <th class="py-3">{{ __('Khấu trừ') }}</th>
                            <th class="py-3">{{ __('Tạm ứng') }}</th>
                            <th class="py-3 fw-bold" style="color: var(--accent-color);">{{ __('Thực nhận') }}</th>
                            <th class="py-3 text-center">{{ __('Trạng thái') }}</th>
                            <th class="py-3 text-end">{{ __('In phiếu') }}</th>
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
                                    <div class="fw-bold fs-6">{{ app()->getLocale() === 'en' ? $dt->format('M') : __('Tháng') . ' ' . $dt->month }}</div>
                                    <div class="text-muted small">{{ __('Năm') }} {{ $dt->year }}</div>
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
                                        <span class="badge badge-success" style="background-color: rgba(11, 170, 75, 0.15); color: #0BAA4B; border: 1px solid rgba(11, 170, 75, 0.2);">{{ __('Đã thanh toán') }}</span>
                                    @else
                                        <span class="badge badge-warning" style="background-color: rgba(255, 193, 7, 0.15); color: #856404; border: 1px solid rgba(255, 193, 7, 0.2);">{{ __('Pending') }}</span>
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
