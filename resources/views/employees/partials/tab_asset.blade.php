<div class="tab-content" id="tab-asset">
    <div class="detail-section">
        <div class="d-flex flex-column gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="section-icon-circle">
                    <i class="bi bi-pc-display"></i>
                </div>
                <h2 style="margin: 0; border-bottom: none; font-size: 1.25rem; font-weight: 700; color: #1f2937;">
                    {{ __('Danh sách tài sản đang mượn') }}
                </h2>
            </div>
            @can('Quản lý tài sản')
                <div class="d-flex">
                    <a href="{{ route('tai-san.index') }}" class="btn-premium-add text-decoration-none">
                        <i class="bi bi-plus-lg"></i>
                        <span>{{ __('Cấp phát mới') }}</span>
                    </a>
                </div>
            @endcan
        </div>
        @if($employee->taiSans->isEmpty())
            <div class="py-5 text-center" style="background-color: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border-color);">
                <i class="bi bi-pc-display mb-3 d-block" style="font-size: 48px; color: var(--text-secondary); opacity: 0.3;"></i>
                <div style="color: var(--text-secondary);">{{ __('Nhân viên này hiện không giữ tài sản nào của công ty.') }}</div>
            </div>
        @else
            <div class="premium-table">
                <table class="table mb-0" id="assetsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th width="50" class="text-center all">{{ __('STT') }}</th>
                            <th class="min-tablet">{{ __('Hình ảnh') }}</th>
                            <th class="all">{{ __('Tên tài sản') }}</th>
                            <th class="min-tablet">{{ __('Ngày bàn giao') }}</th>
                            <th class="text-center all">{{ __('Tình trạng') }}</th>
                            <th class="none">{{ __('Ghi chú') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->taiSans as $index => $asset)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    @if($asset->hinh_anh)
                                        <img src="{{ asset($asset->hinh_anh) }}" alt="{{ $asset->ten_tai_san }}" class="rounded"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $asset->ten_tai_san }}</td>
                                <td>{{ $asset->ngay_muon ? $asset->ngay_muon->format('d/m/Y') : '--' }}</td>
                                <td class="text-center">
                                    @php
                                        $statusClasses = [
                                            'SanSang' => ['text' => __('Sẵn sàng'), 'class' => 'badge-success'],
                                            'DangMuon' => ['text' => __('Đang mượn'), 'class' => 'badge-info'],
                                            'BaoTri' => ['text' => __('Bảo trì'), 'class' => 'badge-warning'],
                                            'Hong' => ['text' => __('Hỏng'), 'class' => 'badge-danger'],
                                        ];
                                        $s = $statusClasses[$asset->trang_thai] ?? ['text' => $asset->trang_thai, 'class' => 'badge-secondary'];
                                    @endphp
                                    <span class="badge {{ $s['class'] }}">{{ $s['text'] }}</span>
                                </td>
                                <td><small class="text-muted">{{ $asset->ghi_chu ?: '--' }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>