@extends('layouts.app')

@section('title', 'Đăng ký tăng ca - Triwin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .close-modal {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #1f2937;
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

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
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
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fecaca; padding: 20px; border-radius: 12px; color: #dc2626; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span style="font-weight: 600;">{{ $error }}</span>
            </div>
        </div>
    @else
        <div class="user-card">
            <div class="user-avatar">
                {{ substr($nhanVien->Ten, 0, 1) }}
            </div>
            <div class="user-details">
                <h2>{{ $nhanVien->Ten }}</h2>
                <p>Mã nhân viên: <strong>{{ $nhanVien->Ma }}</strong></p>
                <p>Phòng ban: <strong>{{ $nhanVien->ttCongViec->phongBan->Ten ?? 'N/A' }}</strong></p>
            </div>
            <div style="margin-left: auto;">
                <button class="btn btn-primary" onclick="openOvertimeModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 16px; height: 16px; display: inline-block; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký tăng ca mới
                </button>
            </div>
        </div>

        <div class="card" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Lịch sử đăng ký tăng ca</h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Giờ bắt đầu</th>
                            <th>Giờ kết thúc</th>
                            <th>Tổng giờ</th>
                            <th>Lý do</th>
                            <th>Ghi chú lãnh đạo</th>
                            <th style="text-align: center;">Lần gửi</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myOvertimes as $ot)
                            <tr>
                                <td class="font-medium">{{ $ot->Ngay->format('d/m/Y') }}</td>
                                <td>{{ substr($ot->BatDau, 0, 5) }}</td>
                                <td>{{ substr($ot->KetThuc, 0, 5) }}</td>
                                <td class="font-medium" style="color: #0BAA4B;">{{ $ot->Tong }}h</td>
                                <td>{{ $ot->LyDo }}</td>
                                <td style="max-width:200px; font-size:13px; color:#6b7280;">
                                    @if($ot->GhiChuLanhDao)
                                        {!! $ot->GhiChuLanhDao !!}
                                    @else
                                        <span style="color:#d1d5db;">&#8212;</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-weight:600;">{{ $ot->Dem ?? 1 }}</span>/3
                                </td>
                                <td>
                                    @if($ot->TrangThai === 'dang_cho')
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @elseif($ot->TrangThai === 'da_duyet')
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @elseif($ot->TrangThai === 'tu_choi')
                                        <span class="badge badge-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ot->TrangThai === 'tu_choi')
                                        @if(($ot->Dem ?? 1) < 3)
                                            <button class="btn btn-primary" style="font-size:12px; padding:6px 12px;"
                                                onclick="openReRequestModal({{ $ot->id }}, {{ $ot->Dem ?? 1 }})">
                                                <i class="bi bi-arrow-clockwise"></i> Yêu cầu lại
                                            </button>
                                        @else
                                            <span class="badge badge-danger" style="font-size:11px;">Hết lượt</span>
                                        @endif
                                    @else
                                        <span style="color:#d1d5db;">&#8212;</span>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px; color: #6b7280;">
                                    Bạn chưa có đơn đăng ký tăng ca nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Overtime Modal -->
    <div class="modal" id="overtimeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Đăng ký tăng ca mới</h2>
                <button class="close-modal" onclick="closeOvertimeModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="overtimeForm" onsubmit="submitOvertime(event)">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Ngày tăng ca <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-control datepicker" id="overtimeDate" placeholder="dd/mm/yyyy"
                            required readonly>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label class="form-label">Giờ bắt đầu <span style="color: #ef4444;">*</span></label>
                            <input type="time" class="form-control" id="overtimeStart" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Giờ kết thúc <span style="color: #ef4444;">*</span></label>
                            <input type="time" class="form-control" id="overtimeEnd" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lý do tăng ca <span style="color: #ef4444;">*</span></label>
                        <textarea class="form-control" id="overtimeReason" required placeholder="Nhập lý do cần tăng ca..."
                            style="min-height: 100px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeOvertimeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Gửi đơn</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Yêu cầu lại --}}
    <div class="modal" id="reRequestModal">
        <div class="modal-content" style="max-width:480px;">
            <div class="modal-header">
                <h2>Yêu cầu xét duyệt lại</h2>
                <button class="close-modal" onclick="closeReRequestModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px;height:24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="reRequestInfo"
                    style="background:#fef9c3; border:1px solid #fde047; border-radius:8px; padding:12px; font-size:13px; margin-bottom:16px; color:#a16207;">
                </div>
                <div class="form-group">
                    <label class="form-label">Lý do yêu cầu lại <span style="color:#ef4444;">*</span></label>
                    <textarea class="form-control" id="reRequestReason"
                        placeholder="Nhập lý do bổ sung hoặc giải trình thêm..." style="min-height:100px;"
                        required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeReRequestModal()">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnReRequest" onclick="submitReRequest()">
                    <i class="bi bi-arrow-clockwise"></i> Gửi yêu cầu lại
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let datePicker;

        document.addEventListener('DOMContentLoaded', function () {
            datePicker = flatpickr("#overtimeDate", {
                dateFormat: "d/m/Y",
                altInput: false,
                defaultDate: new Date()
            });
        });

        function openOvertimeModal() {
            document.getElementById('overtimeModal').classList.add('show');
            if (datePicker) {
                datePicker.setDate(new Date());
            }
        }

        function closeOvertimeModal() {
            document.getElementById('overtimeModal').classList.remove('show');
            document.getElementById('overtimeForm').reset();
        }

        function submitOvertime(event) {
            event.preventDefault();

            const NgayRaw = document.getElementById('overtimeDate').value; // d/m/Y
            // Chuyển đổi d/m/Y sang Y-m-d cho backend
            const parts = NgayRaw.split('/');
            const Ngay = `${parts[2]}-${parts[1]}-${parts[0]}`;

            const BatDau = document.getElementById('overtimeStart').value;
            const KetThuc = document.getElementById('overtimeEnd').value;
            const LyDo = document.getElementById('overtimeReason').value;

            if (BatDau >= KetThuc) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Giờ kết thúc phải sau giờ bắt đầu!',
                    confirmButtonColor: '#0BAA4B'
                });
                return;
            }

            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';

            fetch('{{ route("tang-ca.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ Ngay, BatDau, KetThuc, LyDo })
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
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Gửi đơn';
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
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'Gửi đơn';
                });
        }


        // ==================== YEU CAU LAI ====================
        let currentReRequestId = null;

        function openReRequestModal(id, soLanHienTai) {
            currentReRequestId = id;
            const conLai = 3 - soLanHienTai;
            document.getElementById('reRequestInfo').innerHTML =
                `Đơn đã bị từ chối <strong>${soLanHienTai}</strong> lần. Còn <strong>${conLai}</strong> lần yêu cầu lại.`;
            document.getElementById('reRequestReason').value = '';
            document.getElementById('reRequestModal').classList.add('show');
        }

        function closeReRequestModal() {
            document.getElementById('reRequestModal').classList.remove('show');
            currentReRequestId = null;
        }

        function submitReRequest() {
            const lyDo = document.getElementById('reRequestReason').value.trim();
            if (!lyDo) {
                Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập lý do yêu cầu lại.', confirmButtonColor: '#0BAA4B' });
                return;
            }
            const btn = document.getElementById('btnReRequest');
            btn.disabled = true;
            btn.textContent = 'Đang gửi...';

            fetch('/tang-ca/yeu-cau-lai/' + currentReRequestId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ LyDo: lyDo })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Đã gửi!', text: data.message, timer: 2000, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message, confirmButtonColor: '#0BAA4B' });
                        btn.disabled = false;
                        btn.textContent = 'Gửi yêu cầu lại';
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Lỗi hệ thống', confirmButtonColor: '#0BAA4B' });
                    btn.disabled = false;
                    btn.textContent = 'Gửi yêu cầu lại';
                });
        }

        // Close modal on outside click
        window.onclick = function(event) {
            if (event.target == document.getElementById('overtimeModal')) closeOvertimeModal();
            if (event.target == document.getElementById('reRequestModal')) closeReRequestModal();
        }
    </script>
@endpush