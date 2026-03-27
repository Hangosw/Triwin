{{-- Tab: Chấm công --}}
<div class="tab-content" id="tab-attendance">
    <div class="detail-section">
        <h2>
            <i class="bi bi-clock-history"></i>
            Lịch sử chấm công
        </h2>

        {{-- Filter bar --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            {{-- Month/Year picker --}}
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="display:flex; align-items:center; gap:8px; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:8px; padding:6px 14px; font-size:14px; font-weight:600; color:#374151; cursor:pointer;" id="attendanceDateDisplay" onclick="document.getElementById('attendanceMonthPicker').showPicker()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span id="attendanceDateLabel">--/----</span>
                    <input type="month" id="attendanceMonthPicker" style="position:absolute;opacity:0;width:0;height:0;" onchange="onAttendanceMonthChange(this.value)">
                </div>
            </div>
            {{-- Quick buttons --}}
            <div style="display:flex; gap:8px;">
                <button type="button" onclick="loadAttendancePrevMonth()" style="padding:7px 16px; border:1px solid #d1d5db; border-radius:8px; background:white; font-size:13px; font-weight:600; color:#374151; cursor:pointer; transition:all .15s;" onmouseover="this.style.borderColor='#0BAA4B';this.style.color='#0BAA4B'" onmouseout="this.style.borderColor='#d1d5db';this.style.color='#374151'">
                    ← Tháng trước
                </button>
                <button type="button" onclick="loadAttendanceThisMonth()" style="padding:7px 16px; border:1px solid #0BAA4B; border-radius:8px; background:#0BAA4B; font-size:13px; font-weight:600; color:white; cursor:pointer; transition:all .15s;" onmouseover="this.style.background='#088c3d'" onmouseout="this.style.background='#0BAA4B'">
                    Tháng này
                </button>
            </div>
        </div>

        {{-- Summary stats --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:12px; margin-bottom:20px;" id="attendanceStats">
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; text-align:center;">
                <div style="font-size:22px; font-weight:700; color:#0BAA4B;" id="statTotal">--</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">Tổng ngày</div>
            </div>
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; text-align:center;">
                <div style="font-size:22px; font-weight:700; color:#0BAA4B;" id="statOnTime">--</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">✅ Đúng giờ</div>
            </div>
            <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:14px 16px; text-align:center;">
                <div style="font-size:22px; font-weight:700; color:#ea580c;" id="statLate">--</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">⏰ Đi muộn</div>
            </div>
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 16px; text-align:center;">
                <div style="font-size:22px; font-weight:700; color:#2563eb;" id="statEarly">--</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">🏃 Về sớm</div>
            </div>
        </div>

        {{-- Table --}}
        <div id="attendanceTableWrap">
            <div style="text-align:center; padding:40px; color:#9ca3af; font-size:14px;">
                <div style="font-size:32px; margin-bottom:8px;">📅</div>
                Chọn tháng để xem lịch sử chấm công
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const EMPLOYEE_ID = {{ $employee->id }};
    let currentMonth = null, currentYear = null;

    function pad(n) { return String(n).padStart(2, '0'); }

    function updateLabel(m, y) {
        document.getElementById('attendanceDateLabel').textContent = `${pad(m)}/${y}`;
        document.getElementById('attendanceMonthPicker').value = `${y}-${pad(m)}`;
    }

    function statusBadge(status) {
        const isDark = document.body.classList.contains('dark-theme');
        const map = {
            'dung_gio': {
                label: '✅ Đúng giờ',
                bg: isDark ? 'rgba(74, 222, 128, 0.15)' : '#dcfce7',
                color: isDark ? '#4ade80' : '#166534'
            },
            'tre': {
                label: '⏰ Đi muộn',
                bg: isDark ? 'rgba(251, 146, 60, 0.15)' : '#fff7ed',
                color: isDark ? '#fb923c' : '#c2410c'
            },
            've_som': {
                label: '🏃 Về sớm',
                bg: isDark ? 'rgba(96, 165, 250, 0.15)' : '#eff6ff',
                color: isDark ? '#60a5fa' : '#1d4ed8'
            }
        };
        const item = map[status] || { label: '—', bg: isDark ? '#2e3349' : '#f3f4f6', color: isDark ? '#6b7492' : '#6b7280' };
        return `<span style="background:${item.bg};color:${item.color};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">${item.label}</span>`;
    }

    function timeColor(status, field) {
        // Giờ vào đỏ nếu đi muộn, giờ ra đỏ nếu về sớm
        if (field === 'in'  && status === 'tre')    return 'color:#ef4444;font-weight:600';
        if (field === 'out' && status === 've_som') return 'color:#ef4444;font-weight:600';
        return 'color:#1f2937';
    }

    function loadAttendance(month, year) {
        currentMonth = month;
        currentYear  = year;
        updateLabel(month, year);

        const wrap = document.getElementById('attendanceTableWrap');
        wrap.innerHTML = `<div style="text-align:center;padding:40px;color:#6b7280;font-size:14px;"><div style="font-size:32px;margin-bottom:8px;">⏳</div>Đang tải...</div>`;

        // Reset stats
        ['statTotal','statOnTime','statLate','statEarly'].forEach(id => document.getElementById(id).textContent = '--');

        fetch(`/api/nhan-vien/${EMPLOYEE_ID}/cham-cong?month=${month}&year=${year}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const rows = data.attendances || [];

            // Stats
            document.getElementById('statTotal').textContent  = rows.length;
            document.getElementById('statOnTime').textContent = rows.filter(r => r.TrangThai === 'dung_gio').length;
            document.getElementById('statLate').textContent   = rows.filter(r => r.TrangThai === 'tre').length;
            document.getElementById('statEarly').textContent  = rows.filter(r => r.TrangThai === 've_som').length;

            if (rows.length === 0) {
                wrap.innerHTML = `<div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;"><div style="font-size:32px;margin-bottom:8px;">📭</div>Không có dữ liệu chấm công tháng này</div>`;
                return;
            }

            let html = `
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e5e7eb;">
                            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Ngày</th>
                            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Giờ vào</th>
                            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Giờ ra</th>
                            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Trạng thái</th>
                            <th style="padding:14px 16px;text-align:center;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;"></th>
                        </tr>
                    </thead>
                    <tbody>`;

            rows.forEach(r => {
                const vao = r.Vao ? new Date(r.Vao) : null;
                const ra  = r.Ra  ? new Date(r.Ra)  : null;
                const ngay = vao ? vao.toLocaleDateString('vi-VN', {day:'2-digit',month:'2-digit',year:'numeric'}) : '—';
                const gioVao = vao ? vao.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) : '—';
                const gioRa  = ra  ? ra.toLocaleTimeString('vi-VN',  {hour:'2-digit',minute:'2-digit',second:'2-digit'}) : '<span style="color:#9ca3af">Chưa ra</span>';

                html += `
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:14px 16px;font-size:14px;font-weight:600;color:#1f2937;">${ngay}</td>
                            <td style="padding:14px 16px;font-size:14px;${timeColor(r.TrangThai,'in')}">${gioVao}</td>
                            <td style="padding:14px 16px;font-size:14px;${timeColor(r.TrangThai,'out')}">${gioRa}</td>
                            <td style="padding:14px 16px;">${statusBadge(r.TrangThai)}</td>
                            <td style="padding:14px 16px;text-align:center;">
                                ${r.HinhAnh ? `<button onclick="showAttendanceImg('${r.HinhAnh}')" style="background:none;border:none;cursor:pointer;color:#6b7280;" title="Xem ảnh">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>` : '<span style="color:#e5e7eb;font-size:16px;">⊘</span>'}
                            </td>
                        </tr>`;
            });

            html += `</tbody></table>`;
            wrap.innerHTML = html;
        })
        .catch(() => {
            wrap.innerHTML = `<div style="text-align:center;padding:40px;color:#dc2626;font-size:14px;"><div style="font-size:32px;margin-bottom:8px;">⚠️</div>Không thể tải dữ liệu</div>`;
        });
    }

    window.loadAttendanceThisMonth = function() {
        const now = new Date();
        loadAttendance(now.getMonth() + 1, now.getFullYear());
    };

    window.loadAttendancePrevMonth = function() {
        const m = currentMonth || (new Date().getMonth() + 1);
        const y = currentYear  || new Date().getFullYear();
        const d = new Date(y, m - 2, 1);
        loadAttendance(d.getMonth() + 1, d.getFullYear());
    };

    window.onAttendanceMonthChange = function(val) {
        if (!val) return;
        const [y, m] = val.split('-');
        loadAttendance(parseInt(m), parseInt(y));
    };

    window.showAttendanceImg = function(url) {
        const overlay = document.createElement('div');
        overlay.style = 'position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
        overlay.innerHTML = `<img src="${url}" style="max-width:90vw;max-height:90vh;border-radius:12px;box-shadow:0 25px 50px rgba(0,0,0,.5);">`;
        overlay.onclick = () => overlay.remove();
        document.body.appendChild(overlay);
    };

    // Tự động load tháng hiện tại khi click vào tab chấm công
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-tab="attendance"]');
        if (btn && currentMonth === null) {
            window.loadAttendanceThisMonth();
        }
    });
})();
</script>
@endpush
