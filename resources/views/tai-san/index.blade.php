@extends('layouts.app')

@section('title', 'Quản lý tài sản')

@push('styles')
<style>
    .table th, .table td {
        padding: 12px 16px !important;
    }
    
    @media (min-width: 769px) {
        .table th, .table td {
            white-space: nowrap;
        }
    }
    
    .table-container {
        width: 100%;
        overflow-x: hidden !important;
    }

    #tai-san-table {
        width: 100% !important;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 16px;
        }
        .action-buttons {
            width: 100%;
        }
        .action-buttons > .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Quản lý tài sản</h1>
            <p style="margin-bottom: 0;">Quản lý và theo dõi tài sản của công ty</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('tai-san.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Thêm tài sản mới
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table" id="tai-san-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Hình ảnh</th>
                        <th>Tên tài sản</th>
                        <th>Ngày nhập kho</th>
                        <th>Trạng thái</th>
                        <th>Người đang mượn</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Cấp phát tài sản -->
<div class="modal fade" id="modalCapPhat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cấp phát tài sản: <span id="assetNameLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCapPhat">
                @csrf
                <input type="hidden" name="id" id="assetId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn nhân viên <span class="text-danger">*</span></label>
                        <select name="nhan_vien_id" class="form-control" id="selectNhanVien" required style="width: 100%;">
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($nhanViens as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->Ma }} - {{ $nv->Ten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày mượn <span class="text-danger">*</span></label>
                        <input type="text" name="ngay_muon" class="form-control datepicker" value="{{ date('d/m/Y') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Cấp phát</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tai-san-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: "{{ route('tai-san.data') }}",
            order: [[2, 'asc']],
            columnDefs: [
                { targets: '_all', className: 'dt-nowrap' }
            ],
            columns: [
                { 
                    data: null, 
                    render: (data, type, row, meta) => meta.row + 1 + meta.settings._iDisplayStart, 
                    className: 'stt-checkbox-col text-center all dtr-control',
                    orderable: false
                },
                { 
                    data: 'hinh_anh', 
                    className: 'min-tablet',
                    render: function(data) {
                        if (data) {
                            return `<img src="/${data}" class="avatar" style="border-radius: 4px; width: 50px; height: 50px; object-fit: cover;">`;
                        }
                        return '<div class="avatar" style="border-radius: 4px; width: 50px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center;"><i class="bi bi-box"></i></div>';
                    }
                },
                { data: 'ten_tai_san', className: 'all' },
                { 
                    data: 'ngay_nhap_kho',
                    className: 'min-desktop',
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                { 
                    data: 'trang_thai',
                    className: 'min-tablet',
                    render: function(data) {
                        const statusMap = {
                            'SanSang': { label: 'Sẵn sàng', class: 'badge-success' },
                            'DangMuon': { label: 'Đang mượn', class: 'badge-warning' },
                            'BaoTri': { label: 'Bảo trì', class: 'badge-info' },
                            'Hong': { label: 'Hỏng', class: 'badge-danger' }
                        };
                        const status = statusMap[data] || { label: data, class: 'badge-gray' };
                        return `<span class="badge ${status.class}">${status.label}</span>`;
                    }
                },
                { 
                    data: 'nhan_vien',
                    className: 'min-desktop',
                    render: function(data, type, row) {
                        if (data) {
                            return `<div>
                                        <strong>${data.Ten}</strong><br>
                                        <small class="text-gray">${row.ngay_muon ? 'Từ: ' + moment(row.ngay_muon).format('DD/MM/YYYY') : ''}</small>
                                    </div>`;
                        }
                        return '<span class="text-gray">--</span>';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    className: 'min-tablet',
                    render: function(data, type, row) {
                        let buttons = `
                            <div class="flex gap-2">
                                <a href="/tai-san/sua/${data}" class="btn-icon text-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                        `;
                        
                        if (row.trang_thai === 'SanSang' || row.trang_thai === 'BaoTri') {
                            buttons += `
                                <button onclick="openModalCapPhat(${data}, '${row.ten_tai_san}')" class="btn-icon text-success" title="Cấp phát">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            `;
                        } else if (row.trang_thai === 'DangMuon') {
                            buttons += `
                                <button onclick="thuHoiTaiSan(${data})" class="btn-icon text-warning" title="Thu hồi">
                                    <i class="bi bi-arrow-down-left-square"></i>
                                </button>
                            `;
                        }

                        buttons += `
                                <button onclick="deleteTaiSan(${data})" class="btn-icon text-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                        return buttons;
                    }
                }
            ],
            });

        // Xử lý submit form cấp phát
        $('#formCapPhat').submit(function(e) {
            e.preventDefault();
            const id = $('#assetId').val();
            const formData = $(this).serialize();

            $.ajax({
                url: `/tai-san/cap-phat/${id}`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#modalCapPhat').modal('hide');
                        Swal.fire('Thành công!', response.message, 'success');
                        $('#tai-san-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Lỗi!', xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                }
            });
        });
    });

    function openModalCapPhat(id, name) {
        $('#assetId').val(id);
        $('#assetNameLabel').text(name);
        $('#formCapPhat')[0].reset();
        $('#selectNhanVien').val('').trigger('change');
        $('#modalCapPhat').modal('show');
    }

    function thuHoiTaiSan(id) {
        Swal.fire({
            title: 'Thu hồi tài sản?',
            text: "Xác nhận nhân viên đã trả tài sản này và đưa về trạng thái Sẵn sàng.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#fb923c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Thu hồi ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tai-san/thu-hoi/${id}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Thành công!', response.message, 'success');
                            $('#tai-san-table').DataTable().ajax.reload();
                        }
                    }
                });
            }
        });
    }

    function deleteTaiSan(id) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn không thể hoàn tác hành động này!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xóa ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tai-san/xoa/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Đã xóa!', response.message, 'success');
                            $('#tai-san-table').DataTable().ajax.reload();
                        }
                    },
                    error: function() {
                        Swal.fire('Lỗi!', 'Không thể xóa tài sản này.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
