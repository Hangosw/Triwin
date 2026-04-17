@extends('layouts.app')

@section('title', 'Chỉnh sửa đơn vị - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Chỉnh sửa đơn vị</h1>
        <p>Cập nhật thông tin đơn vị ID: {{ $donVi->id }}</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('don-vi.cap-nhat', $donVi->id) }}" method="POST">
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

            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: 24px;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="form-group">
                    <label class="form-label">Mã đơn vị <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ma" class="form-control" value="{{ old('Ma', $donVi->Ma) }}"
                        placeholder="Ví dụ: DV001" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên đơn vị <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten', $donVi->Ten) }}"
                        placeholder="Nhập tên đơn vị" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ</label>
                <textarea name="DiaChi" class="form-control" rows="3"
                    placeholder="Nhập địa chỉ đơn vị">{{ old('DiaChi', $donVi->DiaChi) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật thông tin
                </button>
                <a href="{{ route('don-vi.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
@endsection
