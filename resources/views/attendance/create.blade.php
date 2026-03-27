@extends('layouts.app')

@section('title', 'Chấm công - Vietnam Rubber Group')

@push('styles')
    <style>
        .clock-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 32px;
        }

        #live-clock {
            font-size: 64px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
            font-family: 'Courier New', Courier, monospace;
        }

        #live-date {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 32px;
        }

        .attendance-actions {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 24px;
        }

        .action-card {
            flex: 1;
            max-width: 300px;
            padding: 32px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            text-align: center;
        }

        .action-card.in {
            background-color: #f0fdf4;
            color: #088c3d;
        }

        .action-card.in:hover {
            border-color: #22c55e;
            transform: translateY(-4px);
        }

        .action-card.out {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .action-card.out:hover {
            border-color: #ef4444;
            transform: translateY(-4px);
        }

        .action-card svg {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
        }

        .action-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .action-card p {
            font-size: 14px;
            opacity: 0.8;
        }

        .recent-activity {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Hệ thống chấm công</h1>
        <p>Ghi nhận thời gian ra vào của nhân viên</p>
    </div>

    <div class="clock-container">
        <div id="live-clock">00:00:00</div>
        <div id="live-date">Thứ Năm, ngày 12 tháng 02 năm 2026</div>

        <div style="max-width: 500px; margin: 0 auto;">
            <div class="form-group">
                <label class="form-label" style="text-align: left;">Chọn nhân viên</label>
                <select id="nhanVienSelect" class="form-control">
                    <option value="">-- Chọn nhân viên để chấm công --</option>
                    @foreach($nhanViens as $nv)
                        <option value="{{ $nv->id }}">{{ $nv->Ma }} - {{ $nv->Ten }}</option>
                    @endforeach
                </select>
            </div>

            <div class="attendance-actions">
                <div class="action-card in" onclick="submitAttendance()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <h3>Chấm công</h3>
                    <p>Ghi nhận giờ Vào hoặc giờ Ra</p>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-activity">
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #374151;">Hoạt động gần đây (Hôm nay)</h2>
        <div id="activity-list">
            @forelse($todayAttendances as $att)
                <div class="activity-item">
                    <div class="employee-info">
                        <div class="avatar"
                            style="width: 36px; height: 36px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #0BAA4B; font-weight: bold; overflow: hidden;">
                            @if($att->nhanVien)
                                {{ substr($att->nhanVien->Ten, 0, 1) }}
                            @elseif($att->AnhChamCong)
                                <img src="{{ $att->AnhChamCong }}" alt="Stranger" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                L
                            @endif
                        </div>
                        <div>
                            <div class="font-medium">{{ $att->nhanVien ? $att->nhanVien->Ten : 'Người lạ' }}</div>
                            <div class="text-gray" style="font-size: 12px;">{{ $att->nhanVien ? $att->nhanVien->Ma : '-' }}</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div class="font-medium">
                            {{ $att->Vao->format('H:i') }}
                            @if($att->Ra)
                                - {{ $att->Ra->format('H:i') }}
                            @endif
                        </div>
                        <div>
                            @if(!$att->nhanVien)
                                <span class="badge badge-danger" style="background-color: #ef4444; color: white;">Khách / Lạ</span>
                            @elseif($att->TrangThai === 'dung_gio')
                                <span class="badge badge-success">Đúng giờ</span>
                            @elseif($att->TrangThai === 'tre')
                                <span class="badge badge-warning">Đi muộn</span>
                            @elseif($att->TrangThai === 've_som')
                                <span class="badge badge-orange">Về sớm</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: #6b7280; text-align: center; padding: 20px;">Chưa có dữ liệu chấm công hôm nay</p>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('live-clock');
            const dateEl = document.getElementById('live-date');

            clock.textContent = now.toLocaleTimeString('vi-VN');

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('vi-VN', options);
        }

        setInterval(updateClock, 1000);
        updateClock();

        function submitAttendance() {
            const nhanVienId = document.getElementById('nhanVienSelect').value;

            if (!nhanVienId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chú ý',
                    text: 'Vui lòng chọn nhân viên trước!',
                    confirmButtonColor: '#0BAA4B'
                });
                return;
            }

            Swal.fire({
                title: 'Đang xử lý...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("cham-cong.tao") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ nhan_vien_id: nhanVienId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                            confirmButtonColor: '#0BAA4B'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi hệ thống',
                        text: 'Có lỗi xảy ra, vui lòng thử lại sau!',
                        confirmButtonColor: '#0BAA4B'
                    });
                });
        }
    </script>
@endpush
