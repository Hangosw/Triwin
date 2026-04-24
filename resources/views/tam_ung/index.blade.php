@extends('layouts.app')

@section('title', 'Quản lý tạm ứng lương - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <style>
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            font-size: 13px;
        }

        body.dark-theme .table th {
            background-color: #1e293b;
            color: #e2e8f0;
        }

        .badge {
            font-size: 12px;
            padding: 5px 10px;
        }

        .btn-clear-filter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: #fef2f2;
            color: #dc2626;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid #fee2e2;
        }

        .btn-clear-filter:hover {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .btn-clear-filter svg {
            width: 16px;
            height: 16px;
        }

        body.dark-theme .btn-clear-filter {
            background-color: rgba(220, 38, 38, 0.1);
            color: #ef4444;
            border-color: rgba(220, 38, 38, 0.2);
        }

        body.dark-theme .btn-clear-filter:hover {
            background-color: rgba(220, 38, 38, 0.2);
            color: #f87171;
        }

        #tamUngTable tbody tr td {
            vertical-align: middle;
        }

        .filter-bar-container .form-label {
            font-size: 12px;
            margin-bottom: 4px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý tạm ứng lương</h1>
        <p>Danh sách các yêu cầu tạm ứng lương của nhân viên</p>
    </div>

    <!-- Actions Bar -->
    <div class="card filter-bar-container mb-4">
        <form method="GET" action="{{ route('tam-ung.index') }}">
            <div class="action-bar d-flex justify-content-between align-items-center flex-wrap gap-3"
                style="padding: 16px 24px;">
                <div class="filter-group d-flex align-items-end flex-wrap gap-2">
                    {{-- Trạng thái --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"
                            style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Trạng
                            thái</label>
                        <div class="dropdown custom-filter-dropdown" data-default="Tất cả trạng thái">
                            <input type="hidden" name="trang_thai" id="filterTrangThai" value="{{ request('trang_thai') }}">
                            <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false"
                                style="cursor: pointer; height: 38px; min-width: 180px; display: flex; justify-content: space-between; align-items: center; border-radius: 8px;">
                                @php
                                    $trangThaiText = 'Tất cả trạng thái';
                                    if (request('trang_thai') === '0')
                                        $trangThaiText = 'Chờ duyệt';
                                    if (request('trang_thai') === '1')
                                        $trangThaiText = 'Đã duyệt';
                                    if (request('trang_thai') === '2')
                                        $trangThaiText = 'Từ chối';
                                @endphp
                                <span class="dropdown-text"
                                    style="color: {{ request()->has('trang_thai') && request('trang_thai') !== '' ? '#212529' : '#6c757d' }};">{{ $trangThaiText }}</span>
                                <span class="dropdown-icon">
                                    <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                                </span>
                            </div>
                            <div class="dropdown-menu p-2 shadow"
                                style="min-width: 220px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                    <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN TRẠNG THÁI</span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                    <button type="button"
                                        class="btn btn-sm filter-btn {{ request('trang_thai') == '' ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                        data-val="" onclick="applyFilterSync('filterTrangThai', this)"
                                        style="text-align: left; {{ request('trang_thai') == '' ? 'background-color: #3b82f6; color: #fff;' : '' }}">Tất
                                        cả trạng thái</button>
                                    <button type="button"
                                        class="btn btn-sm filter-btn {{ request('trang_thai') === '0' ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                        data-val="0" onclick="applyFilterSync('filterTrangThai', this)"
                                        style="text-align: left; {{ request('trang_thai') === '0' ? 'background-color: #3b82f6; color: #fff;' : '' }}">Chờ
                                        duyệt</button>
                                    <button type="button"
                                        class="btn btn-sm filter-btn {{ request('trang_thai') === '1' ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                        data-val="1" onclick="applyFilterSync('filterTrangThai', this)"
                                        style="text-align: left; {{ request('trang_thai') === '1' ? 'background-color: #3b82f6; color: #fff;' : '' }}">Đã
                                        duyệt</button>
                                    <button type="button"
                                        class="btn btn-sm filter-btn {{ request('trang_thai') === '2' ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}"
                                        data-val="2" onclick="applyFilterSync('filterTrangThai', this)"
                                        style="text-align: left; {{ request('trang_thai') === '2' ? 'background-color: #3b82f6; color: #fff;' : '' }}">Từ
                                        chối</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nút xóa bộ lọc --}}
                    @if(request()->has('trang_thai') && request('trang_thai') != '')
                        <div class="form-group" style="margin-bottom: 0;">
                            <a href="{{ route('tam-ung.index') }}" class="btn-clear-filter">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Xóa bộ lọc
                            </a>
                        </div>
                    @endif
                </div>

                <div class="action-buttons">
                    <a href="{{ route('tam-ung.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
                        style="height: 38px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tạo yêu cầu
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="table-container p-0">
            <table class="table table-hover align-middle mb-0 w-100 nowrap" id="tamUngTable">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th class="text-end">Số tiền đề nghị</th>
                        <th>Lý do</th>
                        <th>Ngày yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Người duyệt</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tamUngs as $index => $item)
                        <tr>
                            <td class="text-center">{{ $tamUngs->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->nhanVien?->Ten ?? '—' }}</div>
                                <div class="text-muted" style="font-size: 12px;">{{ $item->nhanVien?->Ma ?? '' }}</div>
                            </td>
                            <td>{{ $item->nhanVien?->ttCongViec?->phongBan?->TenPhongBan ?? '—' }}</td>
                            <td class="text-end fw-bold text-primary">
                                {{ number_format($item->SoTien, 0, ',', '.') }} đ
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $item->Lydo }}">
                                    {{ $item->Lydo }}
                                </div>
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($item->TrangThai == 0)
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($item->TrangThai == 1)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td>
                                @if($item->TrangThai != 0)
                                    {{ $item->nguoiDuyet?->Ten ?? '—' }}
                                @else
                                    <span class="text-muted" style="font-style: italic;">Chưa duyệt</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                    data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tam-ung.update-status', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chi tiết tạm ứng - {{ $item->nhanVien?->Ten }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-6 text-muted">Số tiền xin ứng:</div>
                                                <div class="col-6 fw-bold text-primary fs-5 text-end">
                                                    {{ number_format($item->SoTien, 0, ',', '.') }} đ
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6 text-muted">Hạn mức tối đa (lúc tạo):</div>
                                                <div class="col-6 text-end">{{ number_format($item->HanMuc, 0, ',', '.') }} đ
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-12 text-muted mb-1">Lý do từ nhân viên:</div>
                                                <div class="col-12 p-2 bg-light rounded text-dark"
                                                    style="border: 1px solid #e5e7eb;">
                                                    {{ $item->Lydo }}
                                                </div>
                                            </div>

                                            @if($item->TrangThai == 0)
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Ghi chú (dành cho người duyệt)</label>
                                                    <textarea name="GhiChu" class="form-control" rows="2"
                                                        placeholder="Nhập ghi chú hoặc lý do nếu từ chối..."></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Quyết định</label>
                                                    <select name="TrangThai" class="form-select" required>
                                                        <option value="1">Duyệt yêu cầu</option>
                                                        <option value="2">Từ chối</option>
                                                    </select>
                                                </div>
                                            @else
                                                @if($item->GhiChu)
                                                    <div class="row mb-3 mt-3">
                                                        <div class="col-12 text-muted mb-1">Ghi chú duyệt:</div>
                                                        <div class="col-12 text-dark">{{ $item->GhiChu }}</div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="modal-footer"
                                            style="border-top: 1px solid #e5e7eb; padding: 16px 24px; background-color: #f9fafb;">
                                            <button type="button" class="btn text-dark"
                                                style="background-color: #f3f4f6; border: 1px solid #d1d5db; font-weight: 600; padding: 10px 20px; border-radius: 8px;"
                                                data-bs-dismiss="modal">Đóng</button>
                                            @if($item->TrangThai == 0)
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0BAA4B; border: 1px solid #0BAA4B; font-weight: 600; padding: 10px 20px; border-radius: 8px;">Lưu
                                                    quyết định</button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- DataTable handles empty message -->
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top">
            {{ $tamUngs->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#tamUngTable').DataTable({
                responsive: true,
                paging: false,
                info: false,
                searching: true,
                ordering: false,
                autoWidth: false,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // STT
                    { responsivePriority: 2, targets: 1 }, // Nhân viên
                    { responsivePriority: 3, targets: 5 }, // Ngày yêu cầu
                    { responsivePriority: 10001, targets: 3 }, // Số tiền
                    { responsivePriority: 10002, targets: 6 }, // Trạng thái
                    { responsivePriority: 10003, targets: 8 }, // Thao tác
                    { responsivePriority: 10004, targets: 2 }, // Phòng ban
                    { responsivePriority: 10005, targets: 4 }, // Lý do
                    { responsivePriority: 10006, targets: 7 }  // Người duyệt
                ],
                });
        });

        function applyFilterSync(inputId, btnEl) {
            const val = btnEl.dataset.val;
            const input = document.getElementById(inputId);
            input.value = val;
            input.closest('form').submit();
        }
    </script>
@endpush