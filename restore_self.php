<?php
$content = <<<'EOD'
@extends('layouts.app')

@section('title', 'Đăng ký tăng ca cá nhân')

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-card .label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #0BAA4B;
        }

        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #0BAA4B;
            color: white;
        }

        .btn-primary:hover {
            background: #098a3d;
            transform: translateY(-1px);
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9fafb;
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #1f2937;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success { background: #dcfce7; color: #088c3d; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal.show { display: flex; }

        .modal-content {
            background: white; width: 500px; border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }

        .modal-header {
            padding: 24px; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
            display: flex; justify-content: space-between; align-items: center;
        }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 10px 14px; border-radius: 10px;
            border: 1px solid #d1d5db; font-size: 14px;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Đăng ký tăng ca cá nhân</h1>
        <p>Gửi yêu cầu và theo dõi trạng thái các đơn tăng ca của bạn</p>
    </div>

    @if(isset($error))
        <div class="card" style="background: #fef2f2; border: 1px solid #fecaca; padding: 24px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; color: #dc2626;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span style="font-weight: 500;">{{ $error }}</span>
            </div>
            <p style="margin-top: 12px; color: #7f1d1d; font-size: 14px;">
                Vui lòng liên hệ quản trị viên để được hỗ trợ liên kết hồ sơ.
            </p>
        </div>
    @else
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Tăng ca tháng này</div>
                <div class="value">{{ $myOvertimes->where('TrangThai', 'da_duyet')->sum('Tong') }}h</div>
            </div>
            <div class="stat-card">
                <div class="label">Đơn chờ duyệt</div>
                <div class="value" style="color: #f59e0b;">{{ $myOvertimes->where('TrangThai', 'dang_cho')->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Mã nhân viên</div>
                <div class="value" style="color: #3b82f6; font-size: 24px;">{{ $nhanVien->Ma }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lịch sử đăng ký tăng ca</h3>
                <button class="btn btn-primary" onclick="openOvertimeModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký tăng ca
                </button>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Thời gian</th>
                            <th>Tổng giờ</th>
                            <th>Lý do</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myOvertimes as $ot)
                            <tr>
                                <td class="font-medium">{{ $ot->Ngay->format('d/m/Y') }}</td>
                                <td>{{ substr($ot->BatDau, 0, 5) }} - {{ substr($ot->KetThuc, 0, 5) }}</td>
                                <td class="font-medium">{{ $ot->Tong }}h</td>
                                <td>{{ Str::limit($ot->LyDo, 50) }}</td>
                                <td>
                                    @if($ot->TrangThai === 'dang_cho')
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @elseif($ot->TrangThai === 'da_duyet')
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @else
                                        <span class="badge badge-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ot->TrangThai === 'tu_choi' && ($ot->Dem ?? 0) < 3)
                                        <button class="btn" style="padding:4px 8px; font-size:12px; background:#f3f4f6;" 
                                                onclick="resubmitOvertime({{ $ot->id }})">Gửi lại</button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                                    Bạn chưa có đơn đăng ký tăng ca nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Registration Modal -->
    <div class="modal" id="overtimeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 20px; font-weight: 700;">Đăng ký tăng ca</h2>
                <button onclick="closeOvertimeModal()" style="border: none; background: none; cursor: pointer;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="overtimeForm" onsubmit="submitOvertime(event)">
                @csrf
                <div style="padding: 24px;">
                    <div class="form-group">
                        <label class="form-label">Ngày tăng ca <span style="color: #ef4444;">*</span></label>
                        <input type="date" class="form-control" name="Ngay" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Giờ bắt đầu <span style="color: #ef4444;">*</span></label>
                            <input type="time" class="form-control" name="BatDau" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Giờ kết thúc <span style="color: #ef4444;">*</span></label>
                            <input type="time" class="form-control" name="KetThuc" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lý do tăng ca <span style="color: #ef4444;">*</span></label>
                        <textarea class="form-control" name="LyDo" rows="3" required placeholder="Nhập lý do tăng ca..."></textarea>
                    </div>
                </div>
                <div style="padding: 16px 24px; background: #f9fafb; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn" style="background: #e5e7eb; color: #374151;" onclick="closeOvertimeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đăng ký</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openOvertimeModal() { document.getElementById('overtimeModal').classList.add('show'); }
        function closeOvertimeModal() { document.getElementById('overtimeModal').classList.remove('show'); document.getElementById('overtimeForm').reset(); }

        function submitOvertime(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());

            fetch('{{ route("tang-ca.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Thành công', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Lỗi', res.message, 'error');
                }
            })
            .catch(() => Swal.fire('Lỗi', 'Có lỗi xảy ra, vui lòng thử lại sau.', 'error'));
        }

        function resubmitOvertime(id) {
            Swal.fire({
                title: 'Gửi lại yêu cầu?',
                text: 'Hệ thống sẽ cập nhật trạng thái đơn thành "Đang chờ"',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0BAA4B',
                confirmButtonText: 'Đồng ý'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tang-ca/yeu-cau-lai/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) Swal.fire('Thành công', res.message, 'success').then(() => location.reload());
                        else Swal.fire('Lỗi', res.message, 'error');
                    });
                }
            });
        }
    </script>
@endpush
EOD;

file_put_contents('c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\self.blade.php', $content);
echo "Successfully restored self.blade.php\n";
