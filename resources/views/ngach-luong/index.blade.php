@extends('layouts.app')

@section('title', 'Quản lý Ngạch lương - Vietnam Rubber Group')

@push('styles')
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 30px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .page-header p { color: #6b7280; margin: 0; }
        .card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); margin-bottom: 24px; border: none; }
        .action-bar { padding: 20px 24px; display: flex; justify-content: flex-end; align-items: center; gap: 16px; border-bottom: 1px solid #e5e7eb; }
        .action-buttons { display: flex; gap: 8px; }
        .table-container { padding: 24px; }
        .table { width: 100%; margin: 0; border: none !important; }
        .table thead th { background-color: #f9fafb !important; color: #374151; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; border-bottom: 2px solid #e5e7eb !important; }
        .table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #e5e7eb; font-size: 14px; color: #1f2937; }
        .table tbody tr:hover { background-color: #f9fafb; cursor: pointer; }
        .badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; gap: 6px; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-secondary { background-color: #f3f4f6; color: #374151; }

        /* Dark Theme */
        body.dark-theme .page-header h1, body.dark-theme .page-header p { color: #e8eaf0; }
        body.dark-theme .card { background-color: #1a1d27; border-color: #2e3349; }
        body.dark-theme .table tbody td { color: #e8eaf0; border-bottom-color: #2e3349; }
        body.dark-theme .table thead th { background-color: #21263a !important; color: #c3c8da !important; border-bottom-color: #2e3349 !important; }
        body.dark-theme .action-bar { border-bottom-color: #2e3349; }
        body.dark-theme .table tbody tr:hover { background-color: #21263a; }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Quản lý Ngạch lương</h1>
        <p>Hệ thống cấu hình ngạch, bậc và hệ số lương</p>
    </div>

    <div class="card">
        <div class="action-bar" style="border-bottom: none;">
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="bi bi-plus-lg"></i> Thêm ngạch lương
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table" id="ngachLuongTable">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">STT</th>
                        <th>Thông tin ngạch lương</th>
                        <th class="text-center">Nhóm ngạch</th>
                        <th class="text-center">Cấu hình bậc</th>
                        <th class="text-center">Nhân sự áp dụng</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ngachLuongs as $index => $ngach)
                        <tr class="ngach-row" data-ngach='@json($ngach)'>
                            <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
                            <td>
                                <div style="font-weight: 600;">{{ $ngach->Ten }}</div>
                                <div style="font-size: 12px; color: #6b7280;">Mã: {{ $ngach->Ma }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $ngach->Nhom }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ngach->bac_luongs_count > 0 ? 'badge-info' : 'badge-secondary' }}">
                                    {{ $ngach->bac_luongs_count }} bậc
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $ngach->dien_bien_luongs_count > 0 ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $ngach->dien_bien_luongs_count }} nhân viên
                                </span>
                            </td>
                            <td class="text-center">
                                @if($ngach->TrangThai == 1)
                                    <span class="badge badge-success">Hoạt động</span>
                                @else
                                    <span class="badge badge-danger">Đã khóa</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons" style="justify-content: center;">
                                    <button class="btn btn-sm btn-icon btn-outline-primary btn-edit-ngach" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-outline-{{ $ngach->TrangThai == 1 ? 'danger' : 'success' }}"
                                        onclick="toggleStatus({{ $ngach->Id }}, {{ $ngach->TrangThai }})"
                                        title="{{ $ngach->TrangThai == 1 ? 'Khóa' : 'Mở khóa' }}">
                                        <i class="bi {{ $ngach->TrangThai == 1 ? 'bi-lock' : 'bi-unlock' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="ngachLuongModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                <div class="modal-header" style="border-bottom: 1px solid #f3f4f6; padding: 20px 24px;">
                    <h5 class="modal-title" style="font-weight: 700; color: #1f2937;">Thêm ngạch lương mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ngachLuongForm">
                    @csrf
                    <input type="hidden" name="Id" id="inputNgachId">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="form-group mb-4">
                            <label class="form-label">Mã ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Ma" id="inputMa" placeholder="VD: 01.003"
                                required>
                            <div class="invalid-feedback" id="errorMa"></div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Tên ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Ten" id="inputTen" placeholder="VD: Chuyên viên"
                                required>
                            <div class="invalid-feedback" id="errorTen"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nhóm ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Nhom" id="inputNhom" placeholder="VD: A1"
                                required>
                            <div class="invalid-feedback" id="errorNhom"></div>
                        </div>

                        <!-- Phần quản lý Bậc lương (Chỉ hiện khi Sửa) -->
                        <div id="sectionBacLuong" style="display: none; border-top: 1px solid #f3f4f6; padding-top: 24px; margin-top: 24px;">
                            <h6 style="font-weight: 700; color: #1f2937; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="bi bi-layers-fill" style="color: #0BAA4B;"></i> Danh sách bậc lương</span>
                                <button type="button" class="btn btn-sm btn-success" style="padding: 2px 10px; font-size: 12px;" onclick="addEmptyBacRow()">
                                    <i class="bi bi-plus-lg"></i> Thêm bậc mới
                                </button>
                            </h6>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <table class="table table-sm table-hover mb-0" style="font-size: 13px;">
                                    <thead style="background-color: #f9fafb; position: sticky; top: 0; z-index: 1;">
                                        <tr>
                                            <th class="text-center" style="width: 80px;">Bậc</th>
                                            <th class="text-center">Hệ số</th>
                                            <th class="text-center" style="width: 100px;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listBacLuongBody">
                                        <!-- Dynamic content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 16px 24px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Lưu ngạch lương</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal (Large) -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #0BAA4B, #088a3d); color: white; border-radius: 16px 16px 0 0; padding: 24px 32px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div
                            style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" style="font-weight: 700; margin: 0; font-size: 20px;">Chi tiết ngạch
                                lương: <span id="detailMa"></span></h5>
                            <p style="margin: 0; opacity: 0.9; font-size: 14px;">Thông tin định nghĩa và hệ thống bậc lương
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 32px;">
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div
                                style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 600; margin-bottom: 8px;">
                                    Tên ngạch</div>
                                <div id="detailTen" style="font-weight: 700; color: #1e293b; font-size: 18px;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 600; margin-bottom: 8px;">
                                    Nhóm ngạch</div>
                                <div id="detailNhom" style="font-weight: 700; color: #1e293b; font-size: 18px;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <div
                                    style="color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 600; margin-bottom: 8px;">
                                    Trạng thái</div>
                                <div id="detailStatus" style="font-weight: 700;"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h6
                            style="font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-layers" style="color: #0BAA4B;"></i>
                            Danh sách bậc lương
                        </h6>
                        <div class="table-responsive" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">STT</th>
                                        <th class="text-center">Bậc</th>
                                        <th class="text-center">Hệ số</th>
                                        <th class="text-center">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody id="detailBacLuongBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 20px 32px; background: #f8fafc; border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-center {
            text-align: center;
        }

        .btn-sm {
            font-size: 12px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0;
            transition: all 0.2s;
        }

        .btn-lock {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-lock:hover {
            background-color: #dc2626;
            color: white;
        }

        .btn-unlock {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .btn-unlock:hover {
            background-color: #059669;
            color: white;
        }

        #ngachLuongTable tbody tr:hover {
            background-color: #f0fdf4 !important;
        }

        body.dark-theme .modal-content {
            background-color: #1a1d27;
            color: white;
        }

        body.dark-theme .modal-header {
            border-bottom-color: #2e3349;
        }

        body.dark-theme .modal-footer {
            border-top-color: #2e3349;
            background-color: #161922;
        }

        body.dark-theme #detailTen,
        body.dark-theme #detailNhom {
            color: #e8eaf0;
        }

        body.dark-theme th {
            background: #21263a !important;
            color: #8b93a8;
        }

        body.dark-theme div[style*="background: #f8fafc"] {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }
    </style>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Khởi tạo DataTable
            $('#ngachLuongTable').DataTable({
                language: {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Hiển thị _MENU_ dòng",
                    "sZeroRecords": "Không tìm thấy dữ liệu",
                    "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": { "sFirst": "Đầu", "sPrevious": "Trước", "sNext": "Tiếp", "sLast": "Cuối" }
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });

            // Khởi tạo Bootstrap Modals
            window.ngachModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('ngachLuongModal'));
            window.detailModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal'));

            $(document).on('click', '.ngach-row', function (e) {
                if ($(e.target).closest('button, a, input').length) return;
                const data = $(this).data('ngach');
                openDetailModal(data);
            });

            // Sự kiện Click nút Sửa
            $(document).on('click', '.btn-edit-ngach', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const data = $(this).closest('tr').data('ngach');
                openEditModal(data);
            });

            // Gửi Form (Thêm/Sửa)
            $('#ngachLuongForm').on('submit', async function (e) {
                e.preventDefault();

                const id = $('#inputNgachId').val();
                const url = id 
                    ? `{{ url('salary/ngach-luong/update') }}/${id}` 
                    : `{{ route('salary.ngach-luong.store') }}`;
                
                clearErrors();
                $('#btnSubmit').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Đang lưu...');

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(this)))
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        if (response.status === 422) {
                            Object.keys(result.errors).forEach(key => {
                                $(`#input${key}`).addClass('is-invalid');
                                $(`#error${key}`).text(result.errors[key][0]);
                            });
                        } else {
                            Swal.fire('Lỗi', result.message || 'Có lỗi xảy ra', 'error');
                        }
                    }
                } catch (error) {
                    Swal.fire('Lỗi', 'Có lỗi kết nối hệ thống', 'error');
                } finally {
                    $('#btnSubmit').prop('disabled', false).html(id ? 'Cập nhật ngạch lương' : 'Lưu ngạch lương');
                }
            });
        });

        // --- Các hàm Global ---

        function openAddModal() {
            $('#ngachLuongModal .modal-title').text('Thêm ngạch lương mới');
            $('#inputNgachId').val('');
            $('#ngachLuongForm')[0].reset();
            $('#sectionBacLuong').hide(); // Ẩn khi thêm mới ngạch
            clearErrors();
            window.ngachModal.show();
        }

        function openEditModal(data) {
            const id = data.id || data.Id;
            $('#ngachLuongModal .modal-title').text('Chỉnh sửa ngạch lương');
            $('#inputNgachId').val(id);
            $('#inputMa').val(data.Ma);
            $('#inputTen').val(data.Ten);
            $('#inputNhom').val(data.Nhom);
            $('#btnSubmit').text('Cập nhật ngạch lương');
            
            $('#sectionBacLuong').show();
            const bacs = data.bac_luongs || data.bacLuongs || [];
            renderBacLuongRows(id, bacs);
            
            clearErrors();
            window.ngachModal.show();
        }

        function openDetailModal(data) {
            const bacs = data.bac_luongs || data.bacLuongs || [];
            document.getElementById('detailMa').innerText = data.Ma || '-';
            document.getElementById('detailTen').innerText = data.Ten || '-';
            document.getElementById('detailNhom').innerText = data.Nhom || '-';

            const statusEl = document.getElementById('detailStatus');
            if (data.TrangThai == 1) {
                statusEl.innerHTML = '<span class="badge" style="background-color: #d1fae5; color: #065f46; border: 1px solid #10b981;"><i class="bi bi-check-circle-fill"></i> Đang hoạt động</span>';
            } else {
                statusEl.innerHTML = '<span class="badge" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171;"><i class="bi bi-lock-fill"></i> Đang bị khóa</span>';
            }

            const body = document.getElementById('detailBacLuongBody');
            body.innerHTML = '';

            if (bacs && bacs.length > 0) {
                bacs.forEach((b, i) => {
                    const stepBac = b.Bac || b.bac || '';
                    const stepHeSo = b.HeSo || b.he_so || 0;
                    body.innerHTML += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td class="text-center">Bậc <span style="font-weight: 500;">${stepBac}</span></td>
                            <td class="text-center"><span style="font-weight: 600; color: #0BAA4B;">${parseFloat(stepHeSo).toFixed(2)}</span></td>
                            <td class="text-center text-muted">-</td>
                        </tr>
                    `;
                });
            } else {
                body.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Chưa có hệ thống bậc lương được cấu hình cho ngạch này</td></tr>';
            }

            window.detailModal.show();
        }

        // --- Bac Luong Management Inside Modal ---

        function renderBacLuongRows(ngachId, bacLuongs) {
            const body = $('#listBacLuongBody');
            body.empty();
            
            if (!bacLuongs || bacLuongs.length === 0) {
                body.append('<tr><td colspan="3" class="text-center py-4 text-muted">Chưa có bậc lương nào. Nhấn "Thêm bậc mới" để bắt đầu.</td></tr>');
                return;
            }

            bacLuongs.sort((a, b) => a.Bac - b.Bac).forEach(b => {
                const stepId = b.id || b.Id; // Hỗ trợ cả id và Id
                const stepBac = b.Bac || b.bac || '';
                const stepHeSo = b.HeSo || b.he_so || 0;
                
                body.append(`
                    <tr data-id="${stepId}" class="align-middle">
                        <td><input type="number" class="form-control form-control-sm text-center input-bac font-weight-bold" value="${stepBac}" style="border: none; background: transparent; color: #1f2937; min-width: 60px;"></td>
                        <td><input type="number" step="0.01" class="form-control form-control-sm text-center input-heso font-bold" value="${parseFloat(stepHeSo).toFixed(2)}" style="border: none; background: transparent; color: #0BAA4B;"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-link text-primary p-0 me-2" onclick="updateBac(${stepId}, this)" title="Lưu thay đổi"><i class="bi bi-save"></i></button>
                            <button type="button" class="btn btn-link text-danger p-0" onclick="deleteBac(${stepId}, this)" title="Xóa bậc"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `);
            });
        }

        function addEmptyBacRow() {
            const ngachId = $('#inputNgachId').val();
            const body = $('#listBacLuongBody');
            
            // Xóa thông báo trống nếu có
            if (body.find('td[colspan]').length) body.empty();
            
            const nextBac = body.find('tr').length + 1;
            
            const newRow = $(`
                <tr class="align-middle bg-light">
                    <td><input type="number" class="form-control form-control-sm text-center input-new-bac" value="${nextBac}"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm text-center input-new-heso" placeholder="0.00"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-link text-success p-0 me-2" onclick="saveNewBac(this)" title="Xác nhận"><i class="bi bi-check-circle-fill"></i></button>
                        <button type="button" class="btn btn-link text-muted p-0" onclick="$(this).closest('tr').remove()" title="Hủy"><i class="bi bi-x-circle"></i></button>
                    </td>
                </tr>
            `);
            body.append(newRow);
            newRow.find('.input-new-heso').focus();
        }

        async function saveNewBac(btn) {
            const row = $(btn).closest('tr');
            const data = {
                NgachLuongId: $('#inputNgachId').val(),
                Bac: row.find('.input-new-bac').val(),
                HeSo: row.find('.input-new-heso').val()
            };

            if (!data.Bac || !data.HeSo) {
                Swal.fire('Chú ý', 'Vui lòng nhập đầy đủ Bậc và Hệ số', 'warning');
                return;
            }

            try {
                const response = await fetch(`{{ route('salary.ngach-luong.bac-luong.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire('Lỗi', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error');
            }
        }

        async function updateBac(id, btn) {
            const row = $(btn).closest('tr');
            const data = {
                Bac: row.find('.input-bac').val(),
                HeSo: row.find('.input-heso').val()
            };

            try {
                const response = await fetch(`{{ url('salary/ngach-luong/bac-luong/update') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire({icon: 'success', title: 'Đã cập nhật', timer: 800, showConfirmButton: false});
                } else {
                    Swal.fire('Lỗi', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Lỗi', 'Không thể kết nối máy chủ', 'error');
            }
        }

        async function deleteBac(id, btn) {
            const result = await Swal.fire({
                title: 'Xóa bậc lương?',
                text: "Thao tác này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('salary/ngach-luong/bac-luong/delete') }}/${id}`, {
                        method: 'POST',
                        headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                    });
                    const res = await response.json();
                    if (res.success) {
                        $(btn).closest('tr').remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload()); // Reload để cập nhật số lượng bậc ở bảng chính
                    } else {
                        Swal.fire('Lỗi', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Lỗi', 'Lỗi hệ thống', 'error');
                }
            }
        }

        function clearErrors() {
            $('#ngachLuongForm .form-control').removeClass('is-invalid');
            $('#ngachLuongForm .invalid-feedback').text('');
        }

        function toggleStatus(id, currentStatus) {
            const action = currentStatus == 1 ? 'khóa' : 'mở khóa';
            Swal.fire({
                title: `Xác nhận ${action}?`,
                text: `Bạn có chắc chắn muốn ${action} ngạch lương này không?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: currentStatus == 1 ? '#dc2626' : '#0BAA4B',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`{{ url('salary/ngach-luong/toggle-status') }}/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const res = await response.json();
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi', res.message || 'Xảy ra lỗi khi thay đổi trạng thái', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Lỗi', 'Có lỗi kết nối hệ thống', 'error');
                    }
                }
            });
        }
    </script>
@endpush