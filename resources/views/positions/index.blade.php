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
                    <th style="width: 60px;">
                        <div style="text-align: center;">
                            <div><strong>STT</strong></div>
                            <div style="margin-top: 4px;">
                                <input type="checkbox" id="selectAll" style="cursor: pointer;">
                            </div>
                        </div>
                    </th>
                    <th>Mã chức vụ</th>
                    <th>Tên chức vụ</th>
                    <th>Loại</th>
                    <th>Phụ cấp (VNĐ)</th>
                    <th>Số nhân viên</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chucVus ?? [] as $chucVu)
                    <tr>
                        <td class="stt-checkbox-col">
                            <div style="text-align: center;">
                                <div><strong class="stt-value"></strong></div>
                                <div style="margin-top: 4px;">
                                    <input type="checkbox" class="pos-checkbox" value="{{ $chucVu->id }}" style="cursor: pointer;">
                                </div>
                            </div>
                        </td>
                        <td><span class="font-medium" style="color: #0BAA4B;">{{ $chucVu->Ma }}</span></td>
                        <td>
                            <a href="{{ url('/chuc-vu/info/' . $chucVu->id) }}" class="pos-name-link" style="display: block; width: 100%;">
                                {{ $chucVu->Ten }}
                            </a>
                        </td>
                        <td>
                            @if($chucVu->Loai == 1)
                                <span class="badge" style="background-color: #fef3c7; color: #92400e; font-size: 11px;">Trưởng phòng</span>
                            @else
                                <span class="badge" style="background-color: #f3f4f6; color: #4b5563; font-size: 11px;">Nhân viên</span>
                            @endif
                        </td>
                        <td>{{ number_format($chucVu->PhuCapChucVu ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $chucVu->nhan_viens_count ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('styles')
<style>
    .pos-name-link {
        font-weight: 600;
        color: #0BAA4B;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        z-index: 20;
        display: inline-block;
        padding: 5px 0;
        cursor: pointer !important;
        pointer-events: auto !important;
    }
    .pos-name-link:hover {
        text-decoration: underline;
        color: #09933f;
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
        let selectedIds = [];

        @if(session('success'))
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                    confirmButtonColor: '#0BAA4B',
                    timer: 3000,
                    timerProgressBar: true
                });
            }, 50);
        @endif

        const table = $('#positionsTable').DataTable({
            language: {
                "sProcessing": "Đang xử lý...",
                "sLengthMenu": "Hiển thị <span class='no-select2-parent'>_MENU_</span> dòng",
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
                { orderable: false, targets: [0] }
            ],
            order: [[1, 'asc']] // Default sort by Ma Chuc Vu
        });

        // STT Logic - Sequential numbers
        table.on('order.dt search.dt', function () {
            let i = 1;
            table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, index) {
                $(cell).find('.stt-value').html(index + 1);
            });
        }).draw();

        // Select All checkboxes (Cross-page)
        $('#selectAll').on('change', function() {
            const isChecked = this.checked;
            if (isChecked) {
                // Get all visible checkbox values in the filtered set
                table.rows({ search: 'applied' }).nodes().to$().find('.pos-checkbox').each(function() {
                    const id = parseInt($(this).val());
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                    $(this).prop('checked', true);
                });
            } else {
                // Remove all visible checkbox values in the filtered set from selection
                table.rows({ search: 'applied' }).nodes().to$().find('.pos-checkbox').each(function() {
                    const id = parseInt($(this).val());
                    selectedIds = selectedIds.filter(itemId => itemId !== id);
                    $(this).prop('checked', false);
                });
            }
            updateBulkDeleteButton();
        });

        // Toggle individual checkbox
        $(document).on('change', '.pos-checkbox', function() {
            const id = parseInt($(this).val());
            if (this.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(itemId => itemId !== id);
            }
            
            // Update Select All state based on current page
            const currentPageCheckboxes = $('.pos-checkbox').length;
            const currentPageChecked = $('.pos-checkbox:checked').length;
            $('#selectAll').prop('checked', currentPageCheckboxes > 0 && currentPageCheckboxes === currentPageChecked);
            
            updateBulkDeleteButton();
        });

        // Restore checkbox state on draw (pagination)
        table.on('draw', function() {
            $('.pos-checkbox').each(function() {
                const id = parseInt($(this).val());
                if (selectedIds.includes(id)) {
                    $(this).prop('checked', true);
                }
            });
            
            // Update Select All state for the new page
            const currentPageCheckboxes = $('.pos-checkbox').length;
            const currentPageChecked = $('.pos-checkbox:checked').length;
            $('#selectAll').prop('checked', currentPageCheckboxes > 0 && currentPageCheckboxes === currentPageChecked);
        });

        function updateBulkDeleteButton() {
            const selectedCount = selectedIds.length;
            if (selectedCount > 0) {
                $('#btnDeleteSelected').show();
                $('#selectedCount').text(selectedCount);
            } else {
                $('#btnDeleteSelected').hide();
            }
        }

        // Bulk delete
        $('#btnDeleteSelected').on('click', function() {
            if (selectedIds.length === 0) return;

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
                            selectedIds = [];
                            $('#selectAll').prop('checked', false);
                            updateBulkDeleteButton();
                            
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
        // Force redirect when clicking the name link
        $(document).on('click', '.pos-name-link', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url && url !== '#') {
                window.location.assign(url);
            }
        });
    });
});
</script>
@endpush
