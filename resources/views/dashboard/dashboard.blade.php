@extends('layouts.app')

@section('title', 'Trang chủ - Vietnam Rubber Group')

@section('content')
<div class="page-header">
    <h1>Trang chủ</h1>
    <p>Tổng quan hệ thống quản lý nhân sự</p>
</div>

<!-- Dashboards Selection Grid -->
<div class="dashboard-grid">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
        }
        @media (max-width: 600px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        .action-card {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 24px;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
        .action-card .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .action-card .count-badge {
            position: absolute;
            top: 24px;
            right: 24px;
            font-size: 28px;
            font-weight: 800;
            opacity: 0.8;
        }
        .action-card .title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }
        .action-card .desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        /* Specific Styles */
        .card-leave { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe; }
        .card-wfh { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-color: #ddd6fe; }
        .card-relative { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0; }
        .card-attendance { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-color: #fed7aa; }

        body.dark-theme .card-leave { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-color: #334155; }
        body.dark-theme .card-wfh { background: linear-gradient(135deg, #2e1065 0%, #1e1b4b 100%); border-color: #4c1d95; }
        body.dark-theme .card-relative { background: linear-gradient(135deg, #064e3b 0%, #022c22 100%); border-color: #065f46; }
        body.dark-theme .card-attendance { background: linear-gradient(135deg, #451a03 0%, #2a0e00 100%); border-color: #78350f; }

        body.dark-theme .action-card .title { color: #f8fafc; }
        body.dark-theme .action-card .desc { color: #94a3b8; }
        body.dark-theme .action-card .count-badge { color: rgba(255,255,255,0.4); }

        .relative-list {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .relative-mini-item {
            background: rgba(255,255,255,0.5);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #374151;
        }
        body.dark-theme .relative-mini-item {
            background: rgba(0,0,0,0.2);
            color: #cbd5e1;
        }
    </style>

    <!-- Đơn nghỉ phép -->
    <a href="{{ route('nghi-phep.danh-sach', ['trang_thai' => 2]) }}" class="action-card card-leave">
        <div class="icon-box" style="background: #3b82f6; color: white;">🏖️</div>
        <div class="count-badge">{{ number_format($pendingLeaveCount) }}</div>
        <div>
            <div class="title">Đơn nghỉ phép</div>
            <div class="desc">Có {{ number_format($pendingLeaveCount) }} đơn nghỉ phép đang chờ phê duyệt.</div>
        </div>
        <div style="font-size: 13px; color: #3b82f6; font-weight: 600; margin-top: auto;">Xem chi tiết →</div>
    </a>

    <!-- Đơn WFH -->
    <a href="{{ route('wfh.danh-sach', ['trang_thai' => 'dang_cho']) }}" class="action-card card-wfh">
        <div class="icon-box" style="background: #8b5cf6; color: white;">🏠</div>
        <div class="count-badge">{{ number_format($pendingWFHCount) }}</div>
        <div>
            <div class="title">Đơn WFH</div>
            <div class="desc">Có {{ number_format($pendingWFHCount) }} đơn làm việc từ xa đang chờ duyệt.</div>
        </div>
        <div style="font-size: 13px; color: #8b5cf6; font-weight: 600; margin-top: auto;">Xem chi tiết →</div>
    </a>

    <!-- Người phụ thuộc -->
    <div class="action-card card-relative" style="display: block;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
            <div class="icon-box" style="background: #0BAA4B; color: white;">👥</div>
            <div style="text-align: right;">
                <div style="font-size: 24px; font-weight: 800; color: #059669;">{{ $pendingRelatives->count() }}</div>
                <div style="font-size: 12px; color: #059669; font-weight: 600;">CHỜ DUYỆT</div>
            </div>
        </div>
        <div>
            <div class="title">Duyệt người phụ thuộc</div>
            <div class="desc">Danh sách nhân viên có người thân cần xác nhận.</div>
        </div>
        
        <div class="relative-list">
            @if($pendingRelatives->count() > 0)
                @php $firstItem = $pendingRelatives->first(); @endphp
                <a href="{{ route('nhan-vien.info', $firstItem->NhanVienId) }}#relatives" class="relative-mini-item" style="text-decoration: none;">
                    <span><strong>{{ $firstItem->HoTen }}</strong> ({{ $firstItem->nhanVien->Ten ?? 'N/A' }})</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                
                @if($pendingRelatives->count() > 1)
                    <a href="{{ route('nhan-vien.danh-sach') }}" style="text-decoration: none; display: flex; align-items: center; justify-content: center; padding: 10px; background: rgba(0,0,0,0.05); border-radius: 8px; margin-top: 8px; font-size: 13px; color: #059669; font-weight: 600;">
                        Và {{ $pendingRelatives->count() - 1 }} yêu cầu khác cần duyệt →
                    </a>
                @endif
            @else
                <div style="font-size: 13px; color: #6b7280; font-style: italic; margin-top: 8px;">Không có yêu cầu nào.</div>
            @endif
        </div>
    </div>

    <!-- Nhắc nhở chấm công -->
    <a href="{{ route('cham-cong.danh-sach') }}" class="action-card card-attendance">
        <div class="icon-box" style="background: #f97316; color: white;">⏱️</div>
        <div class="count-badge">{{ number_format($missingAttendanceCount) }}</div>
        <div>
            <div class="title">Nhắc nhở chấm công</div>
            <div class="desc">Có {{ number_format($missingAttendanceCount) }} nhân viên chưa ghi nhận chấm công hôm nay.</div>
        </div>
        <div style="font-size: 13px; color: #f97316; font-weight: 600; margin-top: auto;">Xem danh sách →</div>
    </a>

    <!-- Sinh nhật hôm nay -->
    <div class="card" style="grid-column: 1 / -1;">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            🎂 Sinh nhật hôm nay
            @if($birthdayEmployees->count() > 0)
                <span style="background: linear-gradient(135deg, #f97316, #ef4444); color: white; font-size: 13px; font-weight: 600; border-radius: 12px; padding: 2px 10px;">{{ $birthdayEmployees->count() }}</span>
            @endif
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
            @if($birthdayEmployees->count() > 0)
                @php $firstBirthday = $birthdayEmployees->first(); @endphp
                <div class="birthday-item" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: linear-gradient(135deg, #fff7ed, #fef3c7); border-radius: 16px; border: 1px solid #fde68a;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f97316, #ef4444); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px;">
                        🎂
                    </div>
                    <div style="flex: 1;">
                        <div class="emp-name" style="font-weight: 700; color: #1f2937; font-size: 16px;">{{ $firstBirthday->Ten }}</div>
                        <div class="birthday-text" style="font-size: 13px; color: #78350f;">
                            Chúc mừng sinh nhật! 🎈
                            @if($firstBirthday->NgaySinh)
                                · {{ \Carbon\Carbon::parse($firstBirthday->NgaySinh)->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                    <button
                        onclick="chuMungSinhNhat({{ $firstBirthday->id }}, '{{ addslashes($firstBirthday->Ten) }}')"
                        style="padding: 10px 18px; background: linear-gradient(135deg, #f97316, #ef4444); color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                        🎊 Chúc mừng
                    </button>
                </div>
                
                @if($birthdayEmployees->count() > 1)
                    <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #fffbeb; border-radius: 16px; border: 1px solid #fef3c7;">
                        <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">🎁</div>
                        <div style="flex:1;">
                            <div style="font-weight: 700; color: #92400e;">Và {{ $birthdayEmployees->count() - 1 }} nhân viên khác</div>
                            <div style="font-size: 13px; color: #b45309;">Hôm nay cũng là ngày đặc biệt của họ!</div>
                        </div>
                    </div>
                @endif
            @else
                <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 16px; padding: 32px; background: #f9fafb; border-radius: 16px; border: 1px dashed #e5e7eb; justify-content: center;">
                    <div style="font-size: 32px;">📅</div>
                    <div style="color: #6b7280; font-size: 15px; font-weight: 500;">Hôm nay không có nhân viên nào đón tuổi mới.</div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Chúc mừng --}}
<div id="birthdayModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:white; border-radius:24px; padding:40px; max-width:480px; width:90%; text-align:center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="font-size: 72px; margin-bottom: 16px;">🎉</div>
        <h3 id="birthdayModalTitle" style="font-size:24px; font-weight:800; color:#1f2937; margin-bottom:12px;"></h3>
        <p style="font-size:16px; color:#4b5563; margin-bottom:32px; line-height:1.7;">
            Hãy gửi tin nhắn chúc mừng kèm theo những lời chúc tốt đẹp nhất dành cho thành viên của chúng ta trong ngày đặc biệt này! 🌟
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <button
                id="btnGuiBirthdayMail"
                onclick="guiBirthdayMailAjax()"
                style="padding:12px 28px; background: linear-gradient(135deg, #ec4899, #f97316); color:white; border:none; border-radius:12px; font-size:15px; font-weight:700; cursor:pointer; transition: opacity 0.2s; display:flex; align-items:center; gap:10px;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'"
            >
                📧 Gửi email chúc mừng
            </button>
            <button onclick="closeBirthdayModal()" style="padding:12px 28px; background: #f3f4f6; color:#4b5563; border:none; border-radius:12px; font-size:15px; font-weight:700; cursor:pointer; transition: background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                Để sau
            </button>
        </div>
        <div id="birthdayMailStatus" style="margin-top:20px; font-size:14px; font-weight: 600; display:none;"></div>
    </div>
</div>

<script>
    var currentBirthdayEmployeeId = null;

    function chuMungSinhNhat(id, ten) {
        currentBirthdayEmployeeId = id;
        document.getElementById('birthdayModalTitle').textContent = 'Chúc mừng sinh nhật ' + ten + '! 🎂';
        document.getElementById('birthdayMailStatus').style.display = 'none';
        document.getElementById('birthdayMailStatus').textContent = '';
        var btn = document.getElementById('btnGuiBirthdayMail');
        btn.disabled = false;
        btn.innerHTML = '📧 Gửi email chúc mừng';
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
        btn.innerHTML = '⏳ Đang xử lý...';
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
                btn.innerHTML = '✅ Đã hoàn tất!';
                btn.style.background = 'linear-gradient(135deg, #0BAA4B, #22c55e)';
            } else {
                statusEl.style.color = '#dc2626';
                statusEl.innerHTML = '❌ ' + data.message;
                btn.disabled = false;
                btn.innerHTML = '📧 Thử lại';
                btn.style.opacity = '1';
            }
        })
        .catch(err => {
            statusEl.style.display = 'block';
            statusEl.style.color = '#dc2626';
            statusEl.innerHTML = '❌ Lỗi kết nối máy chủ.';
            btn.disabled = false;
            btn.innerHTML = '📧 Thử lại';
            btn.style.opacity = '1';
        });
    }

    document.getElementById('birthdayModal').addEventListener('click', function(e) {
        if (e.target === this) closeBirthdayModal();
    });
</script>
@endsection
