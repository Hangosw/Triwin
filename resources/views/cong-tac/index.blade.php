@extends('layouts.app')

@section('title', 'Quản lý Công tác - HRM')

@section('content')
    <div class="page-header">
        <h1>Quá trình công tác</h1>
        <p>{{ auth()->user()->hasRole('Nhân Viên') ? 'Quản lý quá trình công tác của bạn' : 'Quản lý quá trình công tác của toàn bộ nhân viên' }}</p>
    </div>

    <div class="card">
        <div class="action-bar" style="flex-wrap: wrap; gap: 12px;">
            <div style="flex: 1; min-width: 200px;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="congTacSearch" class="form-control"
                        placeholder="Tìm kiếm nhân viên, đơn vị, chức vụ...">
                </div>
            </div>

            <div class="action-buttons">
                @can('Tạo Yêu Cầu Công Tác')
                    <a href="{{ route('cong-tac.taoView') }}" class="btn btn-primary" style="background:#0BAA4B;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tạo công tác
                    </a>
                @endcan
{{-- 
                <button class="btn btn-secondary" style="display:flex; align-items:center; gap:6px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất báo cáo
                </button>
--}}
            </div>
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
                <table class="table" id="congTacTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;"><strong>STT</strong></th>
                            <th>Nhân viên</th>
                            <th>Phòng ban, Chức vụ</th>
                            <th>Nội dung</th> {{-- New Column --}}
                            <th>Địa điểm</th>
                            <th>Ghi chú</th>
                            <th>Từ ngày</th>
                            <th>Đến ngày</th>
                            <th>Trạng thái</th>
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
                                <td style="text-align: center;"><strong>{{ $index + 1 }}</strong></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary);">
                                        {{ $nv?->Ten ?? '—' }}
                                    </div>
                                    <div style="font-size: 13px; color: var(--text-secondary);">{{ $nv?->Ma ?? '' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #0BAA4B;">{{ $phongBan?->Ten ?? '—' }}</div>
                                    <div style="font-size: 13px; color: var(--text-secondary);">
                                        <i class="bi bi-briefcase" style="font-size: 11px;"></i>
                                        {{ $chucVu?->Ten ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #4f46e5;">Công tác chuyên môn</div> {{-- Default or Placeholder Content --}}
                                </td>
                                <td>
                                    <div style="font-weight: 500;">{{ $qt->DiaDiem ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; color: var(--text-secondary); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $qt->GhiChu }}">
                                        {{ $qt->GhiChu ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($qt->TuNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->TuNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($qt->DenNgay)
                                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($qt->DenNgay)->format('d/m/Y') }}</div>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isCurrent)
                                        <span class="badge badge-success">Đang công tác</span>
                                    @else
                                        <span class="badge badge-gray">Đã kết thúc</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                style="padding: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-main);">
                <div style="font-size: 14px; color: var(--text-secondary);">
                    Hiển thị <strong>{{ $quatrinhs->count() }}</strong> quá trình công tác
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Xem & Sửa Công Tác -->
    <div class="modal fade" id="modalCongTac" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditCongTac" method="POST">
                    @csrf
                    <div class="modal-header" style="background-color: #0BAA4B; color: white;">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Chi tiết Quá trình Công tác</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                                <select name="NhanVienId" id="edit_NhanVienId" class="form-select select2-modal" required>
                                    @foreach($nhanViens as $nv)
                                        <option value="{{ $nv->id }}">{{ $nv->Ma }} - {{ $nv->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phòng ban công tác <span class="text-danger">*</span></label>
                                <select name="PhongBanId" id="edit_PhongBanId" class="form-select select2-modal" required>
                                    @foreach($phongBans as $pb)
                                        <option value="{{ $pb->id }}">{{ $pb->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chức vụ phụ trách <span class="text-danger">*</span></label>
                                <select name="ChucVuId" id="edit_ChucVuId" class="form-select select2-modal" required>
                                    @foreach($chucVus as $cv)
                                        <option value="{{ $cv->id }}">{{ $cv->Ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Từ ngày <span class="text-danger">*</span></label>
                                <input type="text" name="TuNgay" id="edit_TuNgay" class="form-control datepicker" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Đến ngày</label>
                                <input type="text" name="DenNgay" id="edit_DenNgay" class="form-control datepicker">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Địa điểm công tác</label>
                                <input type="text" name="DiaDiem" id="edit_DiaDiem" class="form-control" placeholder="Nhập địa điểm...">
                            </div>
                            <div class="col-md-12">
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

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#congTacTable').DataTable({
                    language: {
                        "sProcessing": "Đang xử lý...",
                        "sLengthMenu": "Hiển thị _MENU_ dòng",
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
                    pageLength: 10,
                    dom: 'rtip',
                    order: [[1, 'asc']], // Sắp xếp mặc định theo cột Nhân viên (index 1)
                    columnDefs: [
                        { targets: 0, orderable: false }
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

                // Xử lý khi click vào hàng trong bảng
                $('#congTacTable tbody').on('click', 'tr.congtac-row', function() {
                    const id = $(this).data('id');
                    const nhanVienId = $(this).data('nhanvien-id');
                    const phongBanId = $(this).data('phongban-id');
                    const chucVuId = $(this).data('chucvu-id');
                    const tuNgay = $(this).data('tungay');
                    const denNgay = $(this).data('denngay');
                    const diaDiem = $(this).data('diadiem');
                    const ghiChu = $(this).data('ghichu');

                    // Điền dữ liệu vào Modal
                    $('#edit_NhanVienId').val(nhanVienId).trigger('change');
                    $('#edit_PhongBanId').val(phongBanId).trigger('change');
                    $('#edit_ChucVuId').val(chucVuId).trigger('change');
                    $('#edit_TuNgay').val(tuNgay);
                    $('#edit_DenNgay').val(denNgay);
                    $('#edit_DiaDiem').val(diaDiem);
                    $('#edit_GhiChu').val(ghiChu);

                    // Cập nhật Action cho Form
                    $('#formEditCongTac').attr('action', `/cong-tac/update/${id}`);

                    // Mở Modal
                    $('#modalCongTac').modal('show');
                });
            });
        </script>
    @endpush
@endsection
