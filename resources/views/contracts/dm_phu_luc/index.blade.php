@extends('layouts.app')

@section('title', 'Danh mục phụ lục - Triwin')

@section('content')
    <div class="page-header">
        <h1>Danh mục phụ lục</h1>
        <p>Cấu hình các điều khoản/loại phụ lục hợp đồng</p>
    </div>

    <div class="card">
        <div class="action-bar" style="border-bottom: none; display: flex; justify-content: flex-end; padding: 20px 24px;">
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm danh mục mới
            </button>
        </div>

        <div style="padding: 0 24px 24px 24px;">
            <table id="dmPhuLucTable" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="50" style="text-align: center;">STT</th>
                        <th>Từ khóa (Key)</th>
                        <th>Nội dung điều khoản</th>
                        <th>Tính BHXH</th>
                        <th>Trạng thái</th>
                        <th width="80">Thao tác</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="dmPhuLucModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm danh mục phụ lục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="background:none; border:none; font-size: 24px;">&times;</button>
                </div>
                <form id="dmPhuLucForm">
                    @csrf
                    <input type="hidden" id="itemId" name="id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Từ khóa (Key value)</label>
                            <input type="text" class="form-control" id="keyvalue" name="keyvalue" required
                                placeholder="Ví dụ: DIEUKHOAN_1">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Nội dung chi tiết</label>
                            <textarea class="form-control" id="noi_dung" name="noi_dung" rows="5" required
                                placeholder="Nhập nội dung điều khoản phụ lục..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control select2-modal" id="TrangThai" name="TrangThai" required>
                                <option value="mo">Mở (Đang sử dụng)</option>
                                <option value="khoa">Khóa (Ngưng sử dụng)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check form-switch p-0" style="display: flex; align-items: center; gap: 40px;">
                                <label class="form-label mb-0" style="cursor: pointer;" for="is_bhxh">Có đóng BHXH</label>
                                <input class="form-check-input" type="checkbox" id="is_bhxh" name="is_bhxh" value="1"
                                    style="width: 40px; height: 20px; cursor: pointer; margin-left: 0;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            margin: 30px auto;
            max-width: 800px;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        body.dark-theme .modal-content {
            background-color: #1a1d27;
            color: #e8eaf0;
            border: 1px solid #2e3349;
        }

        body.dark-theme .modal-header,
        body.dark-theme .modal-footer {
            border-color: #2e3349;
            background-color: #21263a;
        }

        body.dark-theme .btn-close {
            color: #e8eaf0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let table;
        $(document).ready(function () {
            table = $('#dmPhuLucTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                order: [[1, 'asc']],
                ajax: '{{ route('hop-dong.dm-phu-luc.data') }}',
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    class: 'text-center',
                    width: '50px',
                    render: () => ''
                },
                {
                    data: 'keyvalue',
                    render: (data) => `<span class="font-medium text-primary">${data}</span>`
                },
                {
                    data: 'noi_dung',
                    render: (data) => data.length > 100 ? data.substring(0, 100) + '...' : data
                },
                {
                    data: 'is_bhxh',
                    render: (data) => data ? '<span class="badge badge-info">Có BHXH</span>' : '<span class="badge badge-gray">Không BHXH</span>'
                },
                {
                    data: 'TrangThai',
                    render: (data) => {
                        if (data === 'mo') return '<span class="badge badge-success">Mở</span>';
                        return '<span class="badge badge-danger">Khóa</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                                                        <div style="display: flex; gap: 8px;">
                                                            <button class="btn-icon" onclick='openEditModal(${JSON.stringify(row)})' title="Sửa" style="color: #0BAA4B;">
                                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    `;
                    }
                }
                ],
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
                }
            });

            // Đảm bảo số thứ tự luôn bắt đầu từ 1 khi sort hoặc search (server-side)
            table.on('draw.dt', function () {
                var info = table.page.info();
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + info.start;
                });
            });

            $('#dmPhuLucForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#itemId').val();
                const url = id ? `{{ route('hop-dong.dm-phu-luc.index') }}/update/${id}` :
                    '{{ route('hop-dong.dm-phu-luc.store') }}';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Thành công!', res.message, 'success');
                            closeModal();
                            table.ajax.reload();
                        } else {
                            Swal.fire('Lỗi!', res.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                    }
                });
            });

            $('.btn-close, .btn-secondary').click(closeModal);
        });

        function openCreateModal() {
            $('#modalTitle').text('Thêm danh mục phụ lục');
            $('#dmPhuLucForm')[0].reset();
            $('#itemId').val('');
            $('#TrangThai').val('mo').trigger('change');
            $('#is_bhxh').prop('checked', false);
            $('#dmPhuLucModal').addClass('show');
            initSelect2InModal();
        }

        function openEditModal(item) {
            $('#modalTitle').text('Chỉnh sửa danh mục phụ lục');
            $('#itemId').val(item.id);
            $('#keyvalue').val(item.keyvalue);
            $('#noi_dung').val(item.noi_dung);
            $('#TrangThai').val(item.TrangThai).trigger('change');
            $('#is_bhxh').prop('checked', !!item.is_bhxh);
            $('#dmPhuLucModal').addClass('show');
            initSelect2InModal();
        }

        function initSelect2InModal() {
            $('.select2-modal').select2({
                dropdownParent: $('#dmPhuLucModal'),
                width: '100%'
            });
        }

        function closeModal() {
            $('#dmPhuLucModal').removeClass('show');
        }
    </script>
@endpush