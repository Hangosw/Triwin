@extends('layouts.app')

@section('title', 'Trang chủ - Vietnam Rubber Group')

@section('content')
<div class="page-header">
    <h1>Trang chủ</h1>
    <p>Tổng quan hệ thống quản lý nhân sự</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <a href="{{ route('nhan-vien.danh-sach') }}" class="stat-card" style="text-decoration: none; display: block; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="label">Tổng nhân viên</div>
                <div class="value">{{ number_format($totalEmployees) }}</div>
            </div>
            <div style="width: 48px; height: 48px; background-color: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('phong-ban.danh-sach') }}" class="stat-card" style="text-decoration: none; display: block; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="label">Phòng ban</div>
                <div class="value">{{ number_format($totalDepartments) }}</div>
            </div>
            <div style="width: 48px; height: 48px; background-color: #8b5cf6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('hop-dong.danh-sach') }}" class="stat-card" style="text-decoration: none; display: block; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="label">Hợp đồng</div>
                <div class="value">{{ number_format($totalContracts) }}</div>
            </div>
            <div style="width: 48px; height: 48px; background-color: #f97316; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('hop-dong.danh-sach', ['expiring_soon' => 1]) }}" class="stat-card" style="text-decoration: none; display: block; transition: all 0.2s ease;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="label">Sắp hết hạn (25 ngày)</div>
                <div class="value" style="color: #ef4444;">{{ number_format($expiringContractsCount) }}</div>
            </div>
            <div style="width: 48px; height: 48px; background-color: #ef4444; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </a>
</div>

<!-- Cards -->
<div class="dashboard-grid">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
        }
        @media (max-width: 600px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Recent Activities / Sinh nhật hôm nay -->
    <div class="card">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
            🎂 Sinh nhật hôm nay
            @if($birthdayEmployees->count() > 0)
                <span style="background: linear-gradient(135deg, #f97316, #ef4444); color: white; font-size: 13px; font-weight: 600; border-radius: 12px; padding: 2px 10px;">{{ $birthdayEmployees->count() }}</span>
            @endif
        </h2>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($birthdayEmployees as $nv)
                <div style="display: flex; align-items: center; gap: 16px; padding: 14px; background: linear-gradient(135deg, #fff7ed, #fef3c7); border-radius: 12px; border: 1px solid #fde68a;">
                    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #f97316, #ef4444); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px;">
                        🎂
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: #1f2937; font-size: 15px;">{{ $nv->Ten }}</div>
                        <div style="font-size: 13px; color: #78350f;">
                            Hôm nay là sinh nhật của họ! 🎉
                            @if($nv->NgaySinh)
                                &nbsp;·&nbsp;{{ \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                    <button
                        onclick="chuMungSinhNhat({{ $nv->id }}, '{{ addslashes($nv->Ten) }}')"
                        style="padding: 8px 16px; background: linear-gradient(135deg, #f97316, #ef4444); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'"
                        onmouseout="this.style.opacity='1'"
                    >
                        🎊 Chúc mừng
                    </button>
                </div>
            @empty
                <div style="display: flex; align-items: center; gap: 16px; padding: 20px; background: #f9fafb; border-radius: 12px; border: 1px dashed #e5e7eb;">
                    <div style="font-size: 28px;">📅</div>
                    <div style="color: #6b7280; font-size: 14px;">Không có nhân viên nào có sinh nhật hôm nay.</div>
                </div>
            @endforelse
        </div>

        {{-- Modal Chúc mừng --}}
        <div id="birthdayModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
            <div style="background:white; border-radius:20px; padding:36px 40px; max-width:440px; width:90%; text-align:center; box-shadow: 0 20px 60px rgba(236,72,153,0.2);">
                <div style="font-size: 60px; margin-bottom: 12px;">🎉</div>
                <h3 id="birthdayModalTitle" style="font-size:20px; font-weight:700; color:#1f2937; margin-bottom:8px;"></h3>
                <p style="font-size:15px; color:#4b5563; margin-bottom:28px; line-height:1.6;">
                    Chúc bạn sinh nhật vui vẻ, luôn tràn đầy năng lượng và đạt nhiều thành công trong công việc! 🌟
                </p>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <button
                        id="btnGuiBirthdayMail"
                        onclick="guiBirthdayMailAjax()"
                        style="padding:10px 22px; background: linear-gradient(135deg, #ec4899, #f97316); color:white; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition: opacity 0.2s; display:flex; align-items:center; gap:8px;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                    >
                        📧 Gửi mail
                    </button>
                    <button onclick="closeBirthdayModal()" style="padding:10px 22px; background: linear-gradient(135deg, #0BAA4B, #22c55e); color:white; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        Đóng
                    </button>
                </div>
                <div id="birthdayMailStatus" style="margin-top:16px; font-size:13px; display:none;"></div>
            </div>
        </div>
        <style>
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            #birthdayModal > div { animation: fadeInUp 0.3s ease; }
        </style>
        <script>
            var currentBirthdayEmployeeId = null;

            function chuMungSinhNhat(id, ten) {
                currentBirthdayEmployeeId = id;
                document.getElementById('birthdayModalTitle').textContent = 'Chúc mừng sinh nhật ' + ten + '! 🎂';
                document.getElementById('birthdayMailStatus').style.display = 'none';
                document.getElementById('birthdayMailStatus').textContent = '';
                var btn = document.getElementById('btnGuiBirthdayMail');
                btn.disabled = false;
                btn.innerHTML = '📧 Gửi mail';
                btn.style.opacity = '1';
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
                btn.innerHTML = '⏳ Đang gửi...';
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
                        btn.innerHTML = '✅ Đã gửi!';
                        btn.style.background = 'linear-gradient(135deg, #0BAA4B, #22c55e)';
                    } else {
                        statusEl.style.color = '#dc2626';
                        statusEl.innerHTML = '❌ ' + data.message;
                        btn.disabled = false;
                        btn.innerHTML = '📧 Gửi mail';
                        btn.style.opacity = '1';
                    }
                })
                .catch(err => {
                    statusEl.style.display = 'block';
                    statusEl.style.color = '#dc2626';
                    statusEl.innerHTML = '❌ Có lỗi kết nối, vui lòng thử lại.';
                    btn.disabled = false;
                    btn.innerHTML = '📧 Gửi mail';
                    btn.style.opacity = '1';
                });
            }

            // Close on backdrop click
            document.getElementById('birthdayModal').addEventListener('click', function(e) {
                if (e.target === this) closeBirthdayModal();
            });
        </script>
    </div>

    <!-- Notifications -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Thông báo</h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <a href="{{ route('cham-cong.danh-sach') }}" style="text-decoration: none; display: flex; gap: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb; transition: background 0.2s; border-radius: 4px;">
                <div style="width: 8px; height: 8px; background-color: #f97316; border-radius: 50%; margin-top: 8px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div class="font-medium" style="color: #1f2937;">Nhắc nhở chấm công</div>
                    <div class="text-gray" style="font-size: 14px; margin-top: 4px;">Có {{ number_format($missingAttendanceCount) }} nhân viên chưa chấm công hôm nay</div>
                </div>
            </a>

            <a href="{{ route('nghi-phep.danh-sach', ['trang_thai' => 2]) }}" style="text-decoration: none; display: flex; gap: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb; transition: background 0.2s; border-radius: 4px;">
                <div style="width: 8px; height: 8px; background-color: #3b82f6; border-radius: 50%; margin-top: 8px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div class="font-medium" style="color: #1f2937;">Đơn nghỉ phép chờ duyệt</div>
                    <div class="text-gray" style="font-size: 14px; margin-top: 4px;">{{ number_format($pendingLeaveCount) }} đơn nghỉ phép đang chờ phê duyệt</div>
                </div>
            </a>

            <a href="{{ route('tang-ca.danh-sach', ['trang_thai' => 'dang_cho']) }}" style="text-decoration: none; display: flex; gap: 16px; transition: background 0.2s; border-radius: 4px;">
                <div style="width: 8px; height: 8px; background-color: #8b5cf6; border-radius: 50%; margin-top: 8px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div class="font-medium" style="color: #1f2937;">Phiếu tăng ca chờ duyệt</div>
                    <div class="text-gray" style="font-size: 14px; margin-top: 4px;">{{ number_format($pendingOvertimeCount) }} phiếu tăng ca đang chờ phê duyệt</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
