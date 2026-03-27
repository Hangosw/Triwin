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
                        <th style="width: 50px;">ID</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>Mô tả</th>
                        <th>Nội dung cũ</th>
                        <th>Nội dung mới</th>
                        <th>Thời gian</th>
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
                                        $displayCu[] = "<b>{$key}:</b> " . ($oldValue !== '' ? $oldValue : '<i>(Trống)</i>');
                                        $displayMoi[] = "<b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                    }
                                }
                            } elseif (is_array($moi)) {
                                foreach ($moi as $key => $value) {
                                    if (in_array($key, $ignoreKeys)) continue;
                                    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                                    $displayMoi[] = "<b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                }
                            } elseif (is_array($cu)) {
                                foreach ($cu as $key => $value) {
                                    if (in_array($key, $ignoreKeys)) continue;
                                    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                                    $displayCu[] = "<b>{$key}:</b> " . ($value !== '' ? $value : '<i>(Trống)</i>');
                                }
                            } else {
                                $displayCu[] = $log->DuLieuCu;
                                $displayMoi[] = $log->DuLieuMoi;
                            }
                        @endphp
                        <tr>
                            <td>{{ $log->Id }}</td>
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
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->MoTa }}">
                                    {{ $log->MoTa }}
                                </div>
                            </td>
                            <td>
                                <div style="max-height: 100px; max-width: 250px; overflow-y: auto; overflow-x: hidden; font-size: 13px; line-height: 1.5;">
                                    {!! implode('<br>', array_filter($displayCu)) !!}
                                </div>
                            </td>
                            <td>
                                <div style="max-height: 100px; max-width: 250px; overflow-y: auto; overflow-x: hidden; font-size: 13px; line-height: 1.5;">
                                    {!! implode('<br>', array_filter($displayMoi)) !!}
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                {{ date('d/m/Y H:i:s', strtotime($log->CreatedAt)) }}
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
    </style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable({
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
            order: [[7, 'desc']]
        });
    });
</script>
@endpush
