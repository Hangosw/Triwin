@extends('layouts.app')

@section('title', 'Quản lý chấm công - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Quản lý chấm công</h1>
        <p>Theo dõi chấm công và giờ làm việc của nhân viên</p>
    </div>

    <style>
        /* Đồng bộ chiều cao tất cả các filter */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0.75rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 4px !important;
        }

        /* custom-clear-icon visibility */
        .custom-clear-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            color: #6b7280;
            transition: all 0.2s;
            cursor: pointer;
        }
        .custom-clear-icon:hover {
            color: #ef4444 !important;
            background-color: rgba(239, 68, 68, 0.1);
        }
        body.dark-theme .custom-clear-icon {
            color: #8b93a8;
        }
        body.dark-theme .custom-clear-icon:hover {
            color: #f87171 !important;
            background-color: rgba(248, 113, 113, 0.2);
        }

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

        body.dark-theme .tabs {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .tab {
            color: #8b93a8;
        }

        body.dark-theme .tab:hover {
            color: var(--primary-green);
        }

        body.dark-theme .tab.active {
            color: var(--primary-green);
            border-bottom-color: var(--primary-green);
        }
    </style>

    <!-- Filter Bar -->
    <div class="card">
        <form action="{{ route('cham-cong.danh-sach') }}" method="GET" class="action-bar" id="filterForm">
            <div style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                {{-- Ngày --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Chọn ngày</label>
                    <div class="dropdown custom-day-picker">
                        <input type="hidden" name="day" id="inputDay" value="{{ $day }}">
                        <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                            <span id="dayDropdownText" style="color: {{ $day === '' ? '#6c757d' : '#212529' }};">{{ $day !== '' ? 'Ngày ' . sprintf('%02d', $day) : 'Tất cả ngày' }}</span>
                            @if($day !== '')
                                <i class="bi bi-x-circle-fill ms-2 custom-clear-icon" style="font-size: 12px;" onclick="event.stopPropagation(); clearDayFilter();"></i>
                            @else
                                <i class="bi bi-calendar3 ms-2 text-muted" style="font-size: 14px;"></i>
                            @endif
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 260px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN NGÀY TRONG THÁNG</span>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;">
                                <button type="button" class="btn btn-sm btn-light day-preset {{ $day === '' ? 'active fw-bold text-primary' : '' }}" onclick="selectDay('')" style="grid-column: span 7; font-size: 13px; padding: 6px 0; background-color: {{ $day === '' ? '#eff6ff' : '#f3f4f6' }}; border: none; display: flex; justify-content: center; align-items: center;">Tất cả ngày</button>
                                @for($d = 1; $d <= 31; $d++)
                                    <button type="button" class="btn btn-sm {{ (string)$day === (string)$d ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}" onclick="selectDay('{{ $d }}')" style="padding: 6px 0; font-size: 13px; border-radius: 6px; border: none; background-color: {{ (string)$day === (string)$d ? '#3b82f6' : '#f9fafb' }}; color: {{ (string)$day === (string)$d ? '#fff' : '#374151' }}; transition: all 0.2s; display: flex; justify-content: center; align-items: center;" onmouseover="if('{{ $day }}' != '{{ $d }}') { this.style.backgroundColor='#e5e7eb'; }" onmouseout="if('{{ $day }}' != '{{ $d }}') { this.style.backgroundColor='#f9fafb'; }">{{ $d }}</button>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Tháng --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 140px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tháng</label>
                    <div class="dropdown custom-day-picker">
                        <input type="hidden" name="month" id="inputMonth" value="{{ $month }}">
                        <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                            <span id="monthDropdownText" style="color: {{ $month == date('m') && !request()->has('month') ? '#6c757d' : '#212529' }};">{{ $month !== '' ? 'Tháng ' . $month : 'Tất cả tháng' }}</span>
                            @if($month !== '' && request()->has('month'))
                                <i class="bi bi-x-circle-fill ms-2 custom-clear-icon" style="font-size: 12px;" onclick="event.stopPropagation(); clearFilter('inputMonth');"></i>
                            @else
                                <i class="bi bi-calendar3 ms-2 text-muted" style="font-size: 14px;"></i>
                            @endif
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 250px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN THÁNG</span>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;">
                                <button type="button" class="btn btn-sm btn-light day-preset {{ $month === '' ? 'active fw-bold text-primary' : '' }}" onclick="selectFilter('inputMonth', '')" style="grid-column: span 3; font-size: 13px; padding: 6px 0; background-color: {{ $month === '' ? '#eff6ff' : '#f3f4f6' }}; border: none; display: flex; justify-content: center; align-items: center;">Tất cả tháng</button>
                                @for($m = 1; $m <= 12; $m++)
                                    <button type="button" class="btn btn-sm {{ (string)$month === (string)$m ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}" onclick="selectFilter('inputMonth', '{{ $m }}')" style="padding: 6px 0; font-size: 13px; border-radius: 6px; border: none; background-color: {{ (string)$month === (string)$m ? '#3b82f6' : '#f9fafb' }}; color: {{ (string)$month === (string)$m ? '#fff' : '#374151' }}; transition: all 0.2s; display: flex; justify-content: center; align-items: center;" onmouseover="if('{{ $month }}' != '{{ $m }}') { this.style.backgroundColor='#e5e7eb'; }" onmouseout="if('{{ $month }}' != '{{ $m }}') { this.style.backgroundColor='#f9fafb'; }">Tháng {{ $m }}</button>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Năm --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 130px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Năm</label>
                    <div class="dropdown custom-day-picker">
                        <input type="hidden" name="year" id="inputYear" value="{{ $year }}">
                        <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                            <span id="yearDropdownText" style="color: {{ $year == date('Y') && !request()->has('year') ? '#6c757d' : '#212529' }};">{{ $year !== '' ? 'Năm ' . $year : 'Tất cả năm' }}</span>
                            @if($year !== '' && request()->has('year'))
                                <i class="bi bi-x-circle-fill ms-2 custom-clear-icon" style="font-size: 12px;" onclick="event.stopPropagation(); clearFilter('inputYear');"></i>
                            @else
                                <i class="bi bi-calendar3 ms-2 text-muted" style="font-size: 14px;"></i>
                            @endif
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 250px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN NĂM</span>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;">
                                <button type="button" class="btn btn-sm btn-light day-preset {{ $year === '' ? 'active fw-bold text-primary' : '' }}" onclick="selectFilter('inputYear', '')" style="grid-column: span 3; font-size: 13px; padding: 6px 0; background-color: {{ $year === '' ? '#eff6ff' : '#f3f4f6' }}; border: none; display: flex; justify-content: center; align-items: center;">Tất cả năm</button>
                                @for($y = 2024; $y <= 2030; $y++)
                                    <button type="button" class="btn btn-sm {{ (string)$year === (string)$y ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}" onclick="selectFilter('inputYear', '{{ $y }}')" style="padding: 6px 0; font-size: 13px; border-radius: 6px; border: none; background-color: {{ (string)$year === (string)$y ? '#3b82f6' : '#f9fafb' }}; color: {{ (string)$year === (string)$y ? '#fff' : '#374151' }}; transition: all 0.2s; display: flex; justify-content: center; align-items: center;" onmouseover="if('{{ $year }}' != '{{ $y }}') { this.style.backgroundColor='#e5e7eb'; }" onmouseout="if('{{ $year }}' != '{{ $y }}') { this.style.backgroundColor='#f9fafb'; }">{{ $y }}</button>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Trạng thái --}}
                <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                    <label class="form-label" style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Trạng thái</label>
                    <div class="dropdown custom-day-picker">
                        <input type="hidden" name="status" id="inputStatus" value="{{ $status }}">
                        <div class="form-control d-flex justify-content-between align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; height: 38px; background-color: #fff; padding: 0.375rem 0.75rem;">
                            <span id="statusDropdownText" style="color: {{ $status === '' ? '#6c757d' : '#212529' }};">
                                @if($status === 'dung_gio') Đúng giờ
                                @elseif($status === 'tre') Đi muộn
                                @elseif($status === 've_som') Về sớm
                                @elseif($status === 'la') Khách / Lạ
                                @else Tất cả trạng thái @endif
                            </span>
                            @if($status !== '')
                                <i class="bi bi-x-circle-fill ms-2 custom-clear-icon" style="font-size: 12px;" onclick="event.stopPropagation(); clearFilter('inputStatus');"></i>
                            @else
                                <i class="bi bi-list ms-2 text-muted" style="font-size: 16px;"></i>
                            @endif
                        </div>
                        <div class="dropdown-menu p-2 shadow" style="min-width: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">CHỌN TRẠNG THÁI</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                @php
                                    $statuses = [
                                        '' => 'Tất cả trạng thái',
                                        'dung_gio' => 'Đúng giờ',
                                        'tre' => 'Đi muộn',
                                        've_som' => 'Về sớm',
                                    ];
                                @endphp
                                @foreach($statuses as $val => $label)
                                    <button type="button" class="btn btn-sm {{ $status === $val ? 'btn-primary fw-bold shadow-sm' : 'btn-light' }}" onclick="selectFilter('inputStatus', '{{ $val }}')" style="padding: 8px 12px; font-size: 13px; border-radius: 6px; border: none; background-color: {{ $status === $val ? '#3b82f6' : '#f9fafb' }}; color: {{ $status === $val ? '#fff' : '#374151' }}; transition: all 0.2s; display: flex; justify-content: flex-start; align-items: center;" onmouseover="if('{{ $status }}' != '{{ $val }}') { this.style.backgroundColor='#e5e7eb'; }" onmouseout="if('{{ $status }}' != '{{ $val }}') { this.style.backgroundColor='#f9fafb'; }">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Hidden input for Tab --}}
                <input type="hidden" name="tab" id="inputTab" value="{{ $tab }}">
                {{-- Xóa bộ lọc nhanh --}}
                @if($day !== '' || $status !== '' || request()->has('month') || request()->has('year'))
                    <div class="form-group" style="margin-bottom: 0;">
                        <a href="{{ route('cham-cong.danh-sach') }}" class="btn-clear-filter">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
{{-- 
                <button type="button" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
--}}
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

    <!-- Tabs -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 0 20px;">
            <div class="tabs" style="margin-bottom: 0;">
                <button class="tab {{ $tab === 'nhan_vien' ? 'active' : '' }}" onclick="switchTab('nhan_vien')">
                    Nhân viên ({{ $employeeTabCount }})
                </button>
                <button class="tab {{ $tab === 'khach' ? 'active' : '' }}" onclick="switchTab('khach')">
                    Khách / Người lạ ({{ $guestTabCount }})
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="attendanceTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th style="width: 220px;">Nhân viên</th>
                        <th style="width: 110px;">Loại</th>
                        <th style="width: 180px;">Phòng ban</th>
                        <th style="width: 110px;">Ngày</th>
                        <th style="width: 100px;">Giờ vào</th>
                        <th style="width: 100px;">Giờ ra</th>
                        <th style="width: 80px;">Công</th>
                        <th style="width: 120px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $index => $att)
                        <tr>
                            <td class="text-center"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="avatar"
                                        style="width: 40px; height: 40px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #0BAA4B; font-weight: bold; overflow: hidden;">
                                        @if($att->AnhChamCong)
                                            <img src="{{ asset($att->AnhChamCong) }}" alt="Captured" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="showAttendanceImage('{{ asset($att->AnhChamCong) }}')">
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
        const table = $('#attendanceTable').DataTable({
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
            order: [], // Bỏ sort mặc định để giữ nguyên thứ tự mới nhất từ Server (orderBy 'Vao' desc)
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    className: 'text-center'
                }
            ]
        });

        // Đảm bảo số thứ tự luôn bắt đầu từ 1 khi sort hoặc search
        table.on('order.dt search.dt', function () {
            table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1;
            } );
        }).draw();
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
<script>
    function selectFilter(inputId, val) {
        document.getElementById(inputId).value = val;
        document.getElementById('filterForm').submit();
    }
    
    function clearFilter(inputId) {
        document.getElementById(inputId).value = '';
        document.getElementById('filterForm').submit();
    }

    // Keep Day compat
    function selectDay(day) { selectFilter('inputDay', day); }
    function clearDayFilter() { clearFilter('inputDay'); }

    function switchTab(tab) {
        document.getElementById('inputTab').value = tab;
        // Khi chuyển tab, có thể muốn reset status filter vì status 'la' không còn nữa
        if (tab === 'khach') {
            document.getElementById('inputStatus').value = '';
        }
        document.getElementById('filterForm').submit();
    }
    
    // Custom select2 height normalization to perfectly match the custom day picker (if any other select2 exists on page)
    $(document).ready(function() {
        if ($('.select2-container').length) {
            $('.select2-selection--single').css('height', '38px');
            $('.select2-selection__rendered').css('line-height', '36px');
        }
    });
</script>
@endpush

