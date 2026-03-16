<div class="tab-content" id="tab-salary">
    <div class="detail-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <h2 style="margin-bottom:0; border-bottom:none;">
                <i class="bi bi-graph-up-arrow"></i>
                Diễn biến lương
            </h2>
            <a href="{{ route('salary.config', ['nhanVienId' => $employee->id]) }}" class="btn btn-primary"
                style="display:flex;align-items:center;gap:6px;">
                <i class="bi bi-box-arrow-up-right"></i>
                Xem chi tiết / Quản lý
            </a>
        </div>

        @if($employee->dienBienLuongs->isEmpty())
            <div style="text-align:center; padding:48px 24px; color:#9ca3af;">
                <i class="bi bi-cash-stack" style="font-size:48px; display:block; margin-bottom:16px; opacity:0.35;"></i>
                <div style="font-size:15px; font-weight:500; margin-bottom:8px; color:#6b7280;">Chưa có diễn biến lương
                </div>
                <div style="font-size:14px;">
                    Diễn biến lương sẽ được tạo tự động khi tạo hợp đồng có chọn <strong>Ngạch/Bậc lương</strong>.
                </div>
                <div style="margin-top:20px;">
                    <a href="{{ route('salary.config', ['nhanVienId' => $employee->id]) }}" class="btn btn-secondary">
                        <i class="bi bi-gear-fill"></i> Mở trang cấu hình lương
                    </a>
                </div>
            </div>
        @else
            @php
                $mucLuongCoSo = \App\Models\ThamSoLuong::getCurrentBaseSalary()?->MucLuongCoSo ?? 2340000;
            @endphp
            <div class="table-responsive premium-table">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ngày hưởng</th>
                            <th>Mã ngạch</th>
                            <th>Tên ngạch</th>
                            <th>Bậc</th>
                            <th>Hệ số</th>
                            <th>PC Vượt khung</th>
                            <th>Lương ngạch bậc</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->dienBienLuongs as $i => $dbl)
                            @php
                                $luongBac = $dbl->bacLuong
                                    ? $dbl->bacLuong->HeSo * $mucLuongCoSo * (1 + ($dbl->PhuCapVuotKhung ?? 0) / 100)
                                    : 0;
                                $isFirst = $i === 0;
                            @endphp
                            <tr @if($isFirst) style="background:#f0fdf4;" @endif>
                                <td><strong>{{ $i + 1 }}</strong></td>
                                <td>{{ $dbl->NgayHuong ? \Carbon\Carbon::parse($dbl->NgayHuong)->format('d/m/Y') : '–' }}</td>
                                <td>{{ $dbl->ngachLuong?->Ma ?? '–' }}</td>
                                <td>{{ $dbl->ngachLuong?->Ten ?? '–' }}</td>
                                <td>{{ $dbl->bacLuong ? 'Bậc ' . $dbl->bacLuong->Bac : '–' }}</td>
                                <td><strong>{{ $dbl->bacLuong ? number_format($dbl->bacLuong->HeSo, 2) : '–' }}</strong></td>
                                <td>{{ $dbl->PhuCapVuotKhung ?? 0 }}%</td>
                                <td style="font-weight:600; color:{{ $isFirst ? '#0BAA4B' : '#6b7280' }};">
                                    {{ number_format($luongBac, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    @if($isFirst)
                                        <span class="badge badge-success">Hiện tại</span>
                                    @else
                                        <span class="badge" style="background:#f3f4f6;color:#6b7280;">Đã qua</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="detail-section">
        <h2 style="margin-bottom:24px;">
            <i class="bi bi-file-earmark-text"></i>
            Danh sách phiếu lương tháng
        </h2>

        @if($employee->luongs->isEmpty())
            <div style="text-align:center; padding:32px; color:#9ca3af;">
                <i class="bi bi-receipt" style="font-size:40px; opacity:0.3; margin-bottom:12px; display:block;"></i>
                <div>Chưa có dữ liệu bảng lương tháng nào</div>
            </div>
        @else
            <div class="table-responsive premium-table">
                <table class="table mb-0">
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
                            <tr>
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
