@extends('layouts.app')

@section('title', 'Danh sách nghỉ phép còn lại - Vietnam Rubber Group')

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
                <!-- <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button> -->
            </div>
        </form>
    </div>

    <!-- Data Matrix -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="leaveBalanceTable" style="width: 100%;">
                <thead style="background-color: #f9fafb;">
                    <tr>
                        <th style="width: 50px; text-align: center;">STT</th>
                        <th>Nhân viên</th>
                        <th>Phòng ban</th>
                        @foreach($loaiNghiPheps as $lp)
                            <th style="text-align: right; min-width: 120px;">
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
                                @php
                                    $conLai = 0;
                                    if ($lp->Ten == 'Nghỉ phép năm') {
                                        $phepNam = $nv->quanLyPhepNams->first();
                                        $conLai = $phepNam ? (float)$phepNam->ConLai : 0;
                                    } else {
                                        $used = $nv->dangKyNghiPheps->where('LoaiNghiPhepId', $lp->id)->sum('SoNgayNghi');
                                        $conLai = max(0, $lp->HanMucToiDa - $used);
                                    }
                                @endphp
                                <td style="text-align: right; vertical-align: middle;">
                                    <span class="badge {{ $conLai <= 0 ? 'badge-danger' : ($conLai < 3 ? 'badge-warning' : 'badge-success') }}" style="font-size: 13px; font-weight: 600;">
                                        {{ (float)$conLai }}
                                    </span>
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
        $('#leaveBalanceTable').DataTable({
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
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });
    });
</script>
@endpush
