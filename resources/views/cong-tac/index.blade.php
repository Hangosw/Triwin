@extends('layouts.app')

@section('title', 'Quản lý Công tác - HRM')

@section('content')
    <div class="page-header">
        <h1>Quá trình công tác</h1>
        <p>Quản lý quá trình công tác của toàn bộ nhân viên</p>
    </div>

    <div class="card">
        <div class="action-bar" style="flex-wrap: wrap; gap: 12px;">
            <div style="flex: 1; min-width: 200px;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="congTacSearch" class="form-control"
                        placeholder="Tìm kiếm nhân viên, đơn vị, chức vụ...">
                </div>
            </div>

            <div class="action-buttons">
                @can('Quản lý công tác')
                    <a href="{{ route('cong-tac.taoView') }}" class="btn btn-primary" style="background:#0BAA4B;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tạo công tác
                    </a>
                @endcan
                <button class="btn btn-secondary" style="display:flex; align-items:center; gap:6px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất báo cáo
                </button>
            </div>
        </div>
    </div>

    @if($quatrinhs->isEmpty())
        <div class="card" style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Chưa có dữ liệu công tác</div>
            <div style="font-size: 14px;">Chưa có quá trình công tác nào được ghi nhận trên hệ thống.</div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="table" id="congTacTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;"><strong>STT</strong></th>
                            <th>Nhân viên</th>
                            <th>Phòng ban, Chức vụ</th>
                            <th>Từ ngày</th>
                            <th>Đến ngày</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quatrinhs as $index => $qt)
                            @php
                                $nv = $qt->nhanVien;
                                $phongBan = $qt->phongBan;
                                $chucVu = $qt->chucVu;
                                $isCurrent = is_null($qt->DenNgay) || \Carbon\Carbon::parse($qt->DenNgay)->endOfDay()->isFuture();
                            @endphp
                            <tr class="congtac-row">
                                <td><strong>{{ $index + 1 }}</strong></td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $nv?->Ten ?? '—' }}
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280;">{{ $nv?->Ma ?? '' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #0BAA4B;">{{ $phongBan?->Ten ?? '—' }}</div>
                                    <div style="font-size: 13px; color: #4b5563;">
                                        <i class="bi bi-briefcase" style="font-size: 11px;"></i>
                                        {{ $chucVu?->Ten ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($qt->TuNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->TuNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($qt->DenNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->DenNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isCurrent)
                                        <span class="badge badge-success">Đang công tác</span>
                                    @else
                                        <span class="badge badge-gray">Đã kết thúc</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="{{ route('nhan-vien.info', $nv?->id ?? 0) }}" class="btn-icon"
                                            title="Xem hồ sơ nhân viên">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                style="padding: 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb;">
                <div style="font-size: 14px; color: #6b7280;">
                    Hiển thị <strong>{{ $quatrinhs->count() }}</strong> quá trình công tác
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#congTacTable').DataTable({
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
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    dom: 'rtip',
                    columnDefs: [
                        { orderable: false, targets: [6] }
                    ]
                });

                // Custom Search
                $('#congTacSearch').on('keyup', function () {
                    table.search(this.value).draw();
                });
            });
        </script>
    @endpush
@endsection
