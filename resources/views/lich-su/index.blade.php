@extends('layouts.app')

@section('title', 'Lịch sử hệ thống - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Lịch sử hệ thống</h1>
        <p>Thống kê các thao tác thay đổi trên hệ thống</p>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table table-bordered table-hover" id="historyTable" style="width: 100%;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>Mô tả</th>
                        <th style="width: 160px;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php
                            $cu = json_decode($log->DuLieuCu, true);
                            $moi = json_decode($log->DuLieuMoi, true);
                            
                            $displayCu = [];
                            $displayMoi = [];
                            
                            $ignoreKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'CreatedAt', 'UpdatedAt', 'remember_token', 'MatKhau', 'password'];
                            
                            if (is_array($cu) && is_array($moi)) {
                                foreach ($moi as $key => $value) {
                                    if (in_array($key, $ignoreKeys)) continue;
                                    $oldValue = $cu[$key] ?? '';
                                    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                                    if (is_array($oldValue)) $oldValue = json_encode($oldValue, JSON_UNESCAPED_UNICODE);
                                    
                                    if ((string)$oldValue !== (string)$value) {
                                        $displayCu[] = "• <b>{$key}:</b> " . ($oldValue !== '' ? $oldValue : '<i>(Trống)</i>');
                                        $displayMoi[] = "• <b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                    }
                                }
                            } elseif (is_array($moi)) {
                                foreach ($moi as $key => $value) {
                                    if (in_array($key, $ignoreKeys)) continue;
                                    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                                    $displayMoi[] = "• <b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                }
                            } elseif (is_array($cu)) {
                                foreach ($cu as $key => $value) {
                                    if (in_array($key, $ignoreKeys)) continue;
                                    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                                    $displayCu[] = "• <b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                }
                            }

                            $oldStr = !empty($displayCu) ? implode('<br>', $displayCu) : 'Không có dữ liệu cũ';
                            $newStr = !empty($displayMoi) ? implode('<br>', $displayMoi) : 'Không có dữ liệu mới';
                        @endphp
                        <tr class="log-row" style="cursor: pointer;" data-old="{!! htmlspecialchars($oldStr) !!}" data-new="{!! htmlspecialchars($newStr) !!}" data-title="{{ $log->HanhDong }} - {{ $log->DoiTuongLoai }}">
                            <td class="text-center" data-order="{{ $log->Id }}">{{ $log->Id }}</td>
                            <td>
                                <div style="font-weight: 500;">{{ $log->TenNguoiDung ?: ($log->TaiKhoan ?? 'Hệ thống') }}</div>
                                <div style="font-size: 11px; color: #6b7280;">User ID: {{ $log->NhanVienId }}</div>
                            </td>
                            <td>
                                <span class="badge {{ str_contains($log->HanhDong, 'Xóa') ? 'badge-danger' : (str_contains($log->HanhDong, 'Sửa') || str_contains($log->HanhDong, 'Cập nhật') ? 'badge-warning' : 'badge-success') }}">
                                    {{ $log->HanhDong }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $log->DoiTuongLoai }}</div>
                                <div style="font-size: 11px; color: #6b7280;">Mã: {{ $log->DoiTuongId }}</div>
                            </td>
                            <td>
                                {{ $log->MoTa }}
                            </td>
                            <td style="white-space: nowrap;">
                                {{ date('H:i:s d/m/Y', strtotime($log->CreatedAt)) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    </div>

    <style>
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        .log-row:hover {
            background-color: rgba(0,0,0,0.02) !important;
        }
        body.dark-theme .log-row:hover {
            background-color: rgba(255,255,255,0.05) !important;
        }
        
        /* Swat custom style */
        .swal-log-content {
            text-align: left;
            font-size: 14px;
            line-height: 1.6;
        }
        .swal-log-label {
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
            color: #374151;
        }
        body.dark-theme .swal-log-label {
            border-color: #2e3349;
            color: #e8eaf0;
        }
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const table = $('#historyTable').DataTable({
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
            pageLength: 25,
            order: [[0, 'desc']] // Sort by ID descending by default
        });

        // Click row to show popup
        $('#historyTable tbody').on('click', 'tr', function() {
            const row = $(this);
            const title = row.data('title');
            const oldData = row.data('old');
            const newData = row.data('new');

            if (!oldData && !newData) return;

            Swal.fire({
                title: title,
                html: `
                    <div class="swal-log-content">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <div class="swal-log-label">Dữ liệu cũ</div>
                                <div style="color: #6b7280;">${oldData}</div>
                            </div>
                            <div>
                                <div class="swal-log-label" style="color: #0BAA4B;">Dữ liệu mới</div>
                                <div style="color: #374151;">${newData}</div>
                            </div>
                        </div>
                    </div>
                `,
                width: '800px',
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#6b7280'
            });
        });
    });
</script>
@endpush
