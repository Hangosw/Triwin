@extends('layouts.app')

@section('title', 'Chỉnh sửa tài sản')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <div>
            <h1>Chỉnh sửa tài sản</h1>
            <p>Cập nhật thông tin chi tiết cho tài sản</p>
        </div>
        <div>
            <a href="{{ route('tai-san.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('tai-san.update', $taiSan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label class="form-label">Tên tài sản <span class="text-danger">*</span></label>
                        <input type="text" name="ten_tai_san" class="form-control" value="{{ $taiSan->ten_tai_san }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Ngày nhập kho <span class="text-danger">*</span></label>
                                <input type="text" name="ngay_nhap_kho" class="form-control datepicker" value="{{ $taiSan->ngay_nhap_kho ? $taiSan->ngay_nhap_kho->format('d/m/Y') : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="trang_thai" id="trang_thai" class="form-select" required>
                                    <option value="SanSang" {{ $taiSan->trang_thai == 'SanSang' ? 'selected' : '' }}>Sẵn sàng</option>
                                    <option value="DangMuon" {{ $taiSan->trang_thai == 'DangMuon' ? 'selected' : '' }}>Đang cho mượn</option>
                                    <option value="BaoTri" {{ $taiSan->trang_thai == 'BaoTri' ? 'selected' : '' }}>Bảo trì</option>
                                    <option value="Hong" {{ $taiSan->trang_thai == 'Hong' ? 'selected' : '' }}>Hỏng</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="borrow-section" style="{{ $taiSan->trang_thai == 'DangMuon' ? '' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label">Người mượn</label>
                                    <select name="nhan_vien_id" class="form-select select2">
                                        <option value="">-- Chọn nhân viên --</option>
                                        @foreach($nhanViens as $nv)
                                            <option value="{{ $nv->id }}" {{ $taiSan->nhan_vien_id == $nv->id ? 'selected' : '' }}>{{ $nv->Ten }} ({{ $nv->Ma }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="form-label">Ngày mượn</label>
                                    <input type="text" name="ngay_muon" class="form-control datepicker" value="{{ $taiSan->ngay_muon ? $taiSan->ngay_muon->format('d/m/Y') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" class="form-control" rows="3">{{ $taiSan->ghi_chu }}</textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-4">
                        <label class="form-label">Hình ảnh hiện tại</label>
                        <div class="image-preview-container" style="border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; height: 200px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; background: #f9f9f9;">
                            @if($taiSan->hinh_anh)
                                <img id="preview" src="/{{ $taiSan->hinh_anh }}" alt="Preview" style="max-width: 100%; max-height: 100%;">
                                <div id="placeholder" style="display: none;">
                                    <i class="bi bi-image" style="font-size: 40px; color: #ccc;"></i>
                                    <p class="text-gray small mt-2">Chọn ảnh</p>
                                </div>
                            @else
                                <img id="preview" src="#" alt="Preview" style="max-width: 100%; max-height: 100%; display: none;">
                                <div id="placeholder">
                                    <i class="bi bi-image" style="font-size: 40px; color: #ccc;"></i>
                                    <p class="text-gray small mt-2">Chọn ảnh</p>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="hinh_anh" id="hinh_anh" class="form-control mt-2" accept="image/*">
                        <small class="text-gray mt-1 block">Để trống nếu không muốn thay đổi ảnh</small>
                    </div>
                </div>
            </div>

            <div class="mt-4 border-top pt-4" style="display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật tài sản
                </button>
                <a href="{{ route('tai-san.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
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
