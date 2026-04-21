@extends('layouts.app')

@section('title', 'Thêm tài sản mới')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <div>
            <h1>Thêm tài sản mới</h1>
            <p>Nhập thông tin chi tiết cho tài sản mới</p>
        </div>
        <div>
            <a href="{{ route('tai-san.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('tai-san.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label class="form-label">Tên tài sản <span class="text-danger">*</span></label>
                        <input type="text" name="ten_tai_san" class="form-control" placeholder="VD: Laptop Dell XPS 15" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Ngày nhập kho <span class="text-danger">*</span></label>
                                <input type="text" name="ngay_nhap_kho" class="form-control datepicker" placeholder="dd/mm/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="trang_thai" id="trang_thai" class="form-select" required>
                                    <option value="SanSang">Sẵn sàng</option>
                                    <option value="DangMuon">Đang cho mượn</option>
                                    <option value="BaoTri">Bảo trì</option>
                                    <option value="Hong">Hỏng</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="borrow-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label">Người mượn</label>
                                    <select name="nhan_vien_id" class="form-select select2">
                                        <option value="">-- Chọn nhân viên --</option>
                                        @foreach($nhanViens as $nv)
                                            <option value="{{ $nv->id }}">{{ $nv->Ten }} ({{ $nv->Ma }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label">Ngày mượn</label>
                                    <input type="text" name="ngay_muon" class="form-control datepicker" placeholder="dd/mm/yyyy">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Thông tin thêm..."></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-4">
                        <label class="form-label">Hình ảnh</label>
                        <div class="image-preview-container" style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; text-align: center; height: 200px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <img id="preview" src="#" alt="Preview" style="max-width: 100%; max-height: 100%; display: none;">
                            <div id="placeholder">
                                <i class="bi bi-image" style="font-size: 40px; color: #ccc;"></i>
                                <p class="text-gray small mt-2">Chọn ảnh</p>
                            </div>
                        </div>
                        <input type="file" name="hinh_anh" id="hinh_anh" class="form-control mt-2" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="mt-4 border-top pt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu tài sản
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        $('#trang_thai').change(function() {
            if ($(this).val() === 'DangMuon') {
                $('#borrow-section').slideDown();
            } else {
                $('#borrow-section').slideUp();
            }
        });

        $('#hinh_anh').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                    $('#placeholder').hide();
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
@endsection
