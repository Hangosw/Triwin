@extends('layouts.app')

@section('title', 'Danh sách lương - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Danh sách lương</h1>
        <p>Quản lý và xem danh sách lương của toàn bộ nhân viên</p>
    </div>

    <div class="card">
        <div class="action-bar" style="flex-wrap: wrap; gap: 12px;">
            {{-- Bộ lọc tháng/năm --}}
            <form method="GET" action="{{ route('salary.index') }}" id="filterForm"
                style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 600; white-space: nowrap; color: #374151;">Kỳ lương:</label>
                    <select name="thang" class="form-control" style="height: auto; padding: 6px 10px; min-width: 110px;"
                        onchange="document.getElementById('filterForm').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $thang ? 'selected' : '' }}>
                                Tháng {{ $m }}
                            </option>
                        @endfor
                    </select>
                    <select name="nam" class="form-control" style="height: auto; padding: 6px 10px; min-width: 90px;"
                        onchange="document.getElementById('filterForm').submit()">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $y == $nam ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>

            <div style="flex: 1; min-width: 200px;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="salarySearch" class="form-control"
                        placeholder="Tìm kiếm nhân viên, mã nhân viên...">
                </div>
            </div>

            <div class="action-buttons">
                <button id="btnTinhLuongHangLoat" class="btn btn-primary"
                    style="background:#0BAA4B; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Tính lương tự động
                </button>
                <button id="btnGuiMailLuong" class="btn btn-info"
                    style="background:#3b82f6; color:white; display:flex; align-items:center; gap:6px; border:none;">
                    <i class="bi bi-envelope-fill"></i>
                    Gửi Email
                </button>
                <button class="btn btn-secondary" style="display:flex; align-items:center; gap:6px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất báo cáo
                </button>
            </div>
        </div>
    </div>

    {{-- Tổng quan kỳ lương --}}
    @php
        $tongThucNhan = $luongs->sum('Luong');
        $tongLuongCoBan = $luongs->sum('LuongCoBan');
        $soNhanVien = $luongs->count();
    @endphp
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
        <div class="card"
            style="padding: 16px 20px; margin-bottom: 0; background: linear-gradient(135deg, #0BAA4B, #088c3d); color: white;">
            <div style="font-size: 13px; opacity: 0.85;">Tổng thực nhận tháng {{ $thang }}/{{ $nam }}</div>
            <div style="font-size: 22px; font-weight: 700; margin-top: 4px;">
                {{ number_format($tongThucNhan, 0, ',', '.') }} đ
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 13px; color: #6b7280;">Tổng lương cơ bản</div>
            <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-top: 4px;">
                {{ number_format($tongLuongCoBan, 0, ',', '.') }} đ
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 13px; color: #6b7280;">Số nhân viên</div>
            <div style="font-size: 22px; font-weight: 700; color: #1e293b; margin-top: 4px;">{{ $soNhanVien }} người</div>
        </div>
    </div>

    @if($luongs->isEmpty())
        <div class="card" style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Chưa có dữ liệu lương tháng
                {{ $thang }}/{{ $nam }}
            </div>
            <div style="font-size: 14px;">Bấm <strong>"Tính lương tự động"</strong> để tính và lưu dữ liệu kỳ này.</div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                <table class="table" id="salaryTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;"><strong>STT</strong></th>
                            <th>Nhân viên</th>
                            <th>Chức vụ</th>
                            <th>Lương cơ bản</th>
                            <th>Phụ cấp + Tăng ca</th>
                            <th>Khấu trừ</th>
                            <th style="color: #0BAA4B;">Thực nhận</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($luongs as $index => $luong)
                            @php
                                $nv = $luong->nhanVien;
                                $chucVu = $nv?->ttCongViec?->chucVu?->Ten ?? '—';
                                $isCongNhan = $luong->LoaiLuong === 0;
                            @endphp
                            <tr class="salary-row">
                                <td><strong>{{ $index + 1 }}</strong></td>
                                <td>
                                    <a href="{{ route('salary.detail', [$nv?->id, 'thang' => $thang, 'nam' => $nam]) }}"
                                        style="color: #0BAA4B; text-decoration: none; font-weight: 600;">
                                        {{ $nv?->Ten ?? '—' }}
                                    </a>
                                    <div style="font-size: 13px; color: #6b7280;">{{ $nv?->Ma }}</div>
                                    @if($isCongNhan)
                                        <span class="badge badge-orange" style="font-size:11px; margin-top:2px;">Công nhân</span>
                                    @else
                                        <span class="badge badge-info" style="font-size:11px; margin-top:2px;">Văn phòng</span>
                                    @endif
                                </td>
                                <td>{{ $chucVu }}</td>
                                <td class="font-medium">
                                    {{ number_format($luong->LuongCoBan, 0, ',', '.') }} đ
                                    @if($isCongNhan && $luong->SoNgayCong !== null)
                                        <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                            <i class="bi bi-calendar-check"></i>
                                            {{ $luong->SoNgayCong }} ngày
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if(($luong->PhuCap ?? 0) > 0 || ($luong->LuongTangCa ?? 0) > 0)
                                        @if(($luong->PhuCap ?? 0) > 0)
                                            <div style="font-size: 13px; color: #3b82f6;" title="Phụ cấp">
                                                <span style="opacity:.6;">PC</span> {{ number_format($luong->PhuCap, 0, ',', '.') }} đ
                                            </div>
                                        @endif
                                        @if(($luong->LuongTangCa ?? 0) > 0)
                                            <div style="font-size: 13px; color: #f97316;" title="Tăng ca">
                                                <span style="opacity:.6;">TC</span> {{ number_format($luong->LuongTangCa, 0, ',', '.') }} đ
                                            </div>
                                        @endif
                                    @else
                                        <span style="color:#d1d5db; font-size:13px;">—</span>
                                    @endif
                                </td>
                                <td style="color: #dc2626; white-space: nowrap;">
                                    -{{ number_format(($luong->KhauTruBaoHiem ?? 0) + ($luong->ThueTNCN ?? 0), 0, ',', '.') }} đ
                                    <div style="font-size: 11px; color: #9ca3af;">BH + Thuế</div>
                                </td>
                                <td>
                                    <strong style="color: #0BAA4B; font-size: 15px; white-space: nowrap;">
                                        {{ number_format($luong->Luong, 0, ',', '.') }} đ
                                    </strong>
                                </td>
                                <td>
                                    @if($luong->TrangThai == 1)
                                        <span class="badge badge-success">Đã thanh toán</span>
                                    @else
                                        <span class="badge badge-warning">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="{{ route('salary.detail', [$nv?->id, 'thang' => $thang, 'nam' => $nam]) }}"
                                            class="btn-icon" title="Xem chi tiết">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button class="btn-icon btn-show-slip" title="Xem phiếu lương" data-nv-id="{{ $nv?->id }}"
                                            data-thang="{{ $thang }}" data-nam="{{ $nam }}">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ========== MODAL PHIẾU LƯƠNG ========== --}}
    <div id="slipModal" style="
                        display:none; position:fixed; inset:0; z-index:9999;
                        background:rgba(0,0,0,0.55); align-items:center; justify-content:center;
                        overflow-y:auto; padding:24px 16px;
                    ">
        <div style="
                            background:#fff; border-radius:12px; width:100%; max-width:860px;
                            margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3);
                            display:flex; flex-direction:column; max-height:90vh;
                        ">
            {{-- Modal Header --}}
            <div style="
                                display:flex; justify-content:space-between; align-items:center;
                                padding:16px 20px; border-bottom:1px solid #e5e7eb;
                                background:linear-gradient(135deg,#0BAA4B,#088c3d);
                                border-radius:12px 12px 0 0;
                            ">
                <div style="color:#fff; font-size:16px; font-weight:700;">
                    <i class="bi bi-file-earmark-text"></i>
                    &nbsp;Phiếu Lương
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button id="btnPrintSlip" style="
                                        background:#fff; color:#0BAA4B; border:none; border-radius:6px;
                                        padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer;
                                        display:flex; align-items:center; gap:6px;
                                    ">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        In phiếu
                    </button>
                    <button onclick="closeSlipModal()" style="
                                        background:rgba(255,255,255,0.2); border:none; border-radius:6px;
                                        color:#fff; font-size:20px; cursor:pointer; width:32px; height:32px;
                                        display:flex; align-items:center; justify-content:center; line-height:1;
                                    ">✕</button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div id="slipContent" style="padding:20px; overflow-y:auto; flex:1;">
                <div style="text-align:center; padding:40px; color:#6b7280;">
                    <div style="font-size:32px; margin-bottom:8px;">⏳</div>
                    <div>Đang tải phiếu lương...</div>
                </div>
            </div>
        </div>
    </div>
    {{-- ========== END MODAL ========== --}}

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#salaryTable').DataTable({
                    language: {
                        "sProcessing": "Đang xử lý...",
                        "sLengthMenu": "Hiển thị _MENU_ mục",
                        "sZeroRecords": "Không tìm thấy dữ liệu",
                        "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                        "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                        "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                        "sSearch": "Tìm kiếm:",
                        "oPaginate": {
                            "sFirst": "Đầu",
                            "sPrevious": "Trước",
                            "sNext": "Tiếp",
                            "sLast": "Cuối"
                        }
                    },
                    responsive: true,
                    autoWidth: false,
                    pageLength: 25,
                    dom: 'rtip',
                    columnDefs: [
                        { orderable: false, targets: [8] }
                    ]
                });

                // Custom Search
                $('#salarySearch').on('keyup', function () {
                    table.search(this.value).draw();
                });

                // Nút tính lương hàng loạt
                document.getElementById('btnTinhLuongHangLoat').addEventListener('click', function () {
                    const thang = document.querySelector('select[name="thang"]').value;
                    const nam = document.querySelector('select[name="nam"]').value;

                    Swal.fire({
                        title: `Tính lương tháng ${thang}/${nam}?`,
                        html: `Hệ thống sẽ tính lương tự động cho <strong>toàn bộ nhân viên có hợp đồng active</strong> trong kỳ <strong>tháng ${thang}/${nam}</strong>.<br><br>Nếu đã tồn tại dữ liệu, sẽ <span style="color:#f97316;font-weight:600;">cập nhật lại</span>.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0BAA4B',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bi bi-lightning-charge-fill"></i> Xác nhận tính lương',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        Swal.fire({
                            title: 'Đang tính lương...',
                            html: `Đang xử lý kỳ lương tháng ${thang}/${nam}, vui lòng chờ.`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading(),
                        });

                        fetch('{{ route('salary.tinh-luong-hang-loat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ thang, nam }),
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success && data.thanh_cong > 0) {
                                    let html = data.message;
                                    if (data.bo_qua > 0 && data.errors?.length) {
                                        html += '<br><br><details style="text-align:left;font-size:12px;color:#dc2626;">'
                                            + '<summary style="cursor:pointer;">Xem chi tiết lỗi</summary><ul style="margin-top:6px;">'
                                            + data.errors.map(e => `<li>${e}</li>`).join('')
                                            + '</ul></details>';
                                    }
                                    Swal.fire({
                                        title: 'Hoàn thành!',
                                        html,
                                        icon: 'success',
                                        confirmButtonColor: '#0BAA4B',
                                        confirmButtonText: 'OK',
                                    }).then(() => window.location.reload());
                                } else if (data.success && data.bo_qua > 0) {
                                    const errList = (data.errors ?? []).map(e => `<li style="font-size:12px;">${e}</li>`).join('');
                                    Swal.fire({
                                        title: 'Tất cả thất bại!',
                                        html: `<b>${data.message}</b><br><ul style="text-align:left;margin-top:8px;color:#dc2626;">${errList}</ul>`,
                                        icon: 'error',
                                        confirmButtonColor: '#0BAA4B',
                                    });
                                } else {
                                    Swal.fire('Lỗi', data.message ?? 'Có lỗi xảy ra.', 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                            });
                    });
                });

                // Nút gửi mail lương hàng loạt
                document.getElementById('btnGuiMailLuong').addEventListener('click', function () {
                    const thang = document.querySelector('select[name="thang"]').value;
                    const nam = document.querySelector('select[name="nam"]').value;

                    Swal.fire({
                        title: `Gửi email phiếu lương tháng ${thang}/${nam}?`,
                        text: `Hệ thống sẽ gửi email phiếu lương chi tiết cho toàn bộ nhân viên có dữ liệu lương trong tháng ${thang}/${nam}.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bi bi-envelope-fill"></i> Xác nhận gửi mail',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        Swal.fire({
                            title: 'Đang gửi email...',
                            html: `Đang xử lý gửi phiếu lương tháng ${thang}/${nam}, vui lòng chờ.`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading(),
                        });

                        fetch('{{ route('salary.gui-mail') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ thang, nam }),
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    let html = data.message;
                                    if (data.gui_loi > 0 && data.errors?.length) {
                                        html += '<br><br><details style="text-align:left;font-size:12px;color:#dc2626;">'
                                            + '<summary style="cursor:pointer;">Xem chi tiết lỗi</summary><ul style="margin-top:6px;">'
                                            + data.errors.map(e => `<li>${e}</li>`).join('')
                                            + '</ul></details>';
                                    }
                                    Swal.fire({
                                        title: 'Hoàn thành!',
                                        html,
                                        icon: 'success',
                                        confirmButtonColor: '#0BAA4B',
                                        confirmButtonText: 'OK',
                                    });
                                } else {
                                    Swal.fire('Lỗi', data.message ?? 'Có lỗi xảy ra.', 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                            });
                    });
                });

                // ===== MODAL PHIẾU LƯƠNG =====
                const slipModal = document.getElementById('slipModal');
                const slipContent = document.getElementById('slipContent');
                const btnPrint = document.getElementById('btnPrintSlip');

                const LOADING_HTML = `
                                            <div style="text-align:center;padding:48px;color:#6b7280;">
                                                <div style="font-size:36px;margin-bottom:10px;">⏳</div>
                                                <div style="font-size:14px;">Đang tải phiếu lương...</div>
                                            </div>`;

                function openSlipModal(nvId, thang, nam) {
                    slipContent.innerHTML = LOADING_HTML;
                    slipModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    fetch(`/salary/slip/${nvId}?thang=${thang}&nam=${nam}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(r => {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.text();
                        })
                        .then(html => { slipContent.innerHTML = html; })
                        .catch(err => {
                            slipContent.innerHTML = `
                                                    <div style="text-align:center;padding:48px;color:#dc2626;">
                                                        <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
                                                        <div>Không thể tải phiếu lương.<br><small style="color:#9ca3af;">${err.message}</small></div>
                                                    </div>`;
                        });
                }

                window.closeSlipModal = function () {
                    slipModal.style.display = 'none';
                    document.body.style.overflow = '';
                    slipContent.innerHTML = LOADING_HTML;
                };

                // Click backdrop để đóng
                slipModal.addEventListener('click', function (e) {
                    if (e.target === slipModal) window.closeSlipModal();
                });

                // ESC để đóng
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && slipModal.style.display === 'flex') {
                        window.closeSlipModal();
                    }
                });

                // Gắn sự kiện (Event Delegation) cho tất cả nút phiếu lương
                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.btn-show-slip');
                    if (btn) {
                        const nvId = btn.dataset.nvId;
                        const thang = btn.dataset.thang;
                        const nam = btn.dataset.nam;
                        openSlipModal(nvId, thang, nam);
                    }
                });

                // Nút In phiếu
                btnPrint.addEventListener('click', function () {
                    const printWin = window.open('', '_blank', 'width=950,height=700');
                    printWin.document.write(`
                                                <!DOCTYPE html><html><head>
                                                <meta charset="UTF-8">
                                                <title>Phiếu Lương</title>
                                                <style>
                                                    body { font-family: Arial, sans-serif; font-size:13px; margin:20px; }
                                                    @media print { body { margin: 0; } }
                                                </style>
                                                <\/head><body>${slipContent.innerHTML}<\/body><\/html>`);
                    printWin.document.close();
                    printWin.focus();
                    setTimeout(() => { printWin.print(); }, 500);
                });
                // ===== END MODAL PHIẾU LƯƠNG =====

            });
        </script>
    @endpush
@endsection
