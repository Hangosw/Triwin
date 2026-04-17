@extends('layouts.app')

@section('title', 'Thêm đơn vị mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Thêm đơn vị mới</h1>
        <p>Nhập thông tin đơn vị cần thêm vào hệ thống</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('don-vi.tao') }}" method="POST">
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
                <div class="form-group">
                    <label class="form-label">Mã đơn vị <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ma" class="form-control" value="{{ old('Ma') }}" placeholder="Ví dụ: DV001"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên đơn vị <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten') }}"
                        placeholder="Nhập tên đơn vị" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ</label>
                <textarea name="DiaChi" class="form-control" rows="3"
                    placeholder="Nhập địa chỉ đơn vị">{{ old('DiaChi') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Thêm đơn vị
                </button>
                <a href="{{ route('don-vi.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
@endsection
