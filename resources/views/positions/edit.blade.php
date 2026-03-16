@extends('layouts.app')

@section('title', 'Chỉnh sửa chức vụ - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Chỉnh sửa chức vụ</h1>
        <p>Cập nhật thông tin chức vụ ID: {{ $chucVu->id }}</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('chuc-vu.cap-nhat', $chucVu->id) }}" method="POST">
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
                    <label class="form-label">Mã chức vụ</label>
                    <input type="text" class="form-control" value="{{ $chucVu->Ma }}" disabled 
                        style="background-color: #f3f4f6; cursor: not-allowed; color: #6b7280;">
                    <small style="color: #6b7280; margin-top: 4px; display: block;">Mã chức vụ không thể thay đổi</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Tên chức vụ <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten', $chucVu->Ten) }}"
                        placeholder="Nhập tên chức vụ" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="form-group">
                    <label class="form-label">Loại chức vụ <span style="color: #dc2626;">*</span></label>
                    <select name="Loai" class="form-control" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="0" {{ old('Loai', $chucVu->Loai) == 0 ? 'selected' : '' }}>Nhân viên</option>
                        <option value="1" {{ old('Loai', $chucVu->Loai) == 1 ? 'selected' : '' }}>Trưởng phòng</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Phụ cấp chức vụ (VNĐ)</label>
                    <input type="text" name="PhuCapChucVu" class="form-control currency-input"
                        value="{{ number_format(old('PhuCapChucVu', $chucVu->PhuCapChucVu), 0, ',', '.') }}" 
                        placeholder="0">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật thông tin
                </button>
                <a href="{{ route('chuc-vu.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
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
