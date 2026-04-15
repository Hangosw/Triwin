@extends('layouts.app')

@section('title', 'Thêm mới Công tác - HRM')

@push('styles')
    <style>
        .auth-user-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .auth-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Thêm mới Công tác</h1>
        <p>Ghi nhận hoặc phân công quá trình công tác mới của nhân sự</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger"
            style="color: #991b1b; background-color: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"
            style="color: #991b1b; background-color: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin:0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 900px;">
        <div id="tab-top-down" class="tab-pane active">
            <form action="{{ route('cong-tac.store') }}" method="POST">
                @csrf

                <div class="form-group mb-4">
                    <label class="form-label">Chọn Nhân Viên <span style="color:red">*</span></label>
                    <select name="NhanVienId" class="form-control select2" style="width: 100%;" required>
                        <option value="">-- Tìm kiếm & Chọn nhân viên --</option>
                        @foreach($nhanViens as $nv)
                            <option value="{{ $nv->id }}" {{ old('NhanVienId') == $nv->id ? 'selected' : '' }}>
                                {{ $nv->Ma }} - {{ $nv->Ten }} ({{ $nv->SoCCCD }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group mb-4">
                        <label class="form-label">Phòng ban công tác <span style="color:red">*</span></label>
                        <select name="PhongBanId" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Chọn Phòng ban --</option>
                            @foreach($phongBans as $pb)
                                <option value="{{ $pb->id }}" {{ old('PhongBanId') == $pb->id ? 'selected' : '' }}>
                                    {{ $pb->Ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Chức vụ phụ trách <span style="color:red">*</span></label>
                        <select name="ChucVuId" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Chọn Chức vụ --</option>
                            @foreach($chucVus as $cv)
                                <option value="{{ $cv->id }}" {{ old('ChucVuId') == $cv->id ? 'selected' : '' }}>
                                    {{ $cv->Ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Từ ngày <span style="color:red">*</span></label>
                        <input type="text" name="TuNgay" class="form-control datepicker"
                            value="{{ old('TuNgay', date('d/m/Y')) }}" placeholder="DD/MM/YYYY" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Đến ngày</label>
                        <input type="text" name="DenNgay" class="form-control datepicker" value="{{ old('DenNgay') }}"
                            placeholder="DD/MM/YYYY (Để trống nếu chưa kết thúc)">
                    </div>

                    <div class="form-group mb-4" style="grid-column: span 2;">
                        <label class="form-label">Địa điểm công tác</label>
                        <input type="text" name="DiaDiem" class="form-control" value="{{ old('DiaDiem') }}"
                            placeholder="Ví dụ: TP. Hồ Chí Minh, Hà Nội, Nhà máy X...">
                    </div>

                    <div class="form-group mb-4" style="grid-column: span 2;">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="GhiChu" class="form-control" rows="3"
                            placeholder="Mô tả chi tiết nội dung công tác hoặc các lưu ý khác...">{{ old('GhiChu') }}</textarea>
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="background:#0BAA4B; border-color:#0BAA4B;">
                        <i class="bi bi-save" style="margin-right:8px;"></i> Lưu Nhiệm vụ Công tác
                    </button>
                    <a href="{{ route('cong-tac.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    </div>

@endsection

@push('scripts')
    @if (session('success'))
        <script>
            $(document).ready(function() {
                Swal.fire({
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#0BAA4B',
                    confirmButtonText: 'Đóng'
                }).then((result) => {
                    window.location.href = "{{ route('cong-tac.danh-sach') }}";
                });
            });
        </script>
    @endif
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endpush
