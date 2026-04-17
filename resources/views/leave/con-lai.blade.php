@extends('layouts.app')

@section('title', 'Danh sách nghỉ phép còn lại - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 12px;
            margin-left: 8px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 8px;
        }
        /* Đảm bảo bảng matrix cuộn ngang mượt mà trên mobile, không bị rớt dòng chữ */
        #leaveBalanceTable th, #leaveBalanceTable td {
            white-space: nowrap;
        }

        /* Hide sorting icons for STT */
        #leaveBalanceTable th:first-child:before,
        #leaveBalanceTable th:first-child:after {
            display: none !important;
        }
        #leaveBalanceTable th:first-child {
            cursor: default !important;
        }

        /* Dark Mode Overrides */
        body.dark-theme .dataTables_wrapper .dataTables_filter input,
        body.dark-theme .dataTables_wrapper .dataTables_length select {
            background: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        body.dark-theme #leaveBalanceTable thead {
            background-color: #21263a !important;
        }

        body.dark-theme #leaveBalanceTable th {
            color: #c3c8da;
            border-color: #2e3349;
        }

        body.dark-theme #leaveBalanceTable td {
            color: #e8eaf0;
            border-color: #2e3349;
        }

        body.dark-theme #leaveBalanceTable tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        body.dark-theme .form-label {
            color: #8b93a8 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Danh sách phép còn lại</h1>
        <p>Theo dõi hạn mức nghỉ phép của nhân viên</p>
    </div>

    <!-- Filter Bar -->
    <div class="card">
        <form action="{{ route('nghi-phep.con-lai') }}" method="GET" class="action-bar" id="filterForm">
            <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Năm thống kê</label>
                    <select name="nam" class="form-control" style="width: auto; min-width: 150px; margin-bottom: 0;" onchange="this.form.submit()">
                        @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ request('nam', date('Y')) == $i ? 'selected' : '' }}>
                                Năm {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Phòng ban</label>
                    <select name="phong_ban_id" class="form-control" style="width: auto; min-width: 200px; margin-bottom: 0;" onchange="this.form.submit()">
                        <option value="">Tất cả phòng ban</option>
                        @foreach($phongBans as $pb)
                            <option value="{{ $pb->id }}" {{ request('phong_ban_id') == $pb->id ? 'selected' : '' }}>
                                {{ $pb->Ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="action-buttons">
                <!-- Nút xuất Excel (Placeholder cho tương lai) -->
                {{-- 
                <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                --}}
            </div>
        </form>
    </div>

    <!-- Data Matrix -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="leaveBalanceTable" style="width: 100%;">
                <thead style="background-color: #f9fafb;">
                    <tr>
                        <th style="width: 60px; text-align: center;">STT</th>
                        <th style="width: 250px;">Nhân viên</th>
                        <th style="width: 200px;">Phòng ban</th>
                        @foreach($loaiNghiPheps as $lp)
                            <th style="text-align: right; min-width: 140px; width: 140px;">
                                {{ $lp->Ten }} <br>
                                <small style="color: #6b7280; font-weight: normal;">
                                    (Tối đa: {{ $lp->Ten == 'Nghỉ phép năm' ? 'Theo QĐ' : $lp->HanMucToiDa }})
                                </small>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($nhanViens as $index => $nv)
                        <tr>
                            <td style="text-align: center; vertical-align: middle;"><strong>{{ $index + 1 }}</strong></td>
                            <td style="vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="avatar" style="width: 32px; height: 32px; flex-shrink: 0; min-width: 32px; min-height: 32px; background: #0BAA4B; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 14px;">
                                        {{ substr($nv->Ten, 0, 1) }}
                                    </div>
                                    <div class="font-medium" style="color: #1f2937;">{{ $nv->Ten }} <br><small style="color: #6b7280;">{{ $nv->Ma }}</small></div>
                                </div>
                            </td>
                            <td style="vertical-align: middle; color: #4b5563;">{{ $nv->ttCongViec->phongBan->Ten ?? 'N/A' }}</td>
                            
                            @foreach($loaiNghiPheps as $lp)
                                <td style="text-align: right; vertical-align: middle;">
                                    @if($lp->Ten == 'Nghỉ phép năm')
                                        @php
                                            $phepNam = $nv->quanLyPhepNams->first();
                                            $khaDung = $phepNam ? (float)$phepNam->PhepKhaDung : 0;
                                            $tong = $phepNam ? (float)$phepNam->TongPhepDuocNghi : 0;
                                        @endphp
                                        <div style="text-align: right;">
                                            <span class="badge {{ $khaDung <= 0 ? 'badge-danger' : ($khaDung < 1 ? 'badge-warning' : 'badge-success') }}" title="Phép khả dụng">
                                                {{ $khaDung }}
                                            </span>
                                            <span style="font-size: 11px; color: #64748b; margin: 0 2px;">/</span>
                                            <span style="font-size: 12px; color: #64748b;" title="Tổng phép năm">
                                                {{ $tong }}
                                            </span>
                                        </div>
                                    @else
                                        @php
                                            $used = $nv->dangKyNghiPheps->where('LoaiNghiPhepId', $lp->id)->sum('SoNgayNghi');
                                            $conLai = max(0, $lp->HanMucToiDa - $used);
                                        @endphp
                                        <span class="badge {{ $conLai <= 0 ? 'badge-danger' : ($conLai < 3 ? 'badge-warning' : 'badge-success') }}" style="font-size: 13px; font-weight: 600;">
                                            {{ (float)$conLai }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
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
        const table = $('#leaveBalanceTable').DataTable({
            "language": {
                "sProcessing": "Đang xử lý...",
                "sLengthMenu": "Xem _MENU_ mục",
                "sZeroRecords": "Không tìm thấy dữ liệu nhân viên",
                "sInfo": "Đang xem _START_ đến _END_ / Tổng số _TOTAL_ nhân viên",
                "sInfoEmpty": "Đang xem 0 đến 0 trong tổng số 0 mục",
                "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                "sSearch": "Tìm kiếm nhanh:",
                "oPaginate": {
                    "sFirst": "Đầu",
                    "sPrevious": "Trước",
                    "sNext": "Tiếp",
                    "sLast": "Cuối"
                }
            },
            "pageLength": 25,
            "scrollX": true,
            "responsive": false,
            "autoWidth": false,
            "ordering": true,
            "order": [[1, 'asc']], // Sort by name by default, not by STT
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });

        // Tự động đánh lại STT khi sort hoặc search
        table.on('order.dt search.dt', function () {
            let i = 1;
            table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data('<strong>' + i++ + '</strong>');
            });
        }).draw();
    });
</script>
@endpush
