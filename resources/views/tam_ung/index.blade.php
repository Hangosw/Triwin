@extends('layouts.app')

@section('title', 'Quản lý tạm ứng lương - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
<style>
    .table th {
        background-color: #f8fafc;
        font-weight: 600;
        font-size: 13px;
    }
    body.dark-theme .table th {
        background-color: #1e293b;
        color: #e2e8f0;
    }
    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Quản lý tạm ứng lương</h1>
        <p>Danh sách các yêu cầu tạm ứng lương của nhân viên</p>
    </div>
    <div>
        <a href="{{ route('tam-ung.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Tạo yêu cầu
        </a>
    </div>
</div>

<div class="card mb-4" style="padding: 16px;">
    <form method="GET" action="{{ route('tam-ung.index') }}" class="d-flex gap-3 align-items-end">
        <div style="min-width: 200px;">
            <label class="form-label" style="font-size: 13px; font-weight: 600;">Trạng thái</label>
            <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Từ chối</option>
            </select>
        </div>
        <div style="flex: 1;">
            <label class="form-label" style="font-size: 13px; font-weight: 600;">Tìm kiếm</label>
            <input type="text" name="search" class="form-control" placeholder="Tên hoặc mã nhân viên..." value="{{ request('search') }}">
        </div>
        <div>
            <button type="submit" class="btn btn-secondary">Lọc</button>
            @if(request()->hasAny(['trang_thai', 'search']))
                <a href="{{ route('tam-ung.index') }}" class="btn btn-light ms-2">Xóa lọc</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>Nhân viên</th>
                    <th>Phòng ban</th>
                    <th class="text-end">Số tiền đề nghị</th>
                    <th>Lý do</th>
                    <th>Ngày yêu cầu</th>
                    <th>Trạng thái</th>
                    <th>Người duyệt</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tamUngs as $index => $item)
                <tr>
                    <td class="text-center">{{ $tamUngs->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->nhanVien?->Ten ?? '—' }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ $item->nhanVien?->Ma ?? '' }}</div>
                    </td>
                    <td>{{ $item->nhanVien?->ttCongViec?->phongBan?->TenPhongBan ?? '—' }}</td>
                    <td class="text-end fw-bold text-primary">
                        {{ number_format($item->SoTien, 0, ',', '.') }} đ
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 200px;" title="{{ $item->Lydo }}">
                            {{ $item->Lydo }}
                        </div>
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($item->TrangThai == 0)
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($item->TrangThai == 1)
                            <span class="badge bg-success">Đã duyệt</span>
                        @else
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>
                    <td>
                        @if($item->TrangThai != 0)
                            {{ $item->nguoiDuyet?->Ten ?? '—' }}
                        @else
                            <span class="text-muted" style="font-style: italic;">Chưa duyệt</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                            <i class="bi bi-eye"></i> Xem
                        </button>
                    </td>
                </tr>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('tam-ung.update-status', $item->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết tạm ứng - {{ $item->nhanVien?->Ten }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-6 text-muted">Số tiền xin ứng:</div>
                                        <div class="col-6 fw-bold text-primary fs-5 text-end">{{ number_format($item->SoTien, 0, ',', '.') }} đ</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6 text-muted">Hạn mức tối đa (lúc tạo):</div>
                                        <div class="col-6 text-end">{{ number_format($item->HanMuc, 0, ',', '.') }} đ</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 text-muted mb-1">Lý do từ nhân viên:</div>
                                        <div class="col-12 p-2 bg-light rounded text-dark" style="border: 1px solid #e5e7eb;">
                                            {{ $item->Lydo }}
                                        </div>
                                    </div>

                                    @if($item->TrangThai == 0)
                                        <hr>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Ghi chú (dành cho người duyệt)</label>
                                            <textarea name="GhiChu" class="form-control" rows="2" placeholder="Nhập ghi chú hoặc lý do nếu từ chối..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Quyết định</label>
                                            <select name="TrangThai" class="form-select" required>
                                                <option value="1">Duyệt yêu cầu</option>
                                                <option value="2">Từ chối</option>
                                            </select>
                                        </div>
                                    @else
                                        @if($item->GhiChu)
                                            <div class="row mb-3 mt-3">
                                                <div class="col-12 text-muted mb-1">Ghi chú duyệt:</div>
                                                <div class="col-12 text-dark">{{ $item->GhiChu }}</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    @if($item->TrangThai == 0)
                                        <button type="submit" class="btn btn-primary">Lưu quyết định</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Không có dữ liệu yêu cầu tạm ứng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-3 border-top">
        {{ $tamUngs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
