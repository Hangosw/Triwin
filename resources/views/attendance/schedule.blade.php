@extends('layouts.app')

@section('title', 'Lịch làm việc - Vietnam Rubber Group')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700; color: #1f2937;">Lịch làm việc</h1>
            <p class="text-muted" style="color: #6b7280; font-size: 14px;">Bảng theo dõi lịch làm việc và chấm công tháng
                {{ $month }}/{{ $year }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <form action="{{ route('cham-cong.schedule') }}" method="GET" class="d-flex gap-2 align-items-center">
                <select name="month" class="form-select mx-1" onchange="this.form.submit()" style="min-width: 120px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
                <select name="year" class="form-select mx-1" onchange="this.form.submit()" style="min-width: 120px;">
                    @for($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </form>
            <button class="btn btn-primary" id="btn-save-schedule">
                <i class="bi bi-save"></i> Lưu lịch
            </button>
            <button class="btn btn-secondary">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive schedule-container">
                <table class="table table-bordered schedule-table mb-0">
                    <thead>
                        <tr>
                            <th class="sticky-col first-col bg-light align-middle text-center"
                                style="min-width: 250px; z-index: 10;">
                                Tổ Đội
                            </th>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $date = Carbon\Carbon::createFromDate($year, $month, $d);
                                    $isWeekend = $date->isWeekend();
                                @endphp
                                <th class="text-center align-middle {{ $isWeekend ? 'weekend-day' : '' }}"
                                    style="min-width: 50px;">
                                    <div class="day-num">{{ $d }}</div>
                                    <div class="day-name">{{ compactVietnameseDay($date->dayOfWeek) }}</div>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($toDois as $td)
                            <tr>
                                <td class="sticky-col first-col bg-white">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ mb_substr($td->TenToDoi, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $td->TenToDoi }}</div>
                                            <div class="text-muted small">
                                                {{ $td->phongBan->Ten ?? 'Chưa phân công' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $date = Carbon\Carbon::createFromDate($year, $month, $d);
                                        $isWeekend = $date->isWeekend();
                                        $isToday = $date->isToday();
                                        $dateStr = $date->format('Y-m-d');
                                        $currentCa = $scheduleMap[$td->id][$dateStr] ?? '';
                                    @endphp

                                    <td
                                        class="text-center schedule-cell p-0 {{ $isWeekend ? 'weekend-bg' : '' }} {{ $isToday ? 'today-bg' : '' }}">
                                        <select class="schedule-select" data-team-id="{{ $td->id }}" data-date="{{ $dateStr }}">
                                            <option value="" {{ $currentCa === '' ? 'selected' : '' }}></option>
                                            @foreach($caLamViecs as $ca)
                                                <option value="{{ $ca->MaCa }}" {{ $currentCa === $ca->MaCa ? 'selected' : '' }}>
                                                    {{ $ca->MaCa }}</option>
                                            @endforeach
                                            <option value="OFF" {{ $currentCa === 'OFF' ? 'selected' : '' }}>OFF</option>
                                        </select>
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 1 }}" class="text-center p-5 text-muted">
                                    Không có dữ liệu Tổ Đội
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="d-flex mt-3 gap-4 align-items-center text-muted small px-2">
        <span>
            <div class="d-inline-block bg-shift-ca1"
                style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #e5e7eb; vertical-align: middle;">
            </div> Hành chính / CA1
        </span>
        <span>
            <div class="d-inline-block bg-shift-ca2"
                style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #e5e7eb; vertical-align: middle;">
            </div> CA2
        </span>
        <span>
            <div class="d-inline-block bg-shift-ca3"
                style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #e5e7eb; vertical-align: middle;">
            </div> CA3 (Đêm)
        </span>
        <span>
            <div class="d-inline-block bg-shift-off"
                style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #e5e7eb; vertical-align: middle;">
            </div> Nghỉ (OFF)
        </span>
        <span>
            <div class="d-inline-block weekend-bg"
                style="width: 15px; height: 15px; border-radius: 3px; border: 1px solid #e5e7eb; vertical-align: middle;">
            </div> Cuối tuần
        </span>
    </div>

@endsection

@php
    function compactVietnameseDay($dayOfWeek)
    {
        $map = [
            0 => 'CN',
            1 => 'T2',
            2 => 'T3',
            3 => 'T4',
            4 => 'T5',
            5 => 'T6',
            6 => 'T7'
        ];
        return $map[$dayOfWeek] ?? '';
    }
@endphp

@push('styles')
    <style>
        .schedule-container {
            max-height: 70vh;
            overflow: auto;
        }

        .schedule-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .schedule-table th,
        .schedule-table td {
            border-right: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
            padding: 10px;
        }

        .schedule-table th {
            border-top: 1px solid #f3f4f6;
            position: sticky;
            top: 0;
            z-index: 5;
            background-color: #f8f9fa;
            color: #4b5563;
            font-weight: 600;
            font-size: 13px;
        }

        /* Sticky First Column */
        .sticky-col.first-col {
            position: sticky;
            left: 0;
            z-index: 6;
            /* Higher than other columns */
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
            border-right: 2px solid #e5e7eb;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #d1fae5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .day-num {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }

        .day-name {
            font-size: 11px;
            color: #6b7280;
        }

        .weekend-day {
            color: #dc2626 !important;
        }

        .weekend-day .day-num,
        .weekend-day .day-name {
            color: #dc2626 !important;
        }

        .weekend-bg {
            background-color: #fef2f2 !important;
        }

        .today-bg {
            background-color: #f0fdf4 !important;
            border: 2px solid #10b981 !important;
        }

        .schedule-cell {
            vertical-align: middle;
            transition: background-color 0.2s;
            position: relative;
        }

        .schedule-select {
            width: 100%;
            height: 100%;
            min-height: 48px;
            /* Taller cells to look like continuous blocks */
            border: none;
            background-color: transparent;
            text-align: center;
            text-align-last: center;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            font-weight: 500;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
            padding: 0;
            border-radius: 0;
            transition: all 0.2s;
        }

        .schedule-select:focus {
            outline: none;
            box-shadow: inset 0 0 0 2px #0BAA4B;
        }

        .is-modified::after {
            content: '';
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #f59e0b; /* amber for changed status */
            border-radius: 50%;
            z-index: 10;
        }

        /* Shift Color Coding */
        .bg-shift-ca1 {
            background-color: #dcfce7 !important;
            color: #088c3d !important;
        }

        /* Light Green */
        .bg-shift-ca2 {
            background-color: #fef9c3 !important;
            color: #854d0e !important;
        }

        /* Light Yellow */
        .bg-shift-ca3 {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }

        /* Light Red/Orange */
        .bg-shift-hc {
            background-color: #dcfce7 !important;
            color: #088c3d !important;
        }

        /* Light Green */
        .bg-shift-off {
            background-color: #f3f4f6 !important;
            color: #4b5563 !important;
        }

        /* Gray */

        .schedule-select option {
            background-color: white;
            /* Resets dropdown menu bg */
            color: #1f2937;
        }

        /* Custom scrollbar for table container */
        .schedule-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .schedule-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .schedule-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .schedule-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all select colors on load
            document.querySelectorAll('.schedule-select').forEach(select => {
                updateSelectColor(select);

                // Listen for change event to update color dynamically and mark modified
                select.addEventListener('change', function () {
                    updateSelectColor(this);
                    this.closest('td').classList.add('is-modified');
                });
            });

            // Save button click
            if (document.getElementById('btn-save-schedule')) {
                document.getElementById('btn-save-schedule').addEventListener('click', function(e) {
                    e.preventDefault();
                    const btn = this;
                    const originalHtml = btn.innerHTML;
                    
                    let schedules = [];
                    // collect all modified cells
                    document.querySelectorAll('td.is-modified .schedule-select').forEach(select => {
                        schedules.push({
                            ToDoiId: select.dataset.teamId,
                            NgayLamViec: select.dataset.date,
                            CaId: select.value
                        });
                    });

                    if (schedules.length === 0) {
                        alert('Chưa có sự thay đổi nào để lưu.');
                        return;
                    }

                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
                    btn.disabled = true;

                    fetch("{{ route('cham-cong.schedule.save') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ schedules: schedules })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(!data.success) {
                            console.error('Error:', data.message);
                            alert('Lỗi: ' + data.message);
                        } else {
                            // Flash light green for success on modified cells
                            document.querySelectorAll('td.is-modified').forEach(td => {
                                const originalBg = td.style.backgroundColor;
                                td.style.backgroundColor = '#d1fae5';
                                setTimeout(() => {
                                    td.style.backgroundColor = originalBg;
                                }, 400);
                                td.classList.remove('is-modified');
                            });
                            alert('Lưu lịch làm việc thành công!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi lưu lịch làm việc.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    });
                });
            }
        });

        function updateSelectColor(selectAttr) {
            const val = selectAttr.value;
            const parentCell = selectAttr.closest('td');

            // Remove existing background classes
            parentCell.classList.remove('bg-shift-ca1', 'bg-shift-ca2', 'bg-shift-ca3', 'bg-shift-hc', 'bg-shift-off');
            selectAttr.classList.remove('bg-shift-ca1', 'bg-shift-ca2', 'bg-shift-ca3', 'bg-shift-hc', 'bg-shift-off');

            // Add new background class based on selection
            if (val) {
                let colorClass = '';
                const valUpper = val.toUpperCase();

                if (valUpper.includes('CA1')) {
                    colorClass = 'bg-shift-ca1';
                } else if (valUpper.includes('CA2')) {
                    colorClass = 'bg-shift-ca2';
                } else if (valUpper.includes('CA3')) {
                    colorClass = 'bg-shift-ca3';
                } else if (valUpper === 'HC') {
                    colorClass = 'bg-shift-hc';
                } else if (valUpper === 'OFF') {
                    colorClass = 'bg-shift-off';
                }

                if (colorClass) {
                    // Apply class directly to parent td to ensure full-cell coloring
                    selectAttr.classList.add(colorClass);
                    parentCell.classList.add(colorClass);
                }
            }
        }
    </script>
@endpush
