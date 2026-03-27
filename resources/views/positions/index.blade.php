@extends('layouts.app')

@section('title', 'Quản lý chức vụ - Triwin')

@section('content')
    <div class="page-header">
        <h1>Quản lý chức vụ</h1>
        <p>Danh sách các chức vụ trong công ty</p>
    </div>

    <div class="card">
        <div class="action-bar" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <div class="bulk-actions">
                <button id="btnDeleteSelected" class="btn btn-danger" style="display: none; background-color: #ef4444; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
            </div>
            <a href="{{ route('chuc-vu.taoView') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm chức vụ
            </a>
        </div>

        <table id="positionsTable" class="table table-hover" style="width: 100%;">
            <thead>
                <tr>
                    <th width="50">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>#</th>
                    <th>Mã chức vụ</th>
                    <th>Tên chức vụ</th>
                    <th>Loại</th>
                    <th>Phụ cấp (VNĐ)</th>
                    <th>Số nhân viên</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chucVus ?? [] as $index => $chucVu)
                    <tr>
                        <td>
                            <input type="checkbox" class="pos-checkbox" value="{{ $chucVu->id }}">
                        </td>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="font-medium" style="color: #0BAA4B;">{{ $chucVu->Ma }}</span></td>
                        <td>{{ $chucVu->Ten }}</td>
                        <td>
                            @if($chucVu->Loai == 1)
                                <span class="badge" style="background-color: #fef3c7; color: #92400e; font-size: 11px;">Trưởng phòng</span>
                            @else
                                <span class="badge" style="background-color: #f3f4f6; color: #4b5563; font-size: 11px;">Nhân viên</span>
                            @endif
                        </td>
                        <td>{{ number_format($chucVu->PhuCapChucVu ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $chucVu->nhan_viens_count ?? 0 }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('chuc-vu.info', $chucVu->id) }}" class="btn-icon" title="Chi tiết">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('chuc-vu.suaView', $chucVu->id) }}" class="btn-icon" title="Chỉnh sửa" style="color: #0BAA4B;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" class="btn-icon btn-delete-pos" data-id="{{ $chucVu->id }}" data-name="{{ $chucVu->Ten }}" title="Xóa" style="color: #ef4444; background: none; border: 1px solid #e2e8f0; cursor: pointer;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('styles')
<style>
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: #64748b;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
    }
    .btn-icon:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
    }
    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_length select {
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-left: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const table = $('#positionsTable').DataTable({
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
            columnDefs: [
                { orderable: false, targets: [0, 7] }
            ]
        });

        // Select All checkboxes
        $('#selectAll').on('change', function() {
            $('.pos-checkbox').prop('checked', this.checked);
            updateBulkDeleteButton();
        });

        $(document).on('change', '.pos-checkbox', function() {
            const allChecked = $('.pos-checkbox:checked').length === $('.pos-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
            updateBulkDeleteButton();
        });

        function updateBulkDeleteButton() {
            const selectedCount = $('.pos-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#btnDeleteSelected').show();
                $('#selectedCount').text(selectedCount);
            } else {
                $('#btnDeleteSelected').hide();
            }
        }

        // Single delete
        $(document).on('click', '.btn-delete-pos', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa chức vụ \"" + name + "\"? (Chỉ xóa được khi không có nhân viên)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/chuc-vu/xoa/${id}`, {
                        _token: '{{ csrf_token() }}'
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                confirmButtonColor: '#0BAA4B'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Không thể xóa!',
                                text: response.message,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    }).fail(function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra trong quá trình xử lý.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        });

        // Bulk delete
        $('#btnDeleteSelected').on('click', function() {
            const selectedIds = [];
            $('.pos-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            Swal.fire({
                title: 'Xóa các mục đã chọn?',
                text: `Bạn đang chọn xóa ${selectedIds.length} chức vụ.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('chuc-vu.xoa-nhieu') }}", {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                confirmButtonColor: '#0BAA4B'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Không thể xóa!',
                                text: response.message,
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    }).fail(function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra trong quá trình xử lý.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        });
    });
</script>
@endpush
