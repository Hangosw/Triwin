@extends('layouts.app')

@section('title', 'Quản lý Work From Home - Triwin')

@push('styles')
    <style>
        .tabs { display: flex; gap: 8px; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; }
        .tab {
            padding: 12px 24px; background: none; border: none; border-bottom: 2px solid transparent;
            color: #6b7280; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; margin-bottom: -2px;
        }
        .tab:hover { color: #3b82f6; }
        .tab.active { color: #3b82f6; border-bottom-color: #3b82f6; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        .btn-icon { background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý Work From Home</h1>
        <p>Theo dõi và phê duyệt đơn đăng ký làm việc ngoài văn phòng</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Đơn chờ duyệt</div>
            <div class="value" style="color: #f59e0b;">{{ $pendingCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Đã phê duyệt</div>
            <div class="value" style="color: #10b981;">{{ $approvedCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Từ chối</div>
            <div class="value" style="color: #ef4444;">{{ $rejectedCount }}</div>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="card" style="display: none; background: #eff6ff; border: 1px solid #bfdbfe; margin-bottom: 16px;">
        <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-weight: 600; color: #1e40af;">Đã chọn <span id="selectedCount">0</span> phiếu</span>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-primary" onclick="bulkApprove()">Duyệt nhanh</button>
                <button type="button" class="btn btn-secondary" onclick="bulkReject()">Từ chối nhanh</button>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card">
        <div class="action-bar">
            <div class="search-bar">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên, phòng ban..." id="customSearch">
            </div>
        </div>
    </div>

    <!-- Tabs and Table -->
    <div class="card">
        <div class="tabs">
            <button class="tab {{ !request('trang_thai') ? 'active' : '' }}" onclick="filterStatus('')">Tất cả ({{ $wfhRequests->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'dang_cho' ? 'active' : '' }}" onclick="filterStatus('dang_cho')">Chờ duyệt ({{ $wfhRequests->where('TrangThai', 'dang_cho')->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'da_duyet' ? 'active' : '' }}" onclick="filterStatus('da_duyet')">Đã duyệt ({{ $wfhRequests->where('TrangThai', 'da_duyet')->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'tu_choi' ? 'active' : '' }}" onclick="filterStatus('tu_choi')">Từ chối ({{ $wfhRequests->where('TrangThai', 'tu_choi')->count() }})</button>
        </div>

        <div class="table-container">
            <table class="table" id="wfhTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;"><input type="checkbox" id="selectAll"></th>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Thời gian WFH</th>
                        <th>Số ngày</th>
                        <th>Lý do & Ghi chú</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wfhRequests as $wfh)
                        <tr>
                            <td class="text-center"><input type="checkbox" class="row-checkbox" value="{{ $wfh->id }}"></td>
                            <td>
                                <div class="font-medium">{{ $wfh->nhanVien?->Ten }}</div>
                                <div class="text-xs text-gray-500">{{ $wfh->nhanVien?->Ma }}</div>
                            </td>
                            <td>{{ $wfh->nhanVien?->ttCongViec?->phongBan?->Ten ?? 'N/A' }}</td>
                            <td>
                                <div class="text-sm">
                                    {{ $wfh->NgayBatDau->format('d/m/Y') }} - {{ $wfh->NgayKetThuc->format('d/m/Y') }}
                                </div>
                            </td>
                            <td><span class="badge" style="background: #eff6ff; color: #1e40af;">{{ $wfh->Ngay }} ngày</span></td>
                            <td>
                                <div class="text-sm">{{ Str::limit($wfh->LyDo, 50) }}</div>
                                @if($wfh->GhiChu)
                                    <div class="text-xs text-blue-600 mt-1 italic">{{ $wfh->GhiChu }}</div>
                                @endif
                            </td>
                            <td>
                                @if($wfh->TrangThai === 'dang_cho')
                                    <span class="badge badge-warning">Chờ duyệt</span>
                                @elseif($wfh->TrangThai === 'da_duyet')
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Từ chối</span>
                                @endif
                            </td>
                            <td>
                                @if ($wfh->TrangThai === 'dang_cho')
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-icon text-success" onclick="approveWfh({{ $wfh->id }})" title="Duyệt">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="btn-icon text-danger" onclick="rejectWfh({{ $wfh->id }})" title="Từ chối">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#wfhTable').DataTable({
                language: { "sProcessing": "Đang xử lý...", "sLengthMenu": "Hiển thị _MENU_ dòng", "sZeroRecords": "Không tìm thấy dữ liệu", "sInfo": "Hiển thị _START_ đến _END_ trên _TOTAL_", "sSearch": "Tìm kiếm:", "oPaginate": { "sFirst": "Đầu", "sPrevious": "Trước", "sNext": "Tiếp", "sLast": "Cuối" } },
                order: [[3, 'desc']],
                dom: 'rtip',
                columnDefs: [{ orderable: false, targets: [0, 7] }]
            });

            $('#customSearch').on('keyup', function () { table.search(this.value).draw(); });

            $('#selectAll').on('change', function () {
                $('.row-checkbox').prop('checked', this.checked);
                updateBulkBar();
            });

            $('.row-checkbox').on('change', function () {
                updateBulkBar();
                $('#selectAll').prop('checked', $('.row-checkbox').length === $('.row-checkbox:checked').length);
            });

            function updateBulkBar() {
                const count = $('.row-checkbox:checked').length;
                if (count > 0) {
                    $('#selectedCount').text(count);
                    $('#bulkActionBar').fadeIn();
                } else {
                    $('#bulkActionBar').fadeOut();
                }
            }
        });

        function filterStatus(status) {
            const url = new URL(window.location.href);
            if (status) url.searchParams.set('trang_thai', status);
            else url.searchParams.delete('trang_thai');
            window.location.href = url.toString();
        }

        function approveWfh(id) {
            Swal.fire({
                title: 'Phê duyệt WFH',
                input: 'textarea',
                inputLabel: 'Ghi chú (nếu có)',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Phê duyệt'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/wfh/duyet/${id}`, { _token: '{{ csrf_token() }}', GhiChu: result.value }, function(res) {
                        if (res.success) location.reload();
                    });
                }
            });
        }

        function rejectWfh(id) {
            Swal.fire({
                title: 'Từ chối WFH',
                input: 'textarea',
                inputLabel: 'Lý do từ chối',
                inputValidator: (value) => { if (!value) return 'Vui lòng nhập lý do!' },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Từ chối'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/wfh/tu-choi/${id}`, { _token: '{{ csrf_token() }}', GhiChu: result.value }, function(res) {
                        if (res.success) location.reload();
                    });
                }
            });
        }

        function bulkApprove() {
            const ids = $('.row-checkbox:checked').map(function () { return this.value; }).get();
            Swal.fire({
                title: 'Phê duyệt hàng loạt',
                text: `Duyệt ${ids.length} phiếu đã chọn?`,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Duyệt ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route("wfh.bulk-duyet") }}', { _token: '{{ csrf_token() }}', ids: ids }, function(res) {
                        if (res.success) location.reload();
                    });
                }
            });
        }

        function bulkReject() {
            const ids = $('.row-checkbox:checked').map(function () { return this.value; }).get();
            Swal.fire({
                title: 'Từ chối hàng loạt',
                text: `Từ chối ${ids.length} phiếu đã chọn?`,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Từ chối ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route("wfh.bulk-tu-choi") }}', { _token: '{{ csrf_token() }}', ids: ids }, function(res) {
                        if (res.success) location.reload();
                    });
                }
            });
        }
    </script>
@endpush
