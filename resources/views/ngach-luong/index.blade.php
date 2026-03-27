@extends('layouts.app')

@section('title', 'Quản lý Ngạch lương - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1>Danh sách Ngạch lương</h1>
                <p>Quản lý các ngạch lương trong hệ thống và thống kê nhân viên đang áp dụng</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> Thêm ngạch lương
            </button>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table table-bordered table-hover" id="ngachLuongTable" style="width: 100%;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Mã ngạch</th>
                        <th>Tên ngạch</th>
                        <th>Nhóm</th>
                        <th class="text-center">Số nhân viên</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ngachLuongs as $index => $ngach)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $ngach->Ma }}</strong></td>
                            <td>{{ $ngach->Ten }}</td>
                            <td><span class="badge" style="background-color: #f3f4f6; color: #374151;">{{ $ngach->Nhom }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $ngach->dien_bien_luongs_count > 0 ? 'badge-success' : '' }}" style="{{ $ngach->dien_bien_luongs_count == 0 ? 'background-color: #f3f4f6; color: #6b7280;' : '' }}">
                                    {{ $ngach->dien_bien_luongs_count }} nhân viên
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button class="btn btn-secondary btn-sm" style="padding: 4px 8px;" onclick="openEditModal({{ json_encode($ngach) }})" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm" style="padding: 4px 8px; background-color: #fee2e2; color: #dc2626;" onclick="deleteNgach({{ $ngach->Id }})" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="ngachLuongModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                <div class="modal-header" style="border-bottom: 1px solid #f3f4f6; padding: 20px 24px;">
                    <h5 class="modal-title" id="modalTitle" style="font-weight: 700; color: #1f2937;">Thêm ngạch lương mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ngachLuongForm">
                    @csrf
                    <input type="hidden" id="ngachId" name="Id">
                    <div class="modal-body" style="padding: 24px;">
                        <div class="form-group mb-4">
                            <label class="form-label">Mã ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Ma" id="inputMa" placeholder="VD: 01.003" required>
                            <div class="invalid-feedback" id="errorMa"></div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Tên ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Ten" id="inputTen" placeholder="VD: Chuyên viên" required>
                            <div class="invalid-feedback" id="errorTen"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nhóm ngạch <span style="color: #dc2626;">*</span></label>
                            <input type="text" class="form-control" name="Nhom" id="inputNhom" placeholder="VD: A1" required>
                            <div class="invalid-feedback" id="errorNhom"></div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 16px 24px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .text-center { text-align: center; }
        .btn-sm { font-size: 12px; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
    </style>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#ngachLuongTable').DataTable({
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
            responsive: true,
            autoWidth: false,
            pageLength: 25,
        });
    });

    const modal = new bootstrap.Modal(document.getElementById('ngachLuongModal'));
    const form = document.getElementById('ngachLuongForm');

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Thêm ngạch lương mới';
        document.getElementById('ngachId').value = '';
        form.reset();
        clearErrors();
        modal.show();
    }

    function openEditModal(data) {
        document.getElementById('modalTitle').innerText = 'Chỉnh sửa ngạch lương';
        document.getElementById('ngachId').value = data.Id;
        document.getElementById('inputMa').value = data.Ma;
        document.getElementById('inputTen').value = data.Ten;
        document.getElementById('inputNhom').value = data.Nhom;
        clearErrors();
        modal.show();
    }

    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').innerText = '';
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('ngachId').value;
        const url = id ? `{{ url('salary/ngach-luong/update') }}/${id}` : `{{ route('salary.ngach-luong.store') }}`;
        
        clearErrors();
        $('#btnSubmit').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Đang lưu...');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                if (response.status === 422) {
                    Object.keys(result.errors).forEach(key => {
                        $(`#input${key}`).addClass('is-invalid');
                        $(`#error${key}`).text(result.errors[key][0]);
                    });
                } else {
                    Swal.fire('Lỗi', result.message, 'error');
                }
            }
        } catch (error) {
            Swal.fire('Lỗi', 'Có lỗi kết nối hệ thống', 'error');
        } finally {
            $('#btnSubmit').prop('disabled', false).text('Lưu thay đổi');
        }
    };

    function deleteNgach(id) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn không thể hoàn tác sau khi hành động này!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Đồng ý xóa',
            cancelButtonText: 'Hủy'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`{{ url('salary/ngach-luong/delete') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('Thành công', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi', res.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Lỗi', 'Có lỗi kết nối hệ thống', 'error');
                }
            }
        });
    }
</script>
@endpush
