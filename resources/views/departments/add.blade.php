@extends('layouts.app')

@section('title', 'Thêm phòng ban mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Thêm phòng ban mới</h1>
        <p>Nhập thông tin phòng ban cần thêm vào hệ thống</p>
    </div>

    <div class="card p-4 mb-5">
        <form action="{{ route('phong-ban.tao') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 24px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Tên phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="Ten" class="form-control" value="{{ old('Ten') }}"
                            placeholder="Nhập tên phòng ban (Ví dụ: Phòng Nhân sự)" required>
                    </div>
                </div>
            </div>

            <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    Thêm phòng ban
                </button>
                <a href="{{ route('phong-ban.danh-sach') }}" class="btn btn-secondary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
@endsection
