@extends('layouts.app')

@section('title', 'Dashboard - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>HR Management System Overview</p>
</div>

<!-- Dashboard Wrapper -->
<div class="dashboard-wrapper">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        .action-card {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .action-card .title {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }
        .action-card .value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        
        body.dark-theme .action-card { background: #1a1d27; border-color: #2e3349; }
        body.dark-theme .action-card .value { color: #f8fafc; }

        .pending-section {
            margin-top: 24px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1e293b;
        }
        body.dark-theme .section-title { color: #f8fafc; }
        
        .table-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        body.dark-theme .table-card { background: #1a1d27; border-color: #2e3349; }
        
        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        body.dark-theme .table thead th { background: #13161f; border-bottom-color: #2e3349; color: #94a3b8; }
        
        .table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        body.dark-theme .table tbody td { border-bottom-color: #2e3349; }
        
        .emp-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .emp-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }
        .action-btns {
            display: flex;
            gap: 6px;
        }
        .btn-table-action {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-approve { background: #ecfdf5; color: #059669; }
        .btn-approve:hover { background: #059669; color: white; }
        .btn-reject { background: #fef2f2; color: #dc2626; }
        .btn-reject:hover { background: #dc2626; color: white; }

        .birthday-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
        }
    </style>

    <!-- Thống kê tổng quan -->
    <div class="dashboard-grid mb-4">
        <a href="{{ route('nghi-phep.danh-sach', ['trang_thai' => 2]) }}" class="action-card">
            <div>
                <div class="title">Leave Requests</div>
                <div class="value">{{ number_format($pendingLeaveCount) }}</div>
            </div>
        </a>
        <a href="{{ route('wfh.danh-sach', ['trang_thai' => 'dang_cho']) }}" class="action-card">
            <div>
                <div class="title">WFH Requests</div>
                <div class="value">{{ number_format($pendingWFHCount) }}</div>
            </div>
        </a>
        <a href="{{ route('nhan-vien.danh-sach') }}" class="action-card">
            <div>
                <div class="title">Dependents</div>
                <div class="value">{{ $pendingRelatives->count() }}</div>
            </div>
        </a>
        <a href="{{ route('cham-cong.danh-sach') }}" class="action-card">
            <div>
                <div class="title">Missing Attendance</div>
                <div class="value">{{ number_format($missingAttendanceCount) }}</div>
            </div>
        </a>
    </div>

    <!-- Section: Pending Leave Requests -->
    <div class="pending-section">
        <div class="section-title">
            Pending Leave Requests
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLeaves as $leave)
                            <tr>
                                <td>
                                    <div class="emp-info">
                                        <div class="emp-avatar">{{ substr($leave->nhanVien->Ten ?? 'N', 0, 1) }}</div>
                                        <div>
                                            <div class="font-bold">{{ $leave->nhanVien->Ten ?? 'N/A' }}</div>
                                            <div class="text-muted" style="font-size: 12px;">{{ $leave->nhanVien->MaNhanVien ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-info">{{ $leave->loaiNghiPhep->Ten ?? 'N/A' }}</span></td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($leave->TuNgay)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->DenNgay)->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        {{ $leave->TuBuoi == 'ca_ngay' ? 'Full Day' : ($leave->TuBuoi == 'sang' ? 'Morning' : 'Afternoon') }} 
                                        → {{ $leave->DenBuoi == 'ca_ngay' ? 'Full Day' : ($leave->DenBuoi == 'sang' ? 'Morning' : 'Afternoon') }}
                                    </div>
                                </td>
                                <td><span class="font-bold text-primary">{{ number_format($leave->SoNgayNghi, 1) }}</span> d</td>
                                <td title="{{ $leave->LyDo }}">{{ \Str::limit($leave->LyDo, 30) }}</td>
                                <td>
                                    <div class="action-btns justify-content-end">
                                        <button onclick="processAction('leave', 'approve', {{ $leave->id }})" class="btn-table-action btn-approve" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        <button onclick="processAction('leave', 'reject', {{ $leave->id }})" class="btn-table-action btn-reject" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        <a href="{{ route('nghi-phep.danh-sach', ['trang_thai' => 2]) }}" class="btn-table-action" style="background: #f8fafc; color: #64748b;" title="View Details"><i class="bi bi-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No pending leave requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section: Pending WFH Requests -->
    <div class="pending-section">
        <div class="section-title">
            Pending WFH Requests
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingWFHs as $wfh)
                            <tr>
                                <td>
                                    <div class="emp-info">
                                        <div class="emp-avatar">{{ substr($wfh->nhanVien->Ten ?? 'N', 0, 1) }}</div>
                                        <div>
                                            <div class="font-bold">{{ $wfh->nhanVien->Ten ?? 'N/A' }}</div>
                                            <div class="text-muted" style="font-size: 12px;">{{ $wfh->nhanVien->MaNhanVien ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($wfh->NgayBatDau)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($wfh->NgayKetThuc)->format('d/m/Y') }}</td>
                                <td><span class="font-bold text-primary">{{ number_format($wfh->Ngay, 1) }}</span> d</td>
                                <td title="{{ $wfh->LyDo }}">{{ \Str::limit($wfh->LyDo, 30) }}</td>
                                <td>
                                    <div class="action-btns justify-content-end">
                                        <button onclick="processAction('wfh', 'approve', {{ $wfh->id }})" class="btn-table-action btn-approve" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        <button onclick="processAction('wfh', 'reject', {{ $wfh->id }})" class="btn-table-action btn-reject" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        <a href="{{ route('wfh.danh-sach', ['trang_thai' => 'dang_cho']) }}" class="btn-table-action" style="background: #f8fafc; color: #64748b;" title="View Details"><i class="bi bi-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No pending WFH requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section: Pending Dependent Approvals -->
    <div class="pending-section">
        <div class="section-title">
            Pending Dependent Approvals
        </div>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Dependent</th>
                            <th>Relationship</th>
                            <th>Tax Dep.</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRelatives as $rel)
                            <tr>
                                <td>
                                    <div class="emp-info">
                                        <div class="emp-avatar">{{ substr($rel->nhanVien->Ten ?? 'N', 0, 1) }}</div>
                                        <div>
                                            <div class="font-bold">{{ $rel->nhanVien->Ten ?? 'N/A' }}</div>
                                            <div class="text-muted" style="font-size: 12px;">{{ $rel->nhanVien->MaNhanVien ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-bold">{{ $rel->HoTen }}</div>
                                    <div class="text-muted" style="font-size: 12px;">NS: {{ $rel->NgaySinh ? \Carbon\Carbon::parse($rel->NgaySinh)->format('d/m/Y') : 'N/A' }}</div>
                                </td>
                                <td>
                                    @php
                                        $quanHeArr = [
                                            'bo_de' => 'Father', 'me_de' => 'Mother', 'vo_chong' => 'Spouse',
                                            'con_ruot' => 'Child', 'con_nuoi' => 'Adopted Child', 'khac' => 'Other'
                                        ];
                                    @endphp
                                    {{ $quanHeArr[$rel->QuanHe] ?? $rel->QuanHe }}
                                </td>
                                <td>
                                    @if($rel->LaGiamTruGiaCanh)
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-gray">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btns justify-content-end">
                                        <button onclick="processAction('relative', 'approve', {{ $rel->id }})" class="btn-table-action btn-approve" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        <button onclick="processAction('relative', 'reject', {{ $rel->id }})" class="btn-table-action btn-reject" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        <a href="{{ route('nhan-vien.info', $rel->NhanVienId) }}#relatives" class="btn-table-action" style="background: #f8fafc; color: #64748b;" title="View Details"><i class="bi bi-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No pending dependent requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Today's Birthdays -->
    <div class="pending-section mb-5">
        <div class="section-title">
            Today's Birthdays
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
            @if($birthdayEmployees->count() > 0)
                @foreach($birthdayEmployees->take(6) as $emp)
                    <div class="table-card p-3" style="margin-bottom: 0;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f97316, #ef4444); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white;">
                                🎂
                            </div>
                            <div style="flex: 1;">
                                <div class="font-bold" style="font-size: 16px;">{{ $emp->Ten }}</div>
                                <div class="text-muted" style="font-size: 13px;">{{ $emp->ttCongViec->phongBan->Ten ?? 'Nhân viên' }}</div>
                            </div>
                            <button
                                onclick="chuMungSinhNhat({{ $emp->id }}, '{{ addslashes($emp->Ten) }}')"
                                class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 10px;"
                            >
                                🎊 Wish
                            </button>
                        </div>
                    </div>
                @endforeach
                @if($birthdayEmployees->count() > 6)
                    <div class="text-center mt-3" style="grid-column: 1 / -1;">
                        <span class="text-muted">And {{ $birthdayEmployees->count() - 6 }} others have birthdays today.</span>
                    </div>
                @endif
            @else
                <div style="grid-column: 1 / -1; padding: 40px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1; text-align: center; color: #64748b;">
                    <i class="bi bi-calendar-event" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                    No employee birthdays today.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Script xử lý phê duyệt/từ chối --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function processAction(type, action, id) {
        let title = '';
        let text = '';
        let url = '';
        let confirmText = 'Confirm';
        
        if (type === 'leave') {
            url = action === 'approve' ? `/nghi-phep/duyet/${id}` : `/nghi-phep/tu-choi/${id}`;
            title = action === 'approve' ? 'Approve leave request?' : 'Reject leave request?';
            text = action === 'approve' ? 'This request will be marked as Approved.' : 'Please enter the reason for rejection (optional):';
        } else if (type === 'wfh') {
            url = action === 'approve' ? `/wfh/duyet/${id}` : `/wfh/tu-choi/${id}`;
            title = action === 'approve' ? 'Approve WFH request?' : 'Reject WFH request?';
        } else if (type === 'relative') {
            url = action === 'approve' ? `/nguoi-phu-thuoc/duyet/${id}` : `/nguoi-phu-thuoc/tu-choi/${id}`;
            title = action === 'approve' ? 'Approve dependent request?' : 'Reject dependent request?';
        }

        const swalConfig = {
            title: title,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#059669' : '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
        };

        if (action === 'reject') {
            swalConfig.input = 'textarea';
            swalConfig.inputPlaceholder = 'Enter reason...';
            swalConfig.inputAttributes = { 'aria-label': 'Enter reason' };
            if (type === 'relative') swalConfig.inputValidator = (value) => {
                if (!value) return 'You must enter a reason!';
            };
        }

        Swal.fire(swalConfig).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                if (action === 'reject' && result.value) {
                    formData.append('LyDo', result.value); // for leave
                    formData.append('GhiChu', result.value); // for wfh & relative
                }
                
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message || 'Something went wrong', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error!', 'Could not connect to the server.', 'error');
                });
            }
        });
    }
</script>


{{-- Modal Chúc mừng --}}
<div id="birthdayModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:white; border-radius:24px; padding:40px; max-width:480px; width:90%; text-align:center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="font-size: 72px; margin-bottom: 16px;">🎉</div>
        <h3 id="birthdayModalTitle" style="font-size:24px; font-weight:800; color:#1f2937; margin-bottom:12px;"></h3>
        <p style="font-size:16px; color:#4b5563; margin-bottom:32px; line-height:1.7;">
            Send a congratulatory message along with your best wishes to our member on this special day! 🌟
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <button
                id="btnGuiBirthdayMail"
                onclick="guiBirthdayMailAjax()"
                style="padding:12px 28px; background: linear-gradient(135deg, #ec4899, #f97316); color:white; border:none; border-radius:12px; font-size:15px; font-weight:700; cursor:pointer; transition: opacity 0.2s; display:flex; align-items:center; gap:10px;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'"
            >
                📧 Send Wishes
            </button>
            <button onclick="closeBirthdayModal()" style="padding:12px 28px; background: #f3f4f6; color:#4b5563; border:none; border-radius:12px; font-size:15px; font-weight:700; cursor:pointer; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                Maybe later
            </button>
        </div>
        <div id="birthdayMailStatus" style="margin-top:20px; font-size:14px; font-weight: 600; display:none;"></div>
    </div>
</div>

<script>
    var currentBirthdayEmployeeId = null;

    function chuMungSinhNhat(id, ten) {
        currentBirthdayEmployeeId = id;
        document.getElementById('birthdayModalTitle').textContent = 'Happy Birthday ' + ten + '! 🎂';
        document.getElementById('birthdayMailStatus').style.display = 'none';
        document.getElementById('birthdayMailStatus').textContent = '';
        var btn = document.getElementById('btnGuiBirthdayMail');
        btn.disabled = false;
        btn.innerHTML = '📧 Send Wishes';
        btn.style.opacity = '1';
        btn.style.background = 'linear-gradient(135deg, #ec4899, #f97316)';
        var modal = document.getElementById('birthdayModal');
        modal.style.display = 'flex';
    }

    function closeBirthdayModal() {
        document.getElementById('birthdayModal').style.display = 'none';
        currentBirthdayEmployeeId = null;
    }

    function guiBirthdayMailAjax() {
        if (!currentBirthdayEmployeeId) return;
        var btn = document.getElementById('btnGuiBirthdayMail');
        btn.disabled = true;
        btn.innerHTML = '⏳ Processing...';
        btn.style.opacity = '0.7';

        var statusEl = document.getElementById('birthdayMailStatus');
        statusEl.style.display = 'none';
        statusEl.textContent = '';

        fetch('/dashboard/gui-birthday-mail/' + currentBirthdayEmployeeId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            statusEl.style.display = 'block';
            if (data.success) {
                statusEl.style.color = '#16a34a';
                statusEl.innerHTML = '✅ ' + data.message;
                btn.innerHTML = '✅ Done!';
                btn.style.background = 'linear-gradient(135deg, #0BAA4B, #22c55e)';
            } else {
                statusEl.style.color = '#dc2626';
                statusEl.innerHTML = '❌ ' + data.message;
                btn.disabled = false;
                btn.innerHTML = '📧 Retry';
                btn.style.opacity = '1';
            }
        })
        .catch(err => {
            statusEl.style.display = 'block';
            statusEl.style.color = '#dc2626';
            statusEl.innerHTML = '❌ Server connection error.';
            btn.disabled = false;
            btn.innerHTML = '📧 Retry';
            btn.style.opacity = '1';
        });
    }

    document.getElementById('birthdayModal').addEventListener('click', function(e) {
        if (e.target === this) closeBirthdayModal();
    });
</script>
</div>
@endsection
