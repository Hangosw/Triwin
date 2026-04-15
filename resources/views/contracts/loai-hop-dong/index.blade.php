@extends('layouts.app')

@section('title', 'Cấu hình loại hợp đồng - Triwin')

@section('content')
    <div class="page-header">
        <h1>Cấu hình loại hợp đồng</h1>
        <p>Quản lý các loại hợp đồng lao động trong hệ thống</p>
    </div>

    <div class="card">
        <div class="action-bar" style="border-bottom: none; display: flex; justify-content: flex-end; padding: 20px 24px;">
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm loại hợp đồng
            </button>
        </div>

        <div style="padding: 0 24px 24px 24px;">
            <table id="loaiHopDongTable" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="50" style="text-align: center;">STT</th>
                        <th>Mã loại</th>
                        <th>Tên loại hợp đồng</th>
                        <th>Thời hạn (tháng)</th>
                        <th>Báo trước (ngày)</th>
                        <th>Đóng bảo hiểm</th>
                        <th>Trạng thái</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="loaiHopDongModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm loại hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="background:none; border:none; font-size: 24px;">&times;</button>
                </div>
                <form id="loaiHopDongForm">
                    @csrf
                    <input type="hidden" id="itemId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Mã loại hợp đồng</label>
                                    <input type="text" class="form-control" id="MaLoai" name="MaLoai" required
                                        placeholder="Ví dụ: HD-KD-01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Tên loại hợp đồng</label>
                                    <input type="text" class="form-control" id="TenLoai" name="TenLoai" required
                                        placeholder="Ví dụ: Hợp đồng không xác định thời hạn">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Thời hạn (tháng)</label>
                                    <input type="number" class="form-control" id="ThoiHanThang" name="ThoiHanThang" min="0" value="0"
                                        placeholder="Nhập 0 nếu không xác định thời hạn">
                                    <small class="text-muted">Giá trị 0 tương ứng với hợp đồng không xác định thời hạn.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Báo trước (ngày)</label>
                                    <input type="number" class="form-control" id="ThoiHanBaoTruoc" name="ThoiHanBaoTruoc" min="0" value="30">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check form-switch p-0" style="display: flex; align-items: center; gap: 40px;">
                                        <label class="form-label mb-0" style="cursor: pointer;" for="CoDongBaoHiem">Có đóng BHXH</label>
                                        <input class="form-check-input" type="checkbox" id="CoDongBaoHiem" name="CoDongBaoHiem" value="1"
                                            style="width: 40px; height: 20px; cursor: pointer; margin-left: 0;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-control" id="TrangThai" name="TrangThai" required>
                                        <option value="mo">Mở (Hoạt động)</option>
                                        <option value="khoa">Khóa (Ngưng sử dụng)</option>
                                    </select>
                                </div>
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
            table = $('#loaiHopDongTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                order: [[1, 'asc']],
                ajax: '{{ route('hop-dong.loai-hop-dong.data') }}',
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    class: 'text-center',
                    width: '50px',
                    render: () => ''
                },
                {
                    data: 'MaLoai',
                    render: (data) => `<span class="badge badge-gray" style="font-family: monospace;">${data}</span>`
                },
                {
                    data: 'TenLoai',
                    render: (data) => `<span class="font-medium text-primary">${data}</span>`
                },
                {
                    data: 'ThoiHanThang',
                    render: (data) => data === 0 ? '<span class="text-success font-medium">Không thời hạn</span>' : `<span>${data} tháng</span>`
                },
                {
                    data: 'ThoiHanBaoTruoc',
                    render: (data) => `<span>${data} ngày</span>`
                },
                {
                    data: 'CoDongBaoHiem',
                    render: (data) => data ? '<span class="badge badge-success">Có bảo hiểm</span>' : '<span class="badge badge-gray">Không bảo hiểm</span>'
                },
                {
                    data: 'TrangThai',
                    render: (data) => data === 'mo' ? '<span class="badge badge-success">Mở</span>' : '<span class="badge badge-danger">Khóa</span>'
                },
                {
                    data: null,
                    orderable: false,
                    render: (data, type, row) => {
                        const toggleTitle = row.TrangThai === 'mo' ? 'Khóa' : 'Mở';
                        const toggleColor = row.TrangThai === 'mo' ? '#dc2626' : '#0BAA4B';
                        const toggleIcon = row.TrangThai === 'mo' ? 
                            `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>` : 
                            `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>`;

                        return `
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-icon" onclick='openEditModal(${JSON.stringify(row)})' title="Sửa" style="color: #0BAA4B;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="btn-icon" onclick="toggleStatus(${row.id}, '${row.TrangThai}')" title="${toggleTitle}" style="color: ${toggleColor};">
                                    ${toggleIcon}
                                </button>
                                <button class="btn-icon" onclick="deleteItem(${row.id}, '${row.TenLoai}')" title="Xóa" style="color: #dc2626;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

            $('#loaiHopDongForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#itemId').val();
                const url = id ? `{{ route('hop-dong.loai-hop-dong.index') }}/update/${id}` :
                    '{{ route('hop-dong.loai-hop-dong.store') }}';

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
            $('#modalTitle').text('Thêm loại hợp đồng');
            $('#loaiHopDongForm')[0].reset();
            $('#itemId').val('');
            $('#MaLoai').prop('readonly', false);
            $('#CoDongBaoHiem').prop('checked', false);
            $('#TrangThai').val('mo');
            $('#loaiHopDongModal').addClass('show');
        }

        function openEditModal(item) {
            $('#modalTitle').text('Chỉnh sửa loại hợp đồng');
            $('#itemId').val(item.id);
            $('#MaLoai').val(item.MaLoai).prop('readonly', true);
            $('#TenLoai').val(item.TenLoai);
            $('#ThoiHanThang').val(item.ThoiHanThang);
            $('#ThoiHanBaoTruoc').val(item.ThoiHanBaoTruoc);
            $('#CoDongBaoHiem').prop('checked', !!item.CoDongBaoHiem);
            $('#TrangThai').val(item.TrangThai);
            $('#loaiHopDongModal').addClass('show');
        }

        function closeModal() {
            $('#loaiHopDongModal').removeClass('show');
        }

        function deleteItem(id, name) {
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa loại hợp đồng "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xác nhận xóa',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('hop-dong.loai-hop-dong.index') }}/delete/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Thành công!', res.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Lỗi!', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });
        }
        function toggleStatus(id, currentStatus) {
            const action = currentStatus === 'mo' ? 'Khóa' : 'Mở';
            const confirmBtnColor = currentStatus === 'mo' ? '#dc2626' : '#0BAA4B';

            Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc chắn muốn ${action.toLowerCase()} loại hợp đồng này?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Đồng ý ${action.toLowerCase()}`,
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('hop-dong.loai-hop-dong.index') }}/toggle-status/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Thành công!', res.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Lỗi!', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
