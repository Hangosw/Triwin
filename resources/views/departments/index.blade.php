@extends('layouts.app')

@section('title', 'Quản lý phòng ban - Triwin')

@section('content')
    <div class="page-header">
        <h1>Quản lý phòng ban</h1>
        <p>Danh sách các phòng ban trong công ty</p>
    </div>

    <!-- Actions Bar Card -->
    <div class="card">
        <div class="action-bar">
            <div style="display: flex; gap: 16px; align-items: center; flex: 1;">
                <div class="search-bar">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, mã phòng ban..." id="customSearch">
                </div>
                <div id="lengthMenuContainer" class="no-select2-parent">
                    <!-- DataTables length menu will be moved here -->
                </div>
            </div>
            <div class="action-buttons">
                <button id="btnDeleteSelected" class="btn btn-danger"
                    style="display: none; background-color: #dc2626; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('phong-ban.taoView') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm phòng ban
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="table-container">
            <table id="departmentsTable" class="table table-hover" style="width: 100%; table-layout: fixed;">
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
                    <th style="width: 20%;">Mã phòng ban</th>
                    <th style="width: 55%;">Tên phòng ban</th>
                    <th style="width: 20%;">Số nhân viên</th>
                </tr>
            </thead>
            <tbody>
                @foreach($phongBans ?? [] as $phongBan)
                    <tr>
                        <td>
                            <div style="text-align: center;">
                                <div><strong class="stt-value"></strong></div>
                                <div style="margin-top: 4px;">
                                    <input type="checkbox" class="dept-checkbox" value="{{ $phongBan->id }}" style="cursor: pointer;">
                                </div>
                            </div>
                        </td>
                        <td><span class="font-medium" style="color: #0BAA4B;">{{ $phongBan->Ma }}</span></td>
                        <td>
                            <a href="{{ route('phong-ban.info', $phongBan->id) }}" class="dept-name-link">
                                {{ $phongBan->Ten }}
                            </a>
                        </td>
                        <td>{{ $phongBan->nhanViens->count() ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div> <!-- End table-container -->
    </div> <!-- End card -->
@endsection

@push('styles')
<style>
    .dept-name-link {
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
    .dept-name-link:hover {
        text-decoration: underline;
        color: #09933f;
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

        const table = $('#departmentsTable').DataTable({
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
                { orderable: false, targets: [0] }
            ],
            order: [[1, 'asc']], // Default sort by Ma Phong Ban
            dom: '<"top"l>rtip', // Enable length menu
        });

        // Move length menu to custom container
        $('.dataTables_length').detach().appendTo('#lengthMenuContainer');

        // STT Logic - Sequential numbers
        table.on('order.dt search.dt', function () {
            table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, index) {
                $(cell).find('.stt-value').html(index + 1);
            });
        }).draw();

        // Custom Search
        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Select All checkboxes (Cross-page)
        $('#selectAll').on('change', function() {
            const isChecked = this.checked;
            if (isChecked) {
                table.rows({ search: 'applied' }).nodes().to$().find('.dept-checkbox').each(function() {
                    const id = parseInt($(this).val());
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                    $(this).prop('checked', true);
                });
            } else {
                table.rows({ search: 'applied' }).nodes().to$().find('.dept-checkbox').each(function() {
                    const id = parseInt($(this).val());
                    selectedIds = selectedIds.filter(itemId => itemId !== id);
                    $(this).prop('checked', false);
                });
            }
            updateBulkDeleteButton();
        });

        // Toggle individual checkbox
        $(document).on('change', '.dept-checkbox', function() {
            const id = parseInt($(this).val());
            if (this.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(itemId => itemId !== id);
            }
            
            const currentPageCheckboxes = $('.dept-checkbox').length;
            const currentPageChecked = $('.dept-checkbox:checked').length;
            $('#selectAll').prop('checked', currentPageCheckboxes > 0 && currentPageCheckboxes === currentPageChecked);
            
            updateBulkDeleteButton();
        });

        // Restore checkbox state on draw (pagination)
        table.on('draw', function() {
            $('.dept-checkbox').each(function() {
                const id = parseInt($(this).val());
                if (selectedIds.includes(id)) {
                    $(this).prop('checked', true);
                }
            });
            
            const currentPageCheckboxes = $('.dept-checkbox').length;
            const currentPageChecked = $('.dept-checkbox:checked').length;
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

        // Force redirect when clicking the name link
        $(document).on('click', '.dept-name-link', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url && url !== '#') {
                window.location.assign(url);
            }
        });

        // Bulk Delete
        $('#btnDeleteSelected').on('click', function() {
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Xóa các mục đã chọn?',
                text: `Bạn đang chọn xóa ${selectedIds.length} phòng ban.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('phong-ban.xoa-nhieu') }}", {
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
                    });
                }
            });
        });
    });
</script>
@endpush
