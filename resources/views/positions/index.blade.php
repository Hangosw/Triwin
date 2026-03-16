@extends('layouts.app')

@section('title', 'Quản lý chức vụ - Triwin')

@section('content')
    <div class="page-header">
        <h1>Quản lý chức vụ</h1>
        <p>Danh sách các chức vụ trong công ty</p>
    </div>

    <div class="card">
        <div class="action-bar" style="margin-bottom: 24px; display: flex; justify-content: flex-end;">
            <a href="{{ route('chuc-vu.taoView') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm chức vụ
            </a>
        </div>

        <div class="table-responsive">
            <table id="positionsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Mã chức vụ</th>
                        <th>Tên chức vụ</th>
                        <th>Loại</th>
                        <th>Phụ cấp (VNĐ)</th>
                        <th>Số nhân viên</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chucVus ?? [] as $index => $chucVu)
                        <tr>
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
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
        $('#positionsTable').DataTable({
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
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>
@endpush
