@extends('layouts.app')

@section('title', 'Chấm công cá nhân - Vietnam Rubber Group')

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

        .user-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid #e2e8f0;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            background: #0BAA4B;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 24px;
            font-weight: bold;
        }

        .user-details h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #1e293b;
        }

        .user-details p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .status-badge {
            margin-top: 8px;
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Chấm công cá nhân</h1>
        <p>Ghi nhận thời gian làm việc hàng ngày của bạn</p>
    </div>

    @if(isset($error))
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @else
        <div class="user-card">
            <div class="user-avatar">
                {{ substr($nhanVien->Ten, 0, 1) }}
            </div>
            <div class="user-details">
                <h2>{{ $nhanVien->Ten }}</h2>
                <p>Mã nhân viên: <strong>{{ $nhanVien->Ma }}</strong></p>
                @if($latestAttendance)
                    <div class="status-badge">
                        Trạng thái:
                        @if(!$latestAttendance->Ra)
                            <span class="badge badge-primary">Đang làm việc ({{ $latestAttendance->Loai == 1 ? 'Tăng ca' : 'Hành chính' }})</span>
                        @else
                            @if($approvedOT && $latestAttendance->Loai == 0)
                                <span class="badge badge-warning">Đã xong ca HC - Chờ vào ca Tăng ca</span>
                            @else
                                <span class="badge badge-success">Đã hoàn thành công việc</span>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="status-badge">
                        Trạng thái: <span class="badge badge-gray">Chưa chấm công</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="clock-container">
            <div id="live-clock">00:00:00</div>
            <div id="live-date">...</div>

            <div class="attendance-actions">
                @if(!$latestAttendance || !$latestAttendance->Ra || ($latestAttendance->Ra && $approvedOT && $latestAttendance->Loai == 0))
                    <div class="action-card in" onclick="submitAttendance()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <h3>
                            @if(!$latestAttendance)
                                Chấm công VÀO
                            @elseif($latestAttendance->Ra && $approvedOT)
                                Chấm công VÀO TĂNG CA
                            @else
                                Chấm công RA
                            @endif
                        </h3>
                        <p>Nhấp vào đây để ghi nhận thời gian</p>
                    </div>
                @else
                    <div class="alert alert-success" style="width: 100%; max-width: 400px; margin: 0 auto;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="width: 24px; height: 24px; display: inline-block; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Bạn đã hoàn thành chấm công cho ngày hôm nay.
                    </div>
                @endif
            </div>
        </div>

        @if($todayAttendances->count() > 0)
            <div class="recent-activity"
                style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Chi tiết chấm công hôm nay</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Loại</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayAttendances as $att)
                        <tr>
                            <td>
                                @if($att->Loai == 1)
                                    <span class="badge" style="background:#6366f1; color:white;">Tăng ca</span>
                                @else
                                    <span class="badge" style="background:#3b82f6; color:white;">Hành chính</span>
                                @endif
                            </td>
                            <td>{{ $att->Vao->format('H:i:s') }}</td>
                            <td>{{ $att->Ra ? $att->Ra->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($att->TrangThai === 'dung_gio')
                                    <span class="badge badge-success">Đúng giờ</span>
                                @elseif($att->TrangThai === 'tre')
                                    <span class="badge badge-warning">Đi muộn</span>
                                @elseif($att->TrangThai === 've_som')
                                    <span class="badge badge-orange">Về sớm</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('live-clock');
            const dateEl = document.getElementById('live-date');

            if (clock) clock.textContent = now.toLocaleTimeString('vi-VN');

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            if (dateEl) dateEl.textContent = now.toLocaleDateString('vi-VN', options);
        }

        setInterval(updateClock, 1000);
        updateClock();

        let stream = null;

        async function submitAttendance() {
            try {
                // Check for camera support
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    Swal.fire('Lỗi', 'Trình duyệt của bạn không hỗ trợ truy cập camera.', 'error');
                    return;
                }

                // Request camera permission and show preview in Swal
                const result = await Swal.fire({
                    title: 'Chụp ảnh chấm công',
                    html: `
                        <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; background: #000; border-radius: 8px; overflow: hidden; aspect-ratio: 4/3;">
                            <video id="attendance-video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                            <canvas id="attendance-canvas" style="display: none;"></canvas>
                        </div>
                        <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">Vui lòng giữ khung hình rõ mặt để chấm công</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Chụp ảnh & Chấm công',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#0BAA4B',
                    didOpen: async () => {
                        try {
                            const video = document.getElementById('attendance-video');
                            stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { 
                                    facingMode: "user",
                                    width: { ideal: 640 },
                                    height: { ideal: 480 }
                                } 
                            });
                            video.srcObject = stream;
                        } catch (err) {
                            console.error("Camera error:", err);
                            Swal.showValidationMessage(`Không thể mở camera: ${err.message}`);
                        }
                    },
                    willClose: () => {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }
                    },
                    preConfirm: () => {
                        const video = document.getElementById('attendance-video');
                        const canvas = document.getElementById('attendance-canvas');
                        
                        if (!video.srcObject) {
                            Swal.showValidationMessage('Vui lòng đợi camera sẵn sàng');
                            return false;
                        }

                        // Capture photo
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        const context = canvas.getContext('2d');
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        return canvas.toDataURL('image/jpeg', 0.8);
                    }
                });

                if (result.isConfirmed) {
                    const imageData = result.value;
                    
                    Swal.fire({
                        title: 'Đang xử lý...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route("cham-cong.ca-nhan.post") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            anh_cham_cong: imageData
                        })
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
            } catch (error) {
                console.error("Attendance process error:", error);
                Swal.fire('Lỗi', 'Có lỗi xảy ra trong quá trình chấm công.', 'error');
            }
        }
    </script>
@endpush
