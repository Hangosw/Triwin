@extends('layouts.app')

@section('title', 'Đăng ký Work From Home - Triwin')

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
            max-width: 500px;
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
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 24px;
            font-weight: bold;
        }

        .user-details h2 { font-size: 18px; font-weight: 600; margin: 0; color: #1e293b; }
        .user-details p { font-size: 14px; color: #64748b; margin: 0; }

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

        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-secondary { background: #e5e7eb; color: #374151; }

        .table-container { width: 100%; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
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
        .table td { padding: 16px 24px; border-bottom: 1px solid #e5e7eb; font-size: 14px; color: #1f2937; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #dcfce7; color: #166534; }
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
        <h1>Đăng ký Work From Home</h1>
        <p>Gửi yêu cầu và theo dõi trạng thái các đơn làm việc tại nhà của bạn</p>
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
                <button class="btn btn-primary" onclick="openWfhModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 16px; height: 16px; display: inline-block; margin-right: 8px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Đăng ký WFH mới
                </button>
            </div>
        </div>

        <div class="card" style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Lịch sử đăng ký WFH</h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Từ ngày</th>
                            <th>Đến ngày</th>
                            <th>Số ngày</th>
                            <th>Lý do</th>
                            <th>Ghi chú</th>
                            <th>Trạng thái</th>
                            <th>Ngày duyệt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myWfhs as $wfh)
                            <tr>
                                <td class="font-medium">{{ $wfh->NgayBatDau->format('d/m/Y') }}</td>
                                <td class="font-medium">{{ $wfh->NgayKetThuc->format('d/m/Y') }}</td>
                                <td><span class="badge" style="background: #eff6ff; color: #1e40af;">{{ $wfh->Ngay }} ngày</span></td>
                                <td>{{ $wfh->LyDo }}</td>
                                <td style="max-width:200px; font-size:13px; color:#6b7280;">
                                    {{ $wfh->GhiChu ?: '—' }}
                                </td>
                                <td>
                                    @if($wfh->TrangThai === 'dang_cho')
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @elseif($wfh->TrangThai === 'da_duyet')
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @elseif($wfh->TrangThai === 'tu_choi')
                                        <span class="badge badge-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td>{{ $wfh->NgayDuyet ? $wfh->NgayDuyet->format('d/m/Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                                    Bạn chưa có đơn đăng ký WFH nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- WFH Modal -->
    <div class="modal" id="wfhModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Đăng ký WFH mới</h2>
                <button class="close-modal" onclick="closeWfhModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="wfhForm" onsubmit="submitWfh(event)">
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label class="form-label">Từ ngày <span style="color: #ef4444;">*</span></label>
                            <input type="text" class="form-control datepicker" id="wfhStart" placeholder="dd/mm/yyyy" required readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Đến ngày <span style="color: #ef4444;">*</span></label>
                            <input type="text" class="form-control datepicker" id="wfhEnd" placeholder="dd/mm/yyyy" required readonly>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Lý do <span style="color: #ef4444;">*</span></label>
                        <textarea class="form-control" id="wfhReason" required placeholder="Nhập lý do đăng ký WFH..." style="min-height: 80px;"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ghi chú thêm</label>
                        <textarea class="form-control" id="wfhNote" placeholder="Ghi chú nếu có..." style="min-height: 60px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeWfhModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Gửi đơn</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let startPicker, endPicker;

        document.addEventListener('DOMContentLoaded', function () {
            startPicker = flatpickr("#wfhStart", {
                dateFormat: "d/m/Y",
                defaultDate: new Date(),
                onChange: function(selectedDates, dateStr) {
                    endPicker.set("minDate", dateStr);
                }
            });
            endPicker = flatpickr("#wfhEnd", {
                dateFormat: "d/m/Y",
                defaultDate: new Date(),
                minDate: new Date()
            });
        });

        function openWfhModal() {
            document.getElementById('wfhModal').classList.add('show');
        }

        function closeWfhModal() {
            document.getElementById('wfhModal').classList.remove('show');
            document.getElementById('wfhForm').reset();
        }

        function submitWfh(event) {
            event.preventDefault();

            const NgayBatDau = document.getElementById('wfhStart').value;
            const NgayKetThuc = document.getElementById('wfhEnd').value;
            const LyDo = document.getElementById('wfhReason').value;
            const GhiChu = document.getElementById('wfhNote').value;

            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Đang gửi...';

            fetch('{{ route("wfh.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ NgayBatDau, NgayKetThuc, LyDo, GhiChu })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Thành công', text: data.message, timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'Gửi đơn';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Lỗi hệ thống', text: 'Có lỗi xảy ra, vui lòng thử lại sau!' });
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Gửi đơn';
            });
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('wfhModal')) closeWfhModal();
        }
    </script>
@endpush
