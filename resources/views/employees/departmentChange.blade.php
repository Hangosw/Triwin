@extends('layouts.app')

@section('title', 'Điều chuyển nội bộ')

@push('styles')
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            color: #1f2937;
            padding-left: 0;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0BAA4B;
        }

        /* Custom Radio Styles */
        .radio-group {
            display: flex;
            gap: 24px;
            margin-top: 8px;
        }

        .radio-item {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s;
            user-select: none;
        }

        .radio-item:hover {
            border-color: #0BAA4B;
            background: #f0f7f4;
        }

        .radio-item input[type="radio"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            margin-right: 10px;
            position: relative;
            transition: all 0.2s;
        }

        .radio-item input[type="radio"]:checked {
            border-color: #0BAA4B;
            background-color: #0BAA4B;
        }

        .radio-item input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }

        .radio-item span {
            font-weight: 500;
            color: #4b5563;
        }

        .radio-item input[type="radio"]:checked+span {
            color: #0BAA4B;
        }
    </style>
@endpush


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h1>Điều chuyển nội bộ</h1>
            <p>Tạo phiếu điều chuyển nhân viên giữa các phòng ban hoặc chức vụ.</p>
        </div>


        @if ($errors->any())
            <div class="alert alert-danger card mb-4"
                style="background-color: #fef2f2; color: #991b1b; border: 1px solid #f87171;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form action="{{ route('dieu-chuyen.tao') }}" method="POST">
                @csrf
                <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 32px;">
                    <div class="form-section">
                        <h3 class="mb-4 text-primary">Thông tin nhân viên</h3>

                        <div class="form-group mb-4">
                            <label class="form-label">Chọn nhân viên <span class="text-danger">*</span></label>
                            <select name="NhanVienId" id="NhanVienId" class="form-control select2" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($nhanViens as $nv)
                                    <option value="{{ $nv->id }}"
                                        data-phongban="{{ $nv->ttCongViec?->phongBan?->Ten }}"
                                        data-chucvu="{{ $nv->ttCongViec?->chucVu?->Ten }}"
                                        data-phongban-id="{{ $nv->ttCongViec?->PhongBanId }}"
                                        data-chucvu-id="{{ $nv->ttCongViec?->ChucVuId }}">
                                        {{ $nv->Ma }} - {{ $nv->Ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="current-info" class="p-4 bg-gray-50 rounded-lg mb-4"
                            style="background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; display: none;">
                            <p class="mb-2"><strong>Phòng ban hiện tại:</strong> <span id="cur-phongban"></span></p>
                            <p class="mb-0"><strong>Chức vụ hiện tại:</strong> <span id="cur-chucvu"></span></p>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Ngày dự kiến điều chuyển <span class="text-danger">*</span></label>
                            <input type="text" name="NgayDuKien" class="form-control datepicker" required
                                placeholder="Chọn ngày">
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Thay đổi lương? <span class="text-danger">*</span></label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="CoThayDoiLuong" value="1">
                                    <span>Có</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="CoThayDoiLuong" value="0" checked>
                                    <span>Không</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="mb-4 text-primary">Thông tin điều chuyển mới</h3>



                        <div class="form-group mb-4">
                            <label class="form-label">Phòng ban mới</label>
                            <select name="PhongBanMoiId" class="form-control select2">
                                <option value="">-- Giữ nguyên --</option>
                                @foreach($phongBans as $pb)
                                    <option value="{{ $pb->id }}">{{ $pb->Ten }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Chức vụ mới</label>
                            <select name="ChucVuMoiId" class="form-control select2">
                                <option value="">-- Giữ nguyên --</option>
                                @foreach($chucVus as $cv)
                                    <option value="{{ $cv->id }}" data-loai="{{ $cv->Loai }}">{{ $cv->Ten }}</option>
                                @endforeach
                            </select>
                            <div id="chuc-vu-error" class="mt-2" style="display: none; font-size: 14px; color: #dc3545 !important; font-weight: 500;">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span id="chuc-vu-error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="form-group mb-4">
                    <label class="form-label">Lý do điều chuyển <span class="text-danger">*</span></label>
                    <textarea name="LyDo" class="form-control" rows="4" required
                        placeholder="Nhập lý do điều chuyển..."></textarea>
                </div>

                <div class="flex justify-end gap-2" style="justify-content: flex-end;">
                    <button type="reset" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo phiếu điều chuyển</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Khởi tạo Select2
            $('.select2').select2({
                width: '100%',
                language: {
                    noResults: function () {
                        return "Không tìm thấy kết quả";
                    }
                }
            });

            $('#NhanVienId').on('change', function () {
                const selected = $(this).find('option:selected');
                if (selected.val()) {
                    $('#cur-phongban').text(selected.data('phongban') || 'Chưa cập nhật');
                    $('#cur-chucvu').text(selected.data('chucvu') || 'Chưa cập nhật');
                    $('#current-info').fadeIn();
                } else {
                    $('#current-info').hide();
                }
                checkChucVu();
            });

            // Lắng nghe thay đổi phòng ban và chức vụ mới
            $('select[name="PhongBanMoiId"], select[name="ChucVuMoiId"]').on('change', function () {
                checkChucVu();
            });

            function checkChucVu() {
                const nhanVienSelect = $('#NhanVienId').find('option:selected');
                const nhanVienId = nhanVienSelect.val();

                if (!nhanVienId) return;

                const phongBanMoiId = $('select[name="PhongBanMoiId"]').val() || nhanVienSelect.data('phongban-id');
                const chucVuMoiId = $('select[name="ChucVuMoiId"]').val() || nhanVienSelect.data('chucvu-id');

                if (!phongBanMoiId || !chucVuMoiId) {
                    hideError();
                    return;
                }

                $.ajax({
                    url: "{{ route('api.check-chuc-vu-ton-tai') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        phong_ban_id: phongBanMoiId,
                        chuc_vu_id: chucVuMoiId,
                        nhan_vien_id: nhanVienId
                    },
                    success: function (response) {
                        if (response.exists) {
                            showError(response.message);
                        } else {
                            hideError();
                        }
                    },
                    error: function () {
                        console.error('Lỗi khi kiểm tra chức vụ');
                    }
                });
            }

            function showError(message) {
                $('#chuc-vu-error-message').text(message);
                $('#chuc-vu-error').show();
                $('button[type="submit"]').prop('disabled', true);
            }

            function hideError() {
                $('#chuc-vu-error').hide();
                $('button[type="submit"]').prop('disabled', false);
            }
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0BAA4B'
            });
        @endif
    </script>
@endpush
