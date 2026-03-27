@extends('layouts.app')

@section('title', 'Danh sách điều chuyển nội bộ - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Danh sách điều chuyển nội bộ</h1>
        <p>Quản lý các yêu cầu điều chuyển phòng ban, chức vụ của nhân viên.</p>
    </div>

    <!-- Actions Bar -->
    <div class="card">
        <div class="action-bar">
            <div class="search-bar">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên, phòng ban..." id="customSearch">
            </div>
            <div class="action-buttons">
                <button class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Xuất Excel
                </button>
                <a href="{{ route('dieu-chuyen.taoView') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tạo phiếu điều chuyển
                </a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="table-container">
            <table class="table" id="transferTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="stt-checkbox-col" style="width: 50px;">
                            <span class="stt-text">STT</span>
                        </th>
                        <th>Nhân viên</th>
                        <th>Ngày dự kiến</th>
                        <th>Điều chuyển đến</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($phieus as $index => $phieu)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="font-medium text-gray-900">{{ $phieu->nhanVien?->Ten }}</div>
                                <div class="text-sm text-gray-500">{{ $phieu->nhanVien?->Ma }}</div>
                            </td>
                            <td>{{ $phieu->NgayDuKien?->format('d/m/Y') }}</td>
                            <td>

                                @if ($phieu->phongBanMoi)
                                    <div class="text-sm"><strong>Phòng ban:</strong> {{ $phieu->phongBanMoi->Ten }}</div>
                                @endif
                                @if ($phieu->chucVuMoi)
                                    <div class="text-sm"><strong>Chức vụ:</strong> {{ $phieu->chucVuMoi->Ten }}</div>
                                @endif

                                <div class="mt-2">
                                    @if ($phieu->CoThayDoiLuong)
                                        <span class="badge badge-info" style="font-size: 11px;">Có thay đổi lương</span>
                                    @else
                                        <span class="badge badge-gray" style="font-size: 11px;">Không thay đổi lương</span>
                                    @endif
                                </div>

                                @if (!$phieu->phongBanMoi && !$phieu->chucVuMoi)
                                    <span class="text-gray-400 italic text-sm">Không thay đổi</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm text-gray-600" title="{{ $phieu->LyDo }}">
                                    {{ Str::limit($phieu->LyDo, 50) }}
                                </div>
                                @if ($phieu->GhiChuLanhDao)
                                    <div class="text-xs text-blue-600 mt-1 italic">
                                        <strong>Ghi chú:</strong> {{ $phieu->GhiChuLanhDao }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($phieu->TrangThai == 'cho_duyet')
                                    <span class="badge badge-warning">Chờ duyệt</span>
                                @elseif($phieu->TrangThai == 'da_duyet')
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Từ chối</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $phieu->created_at?->format('H:i d/m/Y') }}
                            </td>
                            <td>
                                @if ($phieu->TrangThai == 'cho_duyet')
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-icon text-success" onclick="approveTransfer({{ $phieu->id }})"
                                            title="Duyệt">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button class="btn-icon text-danger" onclick="rejectTransfer({{ $phieu->id }})"
                                            title="Từ chối">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @elseif($phieu->TrangThai == 'da_duyet' && $phieu->CoThayDoiLuong == 1 && $phieu->DaTaoHopDong == 0)
                                    <a href="{{ route('hop-dong.taoView', ['nhan_vien_id' => $phieu->NhanVienId, 'phieu_dieu_chuyen_id' => $phieu->id]) }}"
                                        class="btn-icon text-primary" title="Tạo hợp đồng ngay">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            style="width: 20px; height: 20px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const table = $('#transferTable').DataTable({
                language: {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Hiển thị _MENU_ dòng",
                    "sZeroRecords": "Không tìm thấy dữ liệu",
                    "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                    "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                order: [
                    [6, 'desc']
                ], // Sắp xếp theo ngày tạo mới nhất
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                dom: 'rtip', // Hide default search box
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3, 4, 7]
                }]
            });

            // Custom Search
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0BAA4B'
                });
            @endif
                        });

        function approveTransfer(id) {
            Swal.fire({
                title: 'Phê duyệt điều chuyển',
                input: 'textarea',
                inputLabel: 'Ghi chú lãnh đạo (nếu có)',
                inputPlaceholder: 'Nhập ghi chú...',
                showCancelButton: true,
                confirmButtonColor: '#0BAA4B',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Phê duyệt',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/dieu-chuyen/duyet/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            GhiChuLanhDao: result.value
                        },
                        success: function (res) {
                            if (res.success) {
                                if (res.redirect_url) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Đã phê duyệt!',
                                        text: res.message + ' Cần tạo hợp đồng mới cho nhân viên này.',
                                        confirmButtonColor: '#0BAA4B',
                                        confirmButtonText: 'Tạo hợp đồng ngay'
                                    }).then(() => {
                                        window.location.href = res.redirect_url;
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: res.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON ? xhr.responseJSON.message :
                                'Có lỗi xảy ra khi phê duyệt.', 'error');
                        }
                    });
                }
            });
        }

        function rejectTransfer(id) {
            Swal.fire({
                title: 'Từ chối điều chuyển',
                input: 'textarea',
                inputLabel: 'Lý do từ chối',
                inputPlaceholder: 'Nhập lý do...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Vui lòng nhập lý do từ chối!'
                    }
                },
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Từ chối',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/dieu-chuyen/tu-choi/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            GhiChuLanhDao: result.value
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã từ chối!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON ? xhr.responseJSON.message :
                                'Có lỗi xảy ra khi từ chối.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
