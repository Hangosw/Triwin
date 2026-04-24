@extends('layouts.app')

@section('title', 'Quản lý Công tác - HRM')

@section('content')
    <div class="page-header">
        <h1>Quá trình công tác</h1>
        <p>{{ auth()->user()->hasAnyRole(['Nhân viên', 'Nhân Viên']) ? 'Quản lý quá trình công tác của bạn' : 'Quản lý quá trình công tác của toàn bộ nhân viên' }}</p>
    </div>

    <div class="card" style="padding: 12px 16px;">
        <div class="row g-2 align-items-center">
            {{-- Search: full width on mobile, flexible on desktop --}}
            <div class="col-12 col-md">
                <div class="search-bar" style="min-width: unset;">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="congTacSearch" class="form-control"
                        placeholder="Tìm kiếm nhân viên, đơn vị, chức vụ...">
                </div>
            </div>

            {{-- Button: full width on mobile, auto on desktop --}}
            @can('Tạo Yêu Cầu Công Tác')
                <div class="col-12 col-md-auto">
                    <a href="{{ route('cong-tac.taoView') }}"
                        class="btn btn-primary w-100"
                        style="background:#0BAA4B; border-color:#0BAA4B; gap: 8px; padding: 10px 20px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tạo công tác</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    @if($quatrinhs->isEmpty())
        <div class="card" style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 12px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">Chưa có dữ liệu công tác</div>
            <div style="font-size: 14px;">Chưa có quá trình công tác nào được ghi nhận trên hệ thống.</div>
        </div>
    @else
        <div class="card">
            <div class="table-container">
                {{-- Bảng có 8 cột: index 0..7 --}}
                <table class="table" id="congTacTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">STT</th>       {{-- 0 --}}
                            <th>Nhân viên</th>                                           {{-- 1 --}}
                            <th>Phòng ban, Chức vụ</th>                                 {{-- 2 --}}
                            <th>Địa điểm</th>                                           {{-- 3 --}}
                            <th>Từ ngày</th>                                            {{-- 4 --}}
                            <th>Đến ngày</th>                                           {{-- 5 --}}
                            <th>Ghi chú</th>                                            {{-- 6 --}}
                            <th>Trạng thái</th>                                         {{-- 7 --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quatrinhs as $index => $qt)
                            @php
                                $nv = $qt->nhanVien;
                                $phongBan = $qt->phongBan;
                                $chucVu = $qt->chucVu;
                                $isCurrent = is_null($qt->DenNgay) || \Carbon\Carbon::parse($qt->DenNgay)->endOfDay()->isFuture();
                            @endphp
                            <tr class="congtac-row"
                                data-id="{{ $qt->id }}"
                                data-nhanvien-id="{{ $nv?->id }}"
                                data-phongban-id="{{ $qt->PhongBanId }}"
                                data-chucvu-id="{{ $qt->ChucVuId }}"
                                data-tungay="{{ \Carbon\Carbon::parse($qt->TuNgay)->format('d/m/Y') }}"
                                data-denngay="{{ $qt->DenNgay ? \Carbon\Carbon::parse($qt->DenNgay)->format('d/m/Y') : '' }}"
                                data-diadiem="{{ $qt->DiaDiem }}"
                                data-ghichu="{{ $qt->GhiChu }}"
                                style="cursor: pointer;">
                                {{-- col 0 --}}
                                <td style="text-align: center; font-weight: 600; color: var(--text-secondary);">{{ $index + 1 }}</td>
                                {{-- col 1 --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary);">{{ $nv?->Ten ?? '—' }}</div>
                                    <div style="font-size: 13px; color: var(--text-secondary);">{{ $nv?->Ma ?? '' }}</div>
                                </td>
                                {{-- col 2 --}}
                                <td>
                                    <div style="font-weight: 500; color: #0BAA4B;">{{ $phongBan?->Ten ?? '—' }}</div>
                                    <div style="font-size: 13px; color: var(--text-secondary);">
                                        <i class="bi bi-briefcase" style="font-size: 11px;"></i>
                                        {{ $chucVu?->Ten ?? '—' }}
                                    </div>
                                </td>
                                {{-- col 3 --}}
                                <td>
                                    <div style="font-weight: 500;">{{ $qt->DiaDiem ?? '—' }}</div>
                                </td>
                                {{-- col 4 --}}
                                <td>
                                    @if($qt->TuNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->TuNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                {{-- col 5 --}}
                                <td>
                                    @if($qt->DenNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->DenNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                {{-- col 6 --}}
                                <td>
                                    <div style="font-size: 13px; color: var(--text-secondary); max-width: 250px;" title="{{ $qt->GhiChu }}">
                                        {{ $qt->GhiChu ?? '—' }}
                                    </div>
                                </td>
                                {{-- col 7 --}}
                                <td>
                                    @if($isCurrent)
                                        <span class="badge" style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">Đang công tác</span>
                                    @else
                                        <span class="badge" style="background: #F3F4F6; color: #374151; border: 1px solid #E5E7EB;">Đã kết thúc</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-main);">
                <div style="font-size: 14px; color: var(--text-secondary);">
                    Hiển thị <strong>{{ $quatrinhs->count() }}</strong> quá trình công tác
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Xem & Sửa Công Tác -->
    <div class="modal fade" id="modalCongTac" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formEditCongTac" method="POST">
                    @csrf
                    <div class="modal-header" style="background-color: #0BAA4B; color: white;">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Chi tiết Quá trình Công tác</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                                <select name="NhanVienId" id="edit_NhanVienId" class="form-select select2-modal" required>
                                    @foreach($nhanViens as $nv)
                                        <option value="{{ $nv->id }}">{{ $nv->Ma }} - {{ $nv->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Phòng ban công tác <span class="text-danger">*</span></label>
                                <select name="PhongBanId" id="edit_PhongBanId" class="form-select select2-modal" required>
                                    @foreach($phongBans as $pb)
                                        <option value="{{ $pb->id }}">{{ $pb->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Chức vụ phụ trách <span class="text-danger">*</span></label>
                                <select name="ChucVuId" id="edit_ChucVuId" class="form-select select2-modal" required>
                                    @foreach($chucVus as $cv)
                                        <option value="{{ $cv->id }}">{{ $cv->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Từ ngày <span class="text-danger">*</span></label>
                                <input type="text" name="TuNgay" id="edit_TuNgay" class="form-control datepicker" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Đến ngày</label>
                                <input type="text" name="DenNgay" id="edit_DenNgay" class="form-control datepicker">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa điểm công tác</label>
                                <input type="text" name="DiaDiem" id="edit_DiaDiem" class="form-control" placeholder="Nhập địa điểm...">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="GhiChu" id="edit_GhiChu" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #0BAA4B; border-color: #0BAA4B;">
                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Bảng có 8 cột, index 0..7
            // 0=STT, 1=NV, 2=PB/CV, 3=DiaDiem, 4=TuNgay, 5=DenNgay, 6=GhiChu, 7=TrangThai
            const table = $('#congTacTable').DataTable({
                
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                dom: 'rtip',
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0] },
                    // Ưu tiên 1: luôn hiển thị
                    { responsivePriority: 1, targets: [0, 1, 7] },   // STT, NV, TrangThai
                    // Ưu tiên 2: hiển thị nếu còn chỗ
                    { responsivePriority: 2, targets: [2, 4] },      // PB/CV, TuNgay
                    // Ưu tiên 3: hiển thị nếu còn chỗ
                    { responsivePriority: 3, targets: [3] },         // DiaDiem
                    // Ưu tiên 10004: ẩn vào child row trước
                    { responsivePriority: 10004, targets: [5, 6] }   // DenNgay, GhiChu
                ]
            });

            // Khởi tạo Select2 cho Modal
            $('.select2-modal').select2({
                dropdownParent: $('#modalCongTac'),
                width: '100%'
            });

            // Custom Search
            $('#congTacSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Click vào row để mở modal.
            // - Bỏ qua td.dtr-control (nút mở/đóng child row của DataTables Responsive)
            // - Bỏ qua click bên trong child row (tr.child) vì closest('tr.congtac-row') sẽ trả về rỗng
            $('#congTacTable tbody').on('click', 'td:not(.dtr-control)', function () {
                const row = $(this).closest('tr.congtac-row');
                if (!row.length) return; // click trong child row → bỏ qua

                const id        = row.data('id');
                const nhanVienId = row.data('nhanvien-id');
                const phongBanId = row.data('phongban-id');
                const chucVuId   = row.data('chucvu-id');
                const tuNgay     = row.data('tungay');
                const denNgay    = row.data('denngay');
                const diaDiem    = row.data('diadiem');
                const ghiChu     = row.data('ghichu');

                $('#edit_NhanVienId').val(nhanVienId).trigger('change');
                $('#edit_PhongBanId').val(phongBanId).trigger('change');
                $('#edit_ChucVuId').val(chucVuId).trigger('change');
                $('#edit_TuNgay').val(tuNgay);
                $('#edit_DenNgay').val(denNgay);
                $('#edit_DiaDiem').val(diaDiem);
                $('#edit_GhiChu').val(ghiChu);

                $('#formEditCongTac').attr('action', `/cong-tac/update/${id}`);
                $('#modalCongTac').modal('show');
            });
        });
    </script>
@endpush
