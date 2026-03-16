@extends('layouts.app')

@section('title', 'Chỉnh sửa phòng ban - Triwin')

@section('content')
    <div class="page-header">
        <h1>Chỉnh sửa phòng ban</h1>
        <p>Cập nhật thông tin phòng ban: {{ $phongBan->Ten }}</p>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('phong-ban.cap-nhat', $phongBan->id) }}" method="POST">
            @csrf
            
            @if ($errors->has('error'))
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="Ma" class="form-label">Mã phòng ban</label>
                <input type="text" id="Ma" class="form-control" value="{{ $phongBan->Ma }}" disabled>
                <small style="color: #6b7280;">Mã phòng ban không thể thay đổi.</small>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="Ten" class="form-label">Tên phòng ban <span style="color: #ef4444;">*</span></label>
                <input type="text" name="Ten" id="Ten" class="form-control @error('Ten') is-invalid @enderror"
                    placeholder="Nhập tên phòng ban mới" value="{{ old('Ten', $phongBan->Ten) }}" required autofocus>
                @error('Ten')
                    <div class="invalid-feedback" style="color: #ef4444; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 16px; height: 16px; margin-right: 4px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật phòng ban
                </button>
                <a href="{{ route('phong-ban.danh-sach') }}" class="btn"
                    style="background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
@endsection
