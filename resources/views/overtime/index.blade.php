@extends('layouts.app')

@section('title', 'Quản lý tăng ca - Vietnam Rubber Group')

@push('styles')
    <style>
        /* Tab Styles */
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: -2px;
        }

        .tab:hover {
            color: #0BAA4B;
        }

        .tab.active {
            color: #0BAA4B;
            border-bottom-color: #0BAA4B;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý tăng ca</h1>
        <p>Theo dõi và phê duyệt đơn đăng ký tăng ca</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Tổng giờ tăng ca tháng này</div>
            <div class="value" style="color: #3b82f6;">{{ number_format($totalHoursThisMonth, 1) }}h</div>
        </div>
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
    <div id="bulkActionBar" class="card"
        style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 16px;">
        <div class="card-body"
            style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-weight: 600; color: #088c3d;">Đã chọn <span id="selectedCount">0</span> phiếu</span>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-primary" onclick="bulkApprove()">
                    Duyệt các phiếu đã chọn
                </button>
                <button type="button" class="btn btn-secondary" onclick="bulkReject()">
                    Từ chối các phiếu đã chọn
                </button>
            </div>
        </div>
    </div>

    <!-- Filter and Action Bar -->
    <div class="card">
        <div class="action-bar">
            <div class="search-bar">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên, phòng ban..." id="customSearch">
            </div>
            <div class="action-buttons">
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                <button type="button" class="btn btn-primary" onclick="openOvertimeModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký tăng ca
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs Filtering (UI only for grouping, DataTables will handle display) -->
    <div class="card">
        <div class="tabs">
            <button class="tab {{ !request('trang_thai') ? 'active' : '' }}" onclick="filterStatus('')">Tất cả
                ({{ $tangCas->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'dang_cho' ? 'active' : '' }}"
                onclick="filterStatus('dang_cho')">Chờ duyệt
                ({{ $tangCas->where('TrangThai', 'dang_cho')->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'da_duyet' ? 'active' : '' }}"
                onclick="filterStatus('da_duyet')">Đã duyệt
                ({{ $tangCas->where('TrangThai', 'da_duyet')->count() }})</button>
            <button class="tab {{ request('trang_thai') == 'tu_choi' ? 'active' : '' }}"
                onclick="filterStatus('tu_choi')">Từ chối ({{ $tangCas->where('TrangThai', 'tu_choi')->count() }})</button>
        </div>

        <div class="table-container">
            <table class="table" id="overtimeTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">
                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                        </th>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Ngày tăng ca</th>
                        <th>Thời gian</th>
                        <th>Lý do & Ghi chú</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tangCas as $index => $ot)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-checkbox" value="{{ $ot->id }}" style="cursor: pointer;">
                            </td>
                            <td>
                                <div class="font-medium text-gray-900">{{ $ot->nhanVien?->Ten }}</div>
                                <div class="text-sm text-gray-500">{{ $ot->nhanVien?->Ma }}</div>
                            </td>
                            <td>{{ $ot->nhanVien?->ttCongViec?->phongBan?->Ten ?? 'N/A' }}</td>
                            <td>{{ $ot->Ngay?->format('d/m/Y') }}</td>
                            <td>
                                <div class="font-medium">
                                    {{ substr($ot->BatDau, 0, 5) }} - {{ substr($ot->KetThuc, 0, 5) }}
                                </div>
                                <div class="text-xs text-green-700">Tổng: {{ $ot->Tong }}h</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-600" title="{{ $ot->LyDo }}">
                                    {{ Str::limit($ot->LyDo, 50) }}
                                </div>
                                @if ($ot->GhiChuLanhDao)
                                    <div class="text-xs text-blue-600 mt-1 italic">
                                        {!! $ot->GhiChuLanhDao !!}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($ot->TrangThai === 'dang_cho')
                                    <span class="badge badge-warning">Chờ duyệt</span>
                                @elseif($ot->TrangThai === 'da_duyet')
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Từ chối</span>
                                @endif
                            </td>
                            <td>
                                @if ($ot->TrangThai === 'dang_cho')
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-icon text-success" onclick="approveOvertime({{ $ot->id }})"
                                            title="Duyệt">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="btn-icon text-danger" onclick="rejectOvertime({{ $ot->id }})"
                                            title="Từ chối">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Registration Modal -->
    <div id="overtimeModal" class="modal"
        style="display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div class="modal-content card" style="max-width:500px; margin: 10vh auto; padding: 24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0;">Đăng ký tăng ca</h2>
                <button type="button" class="btn-icon" onclick="closeOvertimeModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px; height:24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="overtimeForm" onsubmit="submitOvertime(event)">
                <div class="form-group">
                    <label class="form-label">Chọn nhân viên <span class="text-danger">*</span></label>
                    <select name="NhanVienId" class="form-control" id="overtimeEmployee" required>
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach($phongBans as $pb)
                            <optgroup label="{{ $pb->Ten }}">
                                @foreach($pb->ttNhanVienCongViec as $tt)
                                    <option value="{{ $tt->nhanVien->id }}">{{ $tt->nhanVien->Ten }} ({{ $tt->nhanVien->Ma }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày tăng ca <span class="text-danger">*</span></label>
                    <input type="text" name="Ngay" class="form-control datepicker" id="overtimeDate" placeholder="dd/mm/yyyy" required readonly>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Bắt đầu <span class="text-danger">*</span></label>
                        <input type="time" name="BatDau" class="form-control" id="overtimeStart" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kết thúc <span class="text-danger">*</span></label>
                        <input type="time" name="KetThuc" class="form-control" id="overtimeEnd" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Lý do <span class="text-danger">*</span></label>
                    <textarea name="LyDo" class="form-control" id="overtimeReason" required
                        style="min-height:80px;"></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeOvertimeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu phiếu</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#overtimeTable').DataTable({
                language: {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Hiển thị _MENU_ dòng",
                    "sZeroRecords": "Không tìm thấy dữ liệu",
                    "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                order: [
                    [3, 'desc']
                ], // Sắp xếp theo ngày tăng ca
                pageLength: 10,
                dom: 'rtip', // Hide default search box
                columnDefs: [{
                    orderable: false,
                    targets: [0, 7]
                }]
            });

            // Custom Search
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Select All logic
            $('#selectAll').on('change', function () {
                $('.row-checkbox').prop('checked', this.checked);
                updateBulkBar();
            });

            $('.row-checkbox').on('change', function () {
                updateBulkBar();
                const allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
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

        function openOvertimeModal() {
            $('#overtimeModal').show();
            const today = new Date();
            const d = String(today.getDate()).padStart(2, '0');
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const y = today.getFullYear();
            $('#overtimeDate').val(`${d}/${m}/${y}`);
        }

        function closeOvertimeModal() {
            $('#overtimeModal').hide();
            $('#overtimeForm')[0].reset();
        }

        function submitOvertime(e) {
            e.preventDefault();
            const formData = {
                _token: '{{ csrf_token() }}',
                NhanVienId: $('#overtimeEmployee').val(),
                Ngay: $('#overtimeDate').val(),
                BatDau: $('#overtimeStart').val(),
                KetThuc: $('#overtimeEnd').val(),
                LyDo: $('#overtimeReason').val()
            };

            $.ajax({
                url: '{{ route("tang-ca.tao-moi") }}',
                type: 'POST',
                data: formData,
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Thành công!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi!', res.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Lỗi!', xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra.', 'error');
                }
            });
        }

        function approveOvertime(id) {
            Swal.fire({
                title: 'Phê duyệt tăng ca',
                input: 'textarea',
                inputLabel: 'Ghi chú lãnh đạo (nếu có)',
                inputPlaceholder: 'Nhập ghi chú...',
                showCancelButton: true,
                confirmButtonColor: '#0BAA4B',
                confirmButtonText: 'Phê duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/tang-ca/duyet/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            GhiChuLanhDao: result.value
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Thành công!', res.message, 'success').then(() => location.reload());
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra.', 'error');
                        }
                    });
                }
            });
        }

        function rejectOvertime(id) {
            Swal.fire({
                title: 'Từ chối tăng ca',
                input: 'textarea',
                inputLabel: 'Lý do từ chối',
                inputPlaceholder: 'Nhập lý do...',
                inputValidator: (value) => { if (!value) return 'Vui lòng nhập lý do!' },
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Từ chối',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/tang-ca/tu-choi/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            GhiChuLanhDao: result.value
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Đã từ chối!', res.message, 'success').then(() => location.reload());
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra.', 'error');
                        }
                    });
                }
            });
        }

        function bulkApprove() {
            const ids = $('.row-checkbox:checked').map(function () { return this.value; }).get();
            Swal.fire({
                title: 'Duyệt hàng loạt',
                text: `Phê duyệt ${ids.length} phiếu đã chọn?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0BAA4B',
                confirmButtonText: 'Duyệt ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("tang-ca.bulk-duyet") }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', ids: ids },
                        success: function (res) {
                            if (res.success) Swal.fire('Thành công!', res.message, 'success').then(() => location.reload());
                        }
                    });
                }
            });
        }

        function bulkReject() {
            const ids = $('.row-checkbox:checked').map(function () { return this.value; }).get();
            Swal.fire({
                title: 'Từ chối hàng loạt',
                text: `Từ chối ${ids.length} phiếu đã chọn?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Từ chối ngay'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("tang-ca.bulk-tu-choi") }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', ids: ids },
                        success: function (res) {
                            if (res.success) Swal.fire('Đã từ chối!', res.message, 'success').then(() => location.reload());
                        }
                    });
                }
            });
        }
    </script>
@endpush
