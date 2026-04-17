@extends('layouts.app')

@section('title', 'Lịch sử lương - ' . $nv->Ten)

@section('content')
    <div class="page-header">
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <div>
                <h1>Lịch sử lương</h1>
                <p>Xem danh sách phiếu lương của nhân viên: <strong>{{ $nv->Ten }}</strong> ({{ $nv->Ma }})</p>
            </div>
            <a href="{{ url()->previous() == route('salary.index') ? route('salary.index') : route('dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table" id="historyTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">STT</th>
                        <th>Kỳ lương</th>
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
                            $thang = \Carbon\Carbon::parse($luong->ThoiGian)->format('n');
                            $nam = \Carbon\Carbon::parse($luong->ThoiGian)->format('Y');
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>
                                <strong>Tháng {{ $thang }}/{{ $nam }}</strong>
                            </td>
                            <td>{{ number_format($luong->LuongCoBan, 0, ',', '.') }} đ</td>
                            <td>
                                @php $totalAdd = ($luong->PhuCap ?? 0) + ($luong->LuongTangCa ?? 0); @endphp
                                @if($totalAdd > 0)
                                    <span style="color: #3b82f6;">+{{ number_format($totalAdd, 0, ',', '.') }} đ</span>
                                @else
                                    <span style="color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="color: #dc2626;">
                                -{{ number_format(($luong->KhauTruBaoHiem ?? 0) + ($luong->ThueTNCN ?? 0) + ($luong->TamUng ?? 0), 0, ',', '.') }} đ
                            </td>
                            <td>
                                <strong style="color: #0BAA4B;">{{ number_format($luong->Luong, 0, ',', '.') }} đ</strong>
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
                                    <a href="{{ route('salary.detail', [$nv->id, 'thang' => $thang, 'nam' => $nam]) }}"
                                        class="btn-icon" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn-icon btn-show-slip" title="Xem phiếu lương" 
                                        data-nv-id="{{ $nv->id }}"
                                        data-thang="{{ $thang }}" 
                                        data-nam="{{ $nam }}">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========== MODAL PHIẾU LƯƠNG (Copy from index.blade.php) ========== --}}
    <div id="slipModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; overflow-y:auto; padding:24px 16px;">
        <div style="background:#fff; border-radius:12px; width:100%; max-width:860px; margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3); display:flex; flex-direction:column; max-height:90vh;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #e5e7eb; background:linear-gradient(135deg,#0BAA4B,#088c3d); border-radius:12px 12px 0 0;">
                <div style="color:#fff; font-size:16px; font-weight:700;"><i class="bi bi-file-earmark-text"></i> Phiếu Lương</div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button id="btnPrintSlip" style="background:#fff; color:#0BAA4B; border:none; border-radius:6px; padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-printer"></i> In phiếu
                    </button>
                    <button onclick="closeSlipModal()" style="background:rgba(255,255,255,0.2); border:none; border-radius:6px; color:#fff; font-size:20px; cursor:pointer; width:32px; height:32px; display:flex; align-items:center; justify-content:center;">✕</button>
                </div>
            </div>
            <div id="slipContent" style="padding:20px; overflow-y:auto; flex:1;">
                <div style="text-align:center; padding:40px; color:#6b7280;">⏳ Đang tải...</div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                language: {
                    "sSearch": "Tìm nhanh:",
                    "sLengthMenu": "Hiện _MENU_ dòng",
                    "sInfo": "Dòng _START_ đến _END_ trong _TOTAL_ dòng",
                    "sZeroRecords": "Không có dữ liệu",
                    "oPaginate": { "sNext": "Sau", "sPrevious": "Trước" }
                },
                order: [[1, 'desc']],
                pageLength: 12
            });

            // AJAX handles for slip modal
            const slipModal = document.getElementById('slipModal');
            const slipContent = document.getElementById('slipContent');
            const btnPrint = document.getElementById('btnPrintSlip');

            window.openSlipModal = function(nvId, thang, nam) {
                slipContent.innerHTML = '<div style="text-align:center;padding:48px;">⏳ Đang tải phiếu lương...</div>';
                slipModal.style.display = 'flex';
                fetch(`/salary/slip/${nvId}?thang=${thang}&nam=${nam}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(r => r.text())
                    .then(html => { slipContent.innerHTML = html; });
            };

            window.closeSlipModal = function() { slipModal.style.display = 'none'; };

            $(document).on('click', '.btn-show-slip', function() {
                openSlipModal($(this).data('nv-id'), $(this).data('thang'), $(this).data('nam'));
            });

            btnPrint.addEventListener('click', function() {
                const printWin = window.open('', '_blank');
                printWin.document.write(`<html><head><title>Phiếu Lương</title></head><body>${slipContent.innerHTML}</body></html>`);
                printWin.document.close();
                printWin.focus();
                setTimeout(() => { printWin.print(); }, 500);
            });
        });
    </script>
    @endpush
@endsection
