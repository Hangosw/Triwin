@extends('layouts.app')

@section('title', 'Quản lý chấm công - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý chấm công</h1>
        <p>Theo dõi chấm công và giờ làm việc của nhân viên</p>
    </div>

    <!-- Filter Bar -->
    <div class="card">
        <form action="{{ route('cham-cong.danh-sach') }}" method="GET" class="action-bar">
            <div style="display: flex; gap: 16px; align-items: center;">
                <div class="form-group" style="margin-bottom: 0; min-width: 120px;">
                    <select name="month" class="form-control" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0; min-width: 120px;">
                    <select name="year" class="form-control" onchange="this.form.submit()">
                        @for($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action-buttons">
                <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                <a href="{{ route('cham-cong.taoView') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Chấm công
                </a>
                <a href="{{ route('cham-cong.importView') }}" class="btn btn-success"
                    style="background-color: #10b981; border-color: #10b981; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import chấm công
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Tổng nhân viên</div>
            <div class="value" style="color: #3b82f6;">{{ $totalEmployees }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Đi làm đúng giờ (Tháng)</div>
            <div class="value" style="color: #10b981;">{{ $onTimeCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Đi muộn (Tháng)</div>
            <div class="value" style="color: #f97316;">{{ $lateCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Tổng lượt chấm công</div>
            <div class="value" style="color: #6366f1;">{{ count($attendances) }}</div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="attendanceTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th><strong>STT</strong></th>
                        <th>Nhân viên</th>
                        <th>Loại</th>
                        <th>Phòng ban</th>
                        <th>Ngày</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th>Công</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $index => $att)
                        <tr>
                            <td><strong>{{ $index + 1 }}</strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="avatar"
                                        style="width: 40px; height: 40px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #0BAA4B; font-weight: bold;">
                                        {{ substr($att->nhanVien->Ten, 0, 1) }}
                                    </div>
                                    <div class="font-medium">{{ $att->nhanVien->Ten }}</div>
                                </div>
                            </td>
                            <td>
                                @if($att->Loai == 1)
                                    <span class="badge badge-primary" style="background-color: #6366f1; color: white;">Tăng ca</span>
                                @else
                                    <span class="badge badge-info" style="background-color: #3b82f6; color: white;">Hành chính</span>
                                @endif
                            </td>
                            <td>{{ $att->nhanVien->ttCongViec->phongBan->Ten ?? '-' }}</td>
                            <td>{{ $att->Vao->format('d/m/Y') }}</td>
                            <td>{{ $att->Vao->format('H:i:s') }}</td>
                            <td>{{ $att->Ra ? $att->Ra->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($att->Cong > 0)
                                    <span class="font-medium" style="color: #0BAA4B;">{{ number_format($att->Cong, 2) }}</span>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($att->TrangThai === 'dung_gio')
                                    <span class="badge badge-success">Đúng giờ</span>
                                @elseif($att->TrangThai === 'tre')
                                    <span class="badge badge-warning">Đi muộn</span>
                                @elseif($att->TrangThai === 've_som')
                                    <span class="badge badge-orange">Về sớm</span>
                                @else
                                    <span class="badge badge-gray">{{ $att->TrangThai }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(count($attendances) == 0)
                        <tr>
                            <td colspan="8" style="text-align: center; color: #6b7280; padding: 20px;">Không có dữ liệu chấm
                                công cho thời gian này</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
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
            pageLength: 25,
            order: [[4, 'desc'], [5, 'desc']]
        });
    });
</script>
@endpush
