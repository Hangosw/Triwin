@extends('layouts.app')

@section('title', 'Thông tin chức vụ - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>{{ $chucVu->Ten }}</h1>
        <p>Thông tin chi tiết chức vụ</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
            <div class="form-group">
                <label class="form-label">Mã chức vụ</label>
                <div style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500;">
                    {{ $chucVu->Ma }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tên chức vụ</label>
                <div style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500;">
                    {{ $chucVu->Ten }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Loại chức vụ</label>
                <div style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500;">
                    @if($chucVu->Loai == 1)
                        <span class="badge" style="background-color: #fef3c7; color: #92400e;">Trưởng phòng</span>
                    @else
                        <span class="badge badge-info">Nhân viên</span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Phụ cấp chức vụ</label>
                <div
                    style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500; color: #0BAA4B;">
                    {{ number_format($chucVu->PhuCapChucVu ?? 0, 0, ',', '.') }} VNĐ
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Số nhân viên</label>
                <div
                    style="padding: 10px 16px; background-color: #f9fafb; border-radius: 8px; font-weight: 500; color: #0BAA4B;">
                    {{ $chucVu->nhan_viens_count ?? 0 }} người
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('chuc-vu.suaView', $chucVu->id) }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ route('chuc-vu.danh-sach') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Placeholder for future content -->
    <div class="card" style="margin-top: 24px;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Danh sách nhân viên</h3>
        <p style="color: #6b7280; text-align: center; padding: 48px;">Nội dung sẽ được bổ sung sau...</p>
    </div>
@endsection
