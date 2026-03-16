@extends('layouts.app')

@section('title', 'Lịch sử hệ thống - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Lịch sử hệ thống</h1>
        <p>Thống kê các thao tác thay đổi trên hệ thống</p>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="historyTable">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Nhân viên</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>Nội dung cũ</th>
                        <th>Nội dung mới</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->Id }}</td>
                            <td>
                                <div style="font-weight: 500;">{{ $log->TenNhanVien ?? 'N/A' }}</div>
                                <div style="font-size: 11px; color: #6b7280;">ID: {{ $log->NhanVienId }}</div>
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
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->NoiDungCu }}">
                                    {{ $log->NoiDungCu }}
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->NoiDungMoi }}">
                                    {{ $log->NoiDungMoi }}
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                {{ date('d/m/Y H:i:s', strtotime($log->Created_At)) }}
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
