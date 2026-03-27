@extends('layouts.app')

@section('title', 'Quản lý chấm công - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý chấm công</h1>
        <p>Theo dõi chấm công và giờ làm việc của nhân viên</p>
    </div>

    <!-- Filter Bar -->
    <div class="card">
        <form action="{{ route('cham-cong.danh-sach') }}" method="GET" class="action-bar" id="filterForm">
            <div style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                {{-- Ngày --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 130px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Ngày</label>
                    <select name="day" class="form-control" onchange="this.form.submit()">
                        <option value="" {{ $day === '' ? 'selected' : '' }}>Tất cả ngày</option>
                        @for($d = 1; $d <= 31; $d++)
                            <option value="{{ $d }}" {{ (string)$day === (string)$d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                        @endfor
                    </select>
                </div>
                {{-- Tháng --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 120px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tháng</label>
                    <select name="month" class="form-control" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>
                {{-- Năm --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 100px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Năm</label>
                    <select name="year" class="form-control" onchange="this.form.submit()">
                        @for($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                {{-- Trạng thái --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 170px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Trạng thái</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value=""        {{ $status === ''        ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="dung_gio"{{ $status === 'dung_gio'? 'selected' : '' }}>✅ Đúng giờ</option>
                        <option value="tre"     {{ $status === 'tre'     ? 'selected' : '' }}>⏰ Đi muộn</option>
                        <option value="ve_som"  {{ $status === 've_som'  ? 'selected' : '' }}>🏃 Về sớm</option>
                        <option value="la"      {{ $status === 'la'      ? 'selected' : '' }}>❓ Khách / Lạ</option>
                    </select>
                </div>
                {{-- Xóa bộ lọc nhanh --}}
                @if($day !== '' || $status !== '')
                    <div style="margin-bottom: 2px;">
                        <a href="{{ route('cham-cong.danh-sach', ['month' => $month, 'year' => $year]) }}"
                           style="font-size: 13px; color: #ef4444; text-decoration: none; white-space: nowrap; display: flex; align-items: center; gap: 4px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Xóa bộ lọc
                        </a>
                    </div>
                @endif
            </div>
            <div class="action-buttons">
                @if($day !== '' && $day > 0)
                    <a href="{{ route('cham-cong.tong-quan-ngay', ['date' => sprintf('%04d-%02d-%02d', $year, $month, $day)]) }}" 
                       class="btn btn-info" style="background-color: #3b82f6; border-color: #3b82f6; color: white;">
                        Xem tổng quan
                    </a>
                @else
                    <a href="{{ route('cham-cong.tong-quan-ngay', ['date' => now()->toDateString()]) }}" 
                       class="btn btn-info" style="background-color: #3b82f6; border-color: #3b82f6; color: white;">
                        Xem tổng quan hôm nay
                    </a>
                @endif
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
                                        style="width: 40px; height: 40px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #0BAA4B; font-weight: bold; overflow: hidden;">
                                        @if($att->AnhChamCong)
                                            <img src="{{ $att->AnhChamCong }}" alt="Captured" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="showAttendanceImage('{{ $att->AnhChamCong }}')">
                                        @elseif($att->nhanVien && $att->nhanVien->AnhDaiDien)
                                            <img src="{{ asset($att->nhanVien->AnhDaiDien) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="showAttendanceImage('{{ asset($att->nhanVien->AnhDaiDien) }}')">
                                        @elseif($att->nhanVien)
                                            {{ substr($att->nhanVien->Ten, 0, 1) }}
                                        @else
                                            L
                                        @endif
                                    </div>
                                    <div class="font-medium">
                                        {{ $att->nhanVien ? $att->nhanVien->Ten : 'Người lạ' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($att->Loai == 1)
                                    <span class="badge badge-primary" style="background-color: #6366f1; color: white;">Tăng ca</span>
                                @else
                                    <span class="badge badge-info" style="background-color: #3b82f6; color: white;">Hành chính</span>
                                @endif
                            </td>
                            <td>{{ $att->nhanVien ? ($att->nhanVien->ttCongViec->phongBan->Ten ?? '-') : '-' }}</td>
                            <td data-order="{{ $att->Vao->format('Ymd') }}">{{ $att->Vao->format('d/m/Y') }}</td>
                            <td data-order="{{ $att->Vao->timestamp }}">{{ $att->Vao->format('H:i:s') }}</td>
                            <td>{{ $att->Ra ? $att->Ra->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($att->Cong > 0)
                                    <span class="font-medium" style="color: #0BAA4B;">{{ number_format($att->Cong, 2) }}</span>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                @if(!$att->nhanVien)
                                    <span class="badge badge-danger" style="background-color: #ef4444; color: white;">Khách / Lạ</span>
                                @elseif($att->TrangThai === 'dung_gio')
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
            order: [] // Bỏ sort mặc định để giữ nguyên thứ tự mới nhất từ Server (orderBy 'Vao' desc)
        });
    });

    function showAttendanceImage(url) {
        if (!url) return;
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Ảnh Camera Hanet bắt được',
            showConfirmButton: false,
            showCloseButton: true,
            width: '600px',
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.85)',
            customClass: {
                image: 'rounded-lg max-w-full',
                popup: 'p-0 bg-transparent'
            }
        });
    }
</script>
@endpush
