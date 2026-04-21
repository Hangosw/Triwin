<div class="tab-content" id="tab-asset">
    <div class="detail-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0 border-0"><i class="bi bi-pc-display"></i> Danh sách tài sản đang mượn</h2>
            @can('Quản lý tài sản')
                <a href="{{ route('tai-san.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-plus-lg"></i> Cấp phát mới
                </a>
            @endcan
        </div>
        <div class="premium-table">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th width="50" class="text-center">STT</th>
                        <th>Hình ảnh</th>
                        <th>Tên tài sản</th>
                        <th>Ngày bàn giao</th>
                        <th class="text-center">Tình trạng</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->taiSans as $index => $asset)
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
                                        'SanSang' => ['text' => 'Sẵn sàng', 'class' => 'badge-success'],
                                        'DangMuon' => ['text' => 'Đang mượn', 'class' => 'badge-info'],
                                        'BaoTri' => ['text' => 'Bảo trì', 'class' => 'badge-warning'],
                                        'Hong' => ['text' => 'Hỏng', 'class' => 'badge-danger'],
                                    ];
                                    $s = $statusClasses[$asset->trang_thai] ?? ['text' => $asset->trang_thai, 'class' => 'badge-secondary'];
                                @endphp
                                <span class="badge {{ $s['class'] }}">{{ $s['text'] }}</span>
                            </td>
                            <td><small class="text-muted">{{ $asset->ghi_chu ?: '--' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle me-1"></i> Nhân viên này hiện không giữ tài sản nào của công ty.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>