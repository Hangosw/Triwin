@extends('layouts.app')

@section('title', 'Thêm chức vụ mới - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header mb-4">
        <h1>Thêm chức vụ mới</h1>
        <p>Nhập thông tin chức vụ cần thêm vào hệ thống</p>
    </div>

    <div class="card p-4 mb-5">
        <form action="{{ route('chuc-vu.tao') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                {{-- Tên chức vụ --}}
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Tên chức vụ <span class="text-danger">*</span></label>
                        <input type="text" name="Ten" class="form-control" value="{{ old('Ten') }}"
                            placeholder="Nhập tên chức vụ (Ví dụ: Kế toán trưởng)" required>
                    </div>
                </div>

                {{-- Loại chức vụ --}}
                <div class="col-12 col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Loại chức vụ <span class="text-danger">*</span></label>
                        <select name="Loai" class="form-select" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="0" {{ old('Loai') == '0' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="1" {{ old('Loai') == '1' ? 'selected' : '' }}>Trưởng phòng</option>
                        </select>
                    </div>
                </div>

                {{-- Phụ cấp chức vụ --}}
                <div class="col-12 col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Phụ cấp chức vụ (VNĐ)</label>
                        <input type="text" name="PhuCapChucVu" class="form-control currency-input" 
                            value="{{ number_format(old('PhuCapChucVu', 0), 0, ',', '.') }}"
                            placeholder="0">
                    </div>
                </div>
            </div>

            <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i>
                    Thêm chức vụ
                </button>
                <a href="{{ route('chuc-vu.danh-sach') }}" class="btn btn-secondary px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function formatCurrency(value) {
        if (!value) return '';
        // Remove non-numeric characters
        value = value.toString().replace(/[^0-9]/g, '');
        // Format with thousand separator
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $('.currency-input').on('input', function() {
        let value = $(this).val();
        $(this).val(formatCurrency(value));
    });

    // Initialize formatting on load
    $('.currency-input').each(function() {
        $(this).val(formatCurrency($(this).val()));
    });
});
</script>
@endpush
