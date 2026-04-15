<div class="tab-content" id="tab-salary">
    <div class="detail-section">
        <h2 style="margin-bottom:24px;">
            <i class="bi bi-file-earmark-text"></i>
            Danh sách phiếu lương tháng
        </h2>

        <div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap;">
            <div style="min-width: 150px;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block; text-transform: uppercase;">Lọc theo Tháng</label>
                <select id="monthFilter" class="form-select form-select-sm" style="border-radius: 8px; height: 38px;">
                    <option value="">Tất cả các tháng</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div style="min-width: 150px;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block; text-transform: uppercase;">Lọc theo Năm</label>
                <select id="yearFilter" class="form-select form-select-sm" style="border-radius: 8px; height: 38px;">
                    <option value="">Tất cả các năm</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= 2022; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        @if($employee->luongs->isEmpty())
            <div style="text-align:center; padding:32px; color:#9ca3af;">
                <i class="bi bi-receipt" style="font-size:40px; opacity:0.3; margin-bottom:12px; display:block;"></i>
                <div>Chưa có dữ liệu bảng lương tháng nào</div>
            </div>
        @else
            <div class="table-responsive premium-table">
                <table class="table mb-0" id="salarySlipsTable">
                    <thead>
                        <tr>
                            <th>Kỳ lương</th>
                            <th>Loại</th>
                            <th>Thực nhận</th>
                            <th>Trạng thái</th>
                            <th style="text-align:right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->luongs as $l)
                            @php
                                $dt = \Carbon\Carbon::parse($l->ThoiGian);
                            @endphp
                            <tr data-month="{{ $dt->month }}" data-year="{{ $dt->year }}">
                                <td><strong>Tháng {{ $dt->month }}/{{ $dt->year }}</strong></td>
                                <td>
                                    @if($l->LoaiLuong === 0)
                                        <span class="badge badge-info">Văn phòng</span>
                                    @else
                                        <span class="badge badge-warning">Công nhân</span>
                                    @endif
                                </td>
                                <td style="font-weight:600; color:#0BAA4B;">
                                    {{ number_format($l->Luong, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    @if($l->TrangThai == 1)
                                        <span class="badge badge-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge badge-warning">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <button class="btn btn-secondary btn-sm btn-show-slip"
                                        data-nv-id="{{ $employee->id }}"
                                        data-thang="{{ $dt->month }}"
                                        data-nam="{{ $dt->year }}"
                                        style="padding:4px 10px; font-size:13px; display:inline-flex; align-items:center; gap:4px;">
                                        <i class="bi bi-printer"></i> In phiếu
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
