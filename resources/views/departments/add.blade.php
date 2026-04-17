@extends('layouts.app')

@section('title', 'Thêm phòng ban mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Thêm phòng ban mới</h1>
        <p>Nhập thông tin phòng ban cần thêm vào hệ thống</p>
    </div>

    <div class="card" style="max-width: 800px;">
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

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Tên phòng ban <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten') }}"
                        placeholder="Nhập tên phòng ban" required>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Thêm phòng ban
                </button>
                <a href="{{ route('phong-ban.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
@endsection
