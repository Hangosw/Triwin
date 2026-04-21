@extends('layouts.app')

@section('title', 'Chỉnh sửa phòng ban - Triwin')

@section('content')
    <div class="page-header mb-4">
        <h1>Chỉnh sửa phòng ban</h1>
        <p>Cập nhật thông tin phòng ban: {{ $phongBan->Ten }}</p>
    </div>

    <div class="card p-4 mb-5">
        <form action="{{ route('phong-ban.cap-nhat', $phongBan->id) }}" method="POST">
            @csrf
            
            @if ($errors->has('error'))
                <div class="alert alert-danger mb-4">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="Ma" class="form-label fw-bold">Mã phòng ban</label>
                        <input type="text" id="Ma" class="form-control" value="{{ $phongBan->Ma }}" disabled style="background-color: #f3f4f6;">
                        <small class="text-muted d-block mt-1">Mã phòng ban không thể thay đổi.</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="Ten" class="form-label fw-bold">Tên phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="Ten" id="Ten" class="form-control @error('Ten') is-invalid @enderror"
                            placeholder="Nhập tên phòng ban mới" value="{{ old('Ten', $phongBan->Ten) }}" required autofocus>
                        @error('Ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    Cập nhật phòng ban
                </button>
                <a href="{{ route('phong-ban.danh-sach') }}" class="btn btn-secondary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
@endsection
