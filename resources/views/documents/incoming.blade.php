@extends('layouts.app')

@section('title', 'Văn thư đến - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Văn thư đến</h1>
        <p>Danh sách văn bản, công văn đến</p>
    </div>

    <div class="card">
        <div class="action-bar">
            <div class="action-buttons">
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Lọc
                </button>
                <a href="#" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm văn bản đến
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table" id="incomingTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">STT</th>
                        <th>Ngày</th>
                        <th>Số hiệu văn bản</th>
                        <th>Trích yếu / Tiêu đề</th>
                        <th>Loại văn bản</th>
                        <th>Cơ quan / Đơn vị</th>
                        <th>Trạng thái</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#incomingTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('documents.incoming.data') }}',
                    columns: [
                        {
                            data: null,
                            render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'ngay',
                            render: (data) => data ? new Date(data).toLocaleDateString('vi-VN') : ''
                        },
                        { data: 'so_hieu_van_ban' },
                        {
                            data: 'tieu_de',
                            render: function (data, type, row) {
                                return `<strong>${data}</strong>`;
                            }
                        },
                        { data: 'loai_van_ban.ten_loai', defaultContent: 'Chưa phân loại' },
                        { data: 'phong_ban.Ten', defaultContent: 'Nội bộ' },
                        {
                            data: 'trang_thai',
                            render: function (data) {
                                if (data === 'hoan_thanh') return '<span class="badge badge-success">Hoàn thành</span>';
                                if (data === 'dang_xu_ly') return '<span class="badge badge-warning">Đang xử lý</span>';
                                return '<span class="badge badge-gray">Mới</span>';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                return `
                                            <div class="flex gap-2">
                                                <button class="btn-icon text-primary" title="Xem chi tiết">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                ${row.file_path ? `
                                                    <a href="/${row.file_path}" class="btn-icon text-info" title="Tải file" target="_blank">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                ` : ''}
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
                    },
                    order: [[1, 'desc']]
                });
            });
        </script>
    @endpush
@endsection
