@extends('layouts.app')

@section('title', 'Thêm Mới Tổ Đội - Vietnam Rubber Group')

@push('styles')
    <!-- Select2 for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 4px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
            right: 12px;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #0BAA4B;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: #0BAA4B;
        }
    </style>
@endpush

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800" style="font-weight: 700; color: #1f2937;">Thêm Mới Tổ Đội</h1>
            <p class="text-muted" style="color: #6b7280; font-size: 14px;">Tạo phòng ban hoặc tổ làm việc mới và gán tổ
                trưởng</p>
        </div>
        <div>
            <a href="{{ route('to-doi.danh-sach') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi xảy ra!',
                    text: @json(session('error')),
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#dc2626'
                });
            });
        </script>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-body" style="padding: 32px;">
                    <form action="{{ route('to-doi.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <!-- Mã tổ đội -->
                            <div class="col-md-6 form-group">
                                <label class="form-label font-weight-bold">Mã Tổ Đội <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Ma" class="form-control @error('Ma') is-invalid @enderror"
                                    placeholder="VD: TD-SX01" value="{{ old('Ma') }}" required>
                                @error('Ma')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tên tổ đội -->
                            <div class="col-md-6 form-group">
                                <label class="form-label font-weight-bold">Tên Tổ Đội <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Ten" class="form-control @error('Ten') is-invalid @enderror"
                                    placeholder="VD: Tổ Đóng Gói" value="{{ old('Ten') }}" required>
                                @error('Ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Phòng Ban -->
                            <div class="col-md-6 form-group">
                                <label class="form-label font-weight-bold">Thuộc Phòng Ban <span
                                        class="text-danger">*</span></label>
                                <select id="PhongBanId" name="PhongBanId"
                                    class="form-select select2-init @error('PhongBanId') is-invalid @enderror" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    @foreach ($phongBans as $phongBan)
                                        <option value="{{ $phongBan->id }}">{{ $phongBan->Ten }}</option>
                                    @endforeach
                                </select>
                                @error('PhongBanId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tổ trưởng -->
                            <div class="col-md-6 form-group">
                                <label class="form-label font-weight-bold">Tổ Trưởng (Công nhân)</label>
                                <select id="TruongToId" name="TruongToId"
                                    class="form-select select2-init @error('TruongToId') is-invalid @enderror" disabled>
                                    <option value="">-- Vui lòng chọn phòng ban trước --</option>
                                </select>
                                <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i> Có thể chọn
                                    sau</small>
                                @error('TruongToId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <!-- Thành viên Tổ Đội -->
                            <div class="col-md-12 form-group">
                                <label class="form-label font-weight-bold">Thành viên (Công nhân) <span
                                        class="text-danger">*</span></label>
                                <select id="ThanhVienIds" name="ThanhVienIds[]" multiple="multiple"
                                    class="form-select select2-multiple @error('ThanhVienIds') is-invalid @enderror"
                                    disabled>
                                </select>
                                <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i> Chọn một hoặc
                                    nhiều nhân viên để thêm vào tổ đội này</small>
                                @error('ThanhVienIds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-5">
                            <label class="form-label font-weight-bold">Ghi chú</label>
                            <textarea name="GhiChu" class="form-control" rows="3"
                                placeholder="Thông tin bổ sung về tổ đội này...">{{ old('GhiChu') }}</textarea>
                        </div>

                        <hr class="mb-4" style="background-color: #e5e7eb; height: 1px; border: none;">

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                <i class="bi bi-check2-circle me-2"></i> Lưu Tổ Đội
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                width: '100%',
                language: {
                    noResults: function () {
                        return "Không tìm thấy kết quả";
                    }
                }
            });

            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Vui lòng chọn phòng ban trước --',
                language: {
                    noResults: function () {
                        return "Không tìm thấy kết quả";
                    }
                }
            });



            // Handle cascading dropdown: Phong Ban -> Nhan Vien
            $('#PhongBanId').on('change', function () {
                var phongBanId = $(this).val();
                var nhanVienSelect = $('#TruongToId');
                var thanhVienSelect = $('#ThanhVienIds');

                // Clear Nhan Vien and Thanh Vien
                nhanVienSelect.empty().append('<option value="">-- Có thể chọn sau --</option>').prop('disabled', true);
                thanhVienSelect.empty().prop('disabled', true);

                if (phongBanId) {
                    // Fetch Nhan Viens
                    $.get('/to-doi/ajax/nhan-vien/' + phongBanId, function (data) {
                        nhanVienSelect.prop('disabled', false);
                        thanhVienSelect.prop('disabled', false);
                        // Update placeholder for multi-select once enabled
                        thanhVienSelect.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: '-- Chọn một hoặc nhiều thành viên --'
                        });

                        $.each(data, function (index, nv) {
                            var optionText = nv.Ma + ' - ' + nv.Ten + ' (' + nv.ChucVu + ')';
                            nhanVienSelect.append('<option value="' + nv.id + '">' + optionText + '</option>');
                            thanhVienSelect.append('<option value="' + nv.id + '">' + optionText + '</option>');
                        });
                    });
                }
            });
        });
    </script>
@endpush
