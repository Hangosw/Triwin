@extends('layouts.app')

@section('title', 'Nghỉ phép cá nhân')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-green: #0BAA4B;
            --primary-gradient: linear-gradient(135deg, #0BAA4B 0%, #059669 100%);
            --secondary-green: #D1E7DD;
            --text-main: #111827;
            --text-muted: #6b7280;
            --surface: #ffffff;
            --bg-main: #f9fafb;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--surface);
            padding: 28px;
            border-radius: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle at 100% 0%, var(--secondary-green), transparent 70%);
            opacity: 0.3;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--secondary-green);
        }

        .stat-card .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .stat-card.total .icon-box { background: #ecfdf5; color: #10b981; }
        .stat-card.used .icon-box { background: #fef2f2; color: #ef4444; }
        .stat-card.remaining .icon-box { background: #eff6ff; color: #3b82f6; }

        .stat-card .label {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .stat-card .value {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .stat-card.total .value { color: #059669; }
        .stat-card.used .value { color: #dc2626; }
        .stat-card.remaining .value { color: #2563eb; }

        .stat-card .unit {
            font-size: 16px;
            font-weight: 500;
            margin-left: 4px;
            color: var(--text-muted);
        }

        .card {
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid #f3f4f6;
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .leave-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .leave-type-item {
            padding: 20px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: var(--transition);
        }

        .leave-type-item:hover {
            background: white;
            border-color: var(--primary-green);
            box-shadow: var(--shadow-sm);
            transform: scale(1.02);
        }

        .leave-type-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .leave-type-value {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .leave-type-value .number {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
        }

        .leave-type-value .label-text {
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(11, 170, 75, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 170, 75, 0.35);
        }

        /* Modal & Table styling updates */
        .card-header {
            padding: 24px 32px;
            background: white;
            border-bottom: 1px solid #f3f4f6;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            z-index: 1000;
            backdrop-filter: blur(8px);
            overflow-y: auto;
            padding: 40px 16px;
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background: white;
            width: 600px;
            max-width: 100%;
            margin: auto;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid #f3f4f6;
            position: relative;
        }

        .modal-header {
            padding: 24px 32px;
            background: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 32px;
        }

        .table thead th {
            background: #f8fafc;
            padding: 16px 24px;
            font-weight: 700;
            font-size: 13px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        .table td {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 15px;
            transition: var(--transition);
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            background: white;
            box-shadow: 0 0 0 4px rgba(11, 170, 75, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="page-header" style="margin-bottom: 40px;">
        <h1 style="font-size: 32px; font-weight: 850; letter-spacing: -0.03em;">Nghỉ phép cá nhân</h1>
        <p style="font-size: 16px; color: var(--text-muted);">Quản lý hạn mức và theo dõi lịch sử nghỉ phép của bạn</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card total">
            <div class="icon-box">
                <i class="bi bi-calendar-check" style="font-size: 24px;"></i>
            </div>
            <div>
                <div class="label">Tổng phép năm ({{ now()->year }})</div>
                <div class="value">{{ number_format($phepNam->TongPhepDuocNghi ?? 0, 1) }}<span class="unit">ngày</span></div>
            </div>
        </div>
        <div class="stat-card used">
            <div class="icon-box">
                <i class="bi bi-calendar-minus" style="font-size: 24px;"></i>
            </div>
            <div>
                <div class="label">Đã nghỉ</div>
                <div class="value">{{ number_format($phepNam->DaNghi ?? 0, 1) }}<span class="unit">ngày</span></div>
            </div>
        </div>
        <div class="stat-card remaining">
            <div class="icon-box">
                <i class="bi bi-calendar-heart" style="font-size: 24px;"></i>
            </div>
            <div>
                <div class="label">Phép còn lại</div>
                <div class="value">{{ number_format($phepNam->ConLai ?? 0, 1) }}<span class="unit">ngày</span></div>
            </div>
        </div>
    </div>

    {{-- Thống kê các loại nghỉ khác --}}
    <div class="card" style="padding: 32px;">
        <h2 class="section-title">
            <i class="bi bi-grid-1x2 text-primary" style="color: var(--primary-green)"></i>
            Theo dõi các loại nghỉ khác ({{ now()->year }})
        </h2>
        <div class="leave-type-grid">
            @foreach($otherLeaveStats as $stat)
                <div class="leave-type-item">
                    <div class="leave-type-name">{{ $stat['ten'] }}</div>
                    <div class="leave-type-value">
                        <span class="number">{{ number_format($stat['da_dung'], 1) }}</span>
                        @if($stat['co_han_muc'])
                            <span class="label-text">/ {{ number_format($stat['han_muc'], 1) }} ngày</span>
                        @else
                            <span class="label-text">ngày đã dùng</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách đơn nghỉ phép</h3>
            <button class="btn btn-primary" onclick="openLeaveModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Đăng ký nghỉ phép
            </button>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Loại nghỉ</th>
                        <th>Thời gian</th>
                        <th>Số ngày</th>
                        <th>Lý do</th>
                        <th>Người duyệt</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nghiPheps as $index => $np)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-medium">{{ $np->loaiNghiPhep->Ten }}</td>
                            <td>
                                <div>{{ $np->TuNgay->format('d/m/Y') }} - {{ $np->DenNgay->format('d/m/Y') }}</div>
                            </td>
                            <td class="font-medium">{{ $np->SoNgayNghi }}</td>
                            <td>{{ $np->LyDo }}</td>
                            <td>{{ $np->nguoiDuyet->Ten ?? '-' }}</td>
                            <td>
                                @if($np->TrangThai === 2)
                                    <span class="badge badge-warning">Đang chờ</span>
                                @elseif($np->TrangThai === 1)
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Từ chối</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Bạn chưa có đơn nghỉ phép nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leave Modal -->
    <div class="modal" id="leaveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 20px; font-weight: 700;">Đăng ký nghỉ phép</h2>
                <button onclick="closeLeaveModal()" style="border: none; background: none; cursor: pointer;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="leaveForm" onsubmit="submitLeave(event)">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Loại nghỉ phép <span style="color: #ef4444;">*</span></label>
                        <select class="form-control" name="LoaiNghiPhepId">
                            @foreach($loaiNghiPheps as $type)
                                <option value="{{ $type->id }}">{{ $type->Ten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Từ ngày <span style="color: #ef4444;">*</span></label>
                            <input type="text" class="form-control" id="startDate" name="TuNgay" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Đến ngày <span style="color: #ef4444;">*</span></label>
                            <input type="text" class="form-control" id="endDate" name="DenNgay" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lý do nghỉ <span style="color: #ef4444;">*</span></label>
                        <textarea class="form-control" name="LyDo" rows="3"
                            placeholder="Nhập lý do nghỉ..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số ngày nghỉ</label>
                        <input type="text" class="form-control" id="leaveDaysDisplay" readonly
                            style="background-color: #f9fafb;">
                    </div>
                    <!-- Split Leave Warning & Type Selection -->
                    <div id="splitLeaveSection" style="display: none; background: #fff7ed; padding: 16px; border-radius: 12px; border: 1px solid #ffedd5; margin-bottom: 24px;">
                        <p style="color: #9a3412; font-size: 14px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Quỹ phép năm của bạn không đủ. Phần dư sẽ được tính vào loại nghỉ thay thế.
                        </p>
                        <label class="form-label">Loại nghỉ thay thế cho phần dư <span style="color: #ef4444;">*</span></label>
                        <select class="form-control no-select2" name="SplitLoaiNghiPhepId" id="splitTypeSelect">
                            <option value="">-- Chọn loại nghỉ --</option>
                            @foreach($loaiNghiPheps as $type)
                                @if($type->Ten != 'Nghỉ phép năm')
                                    <option value="{{ $type->id }}">{{ $type->Ten }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div
                    style="padding: 16px 24px; background: #f9fafb; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn" style="background: #e5e7eb; color: #374151;"
                        onclick="closeLeaveModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đơn đăng ký</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script>
        const workingSchedule = @json($workingSchedule->keyBy('Thu'));
        const remainingLeaveDays = {{ $phepNam->ConLai ?? 0 }};
        const annualLeaveId = {{ $loaiNghiPheps->firstWhere('Ten', 'Nghỉ phép năm')->id ?? 'null' }};
        let startPicker, endPicker;

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr.localize(flatpickr.l10ns.vn);

            startPicker = flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function (selectedDates) {
                    if (endPicker) endPicker.set('minDate', selectedDates[0]);
                    calculateDays();
                }
            });

            endPicker = flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                minDate: "today",
                onChange: function () {
                    calculateDays();
                }
            });
        });

        function calculateDays() {
            if (!startPicker || !endPicker) return;

            const fromDate = startPicker.selectedDates[0];
            const toDate = endPicker.selectedDates[0];

            if (fromDate && toDate) {
                if (toDate < fromDate) {
                    document.getElementById('leaveDaysDisplay').value = '';
                    return;
                }

                let count = 0;
                let cur = new Date(fromDate);
                cur.setHours(0, 0, 0, 0);
                let to = new Date(toDate);
                to.setHours(0, 0, 0, 0);

                while (cur <= to) {
                    const dayOfWeek = cur.getDay();
                    const dbDayOfWeek = (dayOfWeek === 0) ? 8 : (dayOfWeek + 1);

                    if (workingSchedule[dbDayOfWeek] && workingSchedule[dbDayOfWeek].CoLamViec) {
                        count += parseFloat(workingSchedule[dbDayOfWeek].CoLamViec);
                    }
                    cur.setDate(cur.getDate() + 1);
                }
                document.getElementById('leaveDaysDisplay').value = count + ' ngày';
                
                // Kiểm tra tách đơn
                const typeSelect = document.getElementsByName('LoaiNghiPhepId')[0];
                const splitSection = document.getElementById('splitLeaveSection');
                
                if (typeSelect.value == annualLeaveId && count > remainingLeaveDays) {
                    splitSection.style.display = 'block';
                } else {
                    splitSection.style.display = 'none';
                }
            }
        }

        // Thêm listener cho select loại nghỉ chính
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementsByName('LoaiNghiPhepId')[0];
            if (typeSelect) {
                typeSelect.addEventListener('change', calculateDays);
            }
        });

        function openLeaveModal() {
            document.getElementById('leaveModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            calculateDays();
        }

        function closeLeaveModal() {
            document.getElementById('leaveModal').classList.remove('show');
            document.getElementById('leaveForm').reset();
            document.body.style.overflow = '';
        }

        function submitLeave(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Manual Validation
            const loaiNghiPhepId = form.querySelector('[name="LoaiNghiPhepId"]').value;
            const tuNgay = form.querySelector('[name="TuNgay"]').value;
            const denNgay = form.querySelector('[name="DenNgay"]').value;
            const lyDo = form.querySelector('[name="LyDo"]').value;
            const splitSection = document.getElementById('splitLeaveSection');
            const splitTypeSelect = document.getElementById('splitTypeSelect');

            if (!loaiNghiPhepId || !tuNgay || !denNgay || !lyDo.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)'
                });
                return;
            }

            if (splitSection.style.display === 'block' && !splitTypeSelect.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng chọn loại nghỉ thay thế cho phần dư.'
                });
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Đang xử lý...';

            Swal.fire({
                title: 'Đang gửi đơn...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("nghi-phep.tao-moi") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;
                    
                    if (!response.ok) {
                        throw new Error(data?.message || 'Có lỗi xảy ra từ máy chủ (Mã lỗi: ' + response.status + ')');
                    }
                    return data;
                })
                .then(data => {
                    if (data && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data?.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: error.message
                    });
                });
        }
    </script>
@endpush
