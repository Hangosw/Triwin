@extends('layouts.app')

@section('title', 'Cấu hình loại nghỉ phép')

@push('styles')
    <style>
        :root {
            --primary-green: #0BAA4B;
            --secondary-green: #D1E7DD;
            --text-muted: #6b7280;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-green);
            color: white;
        }

        .btn-primary:hover {
            background: #0a3d26;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            background: #fff;
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        .btn-outline-danger:hover {
            background: #fee2e2;
        }

        .btn-outline-primary {
            background: #fff;
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }

        .btn-outline-primary:hover {
            background: var(--secondary-green);
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9fafb;
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #1f2937;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background: #dcfce7;
            color: #088c3d;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            width: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 24px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px var(--secondary-green);
        }

        .help-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Cấu hình nghỉ phép</h1>
        <p>Quản lý các loại nghỉ phép và quy định hưởng lương</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách loại nghỉ phép</h3>
            <button class="btn btn-primary" onclick="openModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm loại nghỉ mới
            </button>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tền loại nghỉ</th>
                        <th>% Hưởng lương</th>
                        <th>Hạn mức</th>
                        <th style="width: 100px;">Hạn mức (ngày)</th>
                        <th>Trạng thái</th>
                        <th style="width: 200px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loaiNghiPheps as $loai)
                        <tr>
                            <td>{{ $loai->id }}</td>
                            <td class="font-medium">{{ $loai->Ten }}</td>
                            <td>
                                <span class="badge {{ $loai->HuongLuong > 0 ? 'badge-success' : 'badge-gray' }}">
                                    {{ (float) $loai->HuongLuong }}%
                                </span>
                            </td>
                            <td>
                                @if($loai->CoHanMuc === 1)
                                    <span class="badge badge-success">Có giới hạn</span>
                                @else
                                    <span class="badge badge-gray">Không giới hạn</span>
                                @endif
                            </td>
                            <td>
                                @if($loai->CoHanMuc === 1)
                                    <span class="font-bold text-primary-green">{{ $loai->HanMucToiDa ?? 0 }} ngày</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($loai->TrangThai == 1)
                                    <span class="badge badge-success">Đang hoạt động</span>
                                @else
                                    <span class="badge" style="background: #fee2e2; color: #ef4444;">Đã khóa</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-outline-primary" style="padding: 6px 12px;"
                                        onclick="editLoaiPhep({{ json_encode($loai) }})">
                                        Sửa
                                    </button>
                                    @if($loai->TrangThai == 1)
                                    <button class="btn btn-outline-danger" style="padding: 6px 12px;"
                                        onclick="deleteLoaiPhep({{ $loai->id }})">
                                        Khóa
                                    </button>
                                    @else
                                    <button class="btn" style="padding: 6px 12px; background: #f3f4f6; color: #9ca3af; cursor: not-allowed;" title="Đã khóa" onclick="return false;">
                                        Đã khóa
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal" id="loaiPhepModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="font-size: 20px; font-weight: 700;">Thêm loại nghỉ phép</h2>
                <button onclick="closeModal()" style="border: none; background: none; cursor: pointer;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="loaiPhepForm" onsubmit="saveLoaiPhep(event)">
                @csrf
                <input type="hidden" name="id" id="loaiPhepId">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tên loại nghỉ phép <span style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-control" name="Ten" id="inputTen" required
                            placeholder="VD: Nghỉ phép năm, Nghỉ việc riêng...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tỷ lệ hưởng lương (0 - 100%) <span
                                style="color: #ef4444;">*</span></label>
                        <input type="number" class="form-control" name="HuongLuong" id="inputHuongLuong" step="1" min="0"
                            max="100" required placeholder="VD: 100 cho 100%, 75 cho 75%">
                        <p class="help-text">Nhập 100 nếu hưởng nguyên lương, 0 nếu nghỉ không lương.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Có giới hạn số ngày trong năm?</label>
                        <select class="form-control" name="CoHanMuc" id="inputCoHanMuc">
                            <option value="1">Có giới hạn (VD: Phép năm có 12 ngày)</option>
                            <option value="0">Không giới hạn (VD: Nghỉ thai sản, công tác...)</option>
                        </select>
                    </div>
                    <div class="form-group" id="groupHanMucToiDa" style="display: none;">
                        <label class="form-label">Hạn mức số ngày trong năm <span style="color: #ef4444;">*</span></label>
                        <input type="number" class="form-control" name="HanMucToiDa" id="inputHanMucToiDa" min="1"
                            placeholder="VD: 12">
                    </div>
                </div>
                <div
                    style="padding: 16px 24px; background: #f9fafb; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn" style="background: #e5e7eb; color: #374151;"
                        onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleHanMucField() {
            const select = document.getElementById('inputCoHanMuc');
            const group = document.getElementById('groupHanMucToiDa');
            const input = document.getElementById('inputHanMucToiDa');
            if (select.value == '1') {
                group.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                group.style.display = 'none';
                input.removeAttribute('required');
            }
        }

        document.getElementById('inputCoHanMuc').addEventListener('change', toggleHanMucField);

        function openModal() {
            document.getElementById('modalTitle').innerText = 'Thêm loại nghỉ phép';
            document.getElementById('loaiPhepId').value = '';
            document.getElementById('loaiPhepForm').reset();
            toggleHanMucField(); // Sync visibility after reset
            document.getElementById('loaiPhepModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('loaiPhepModal').classList.remove('show');
        }

        function editLoaiPhep(loai) {
            document.getElementById('modalTitle').innerText = 'Chỉnh sửa loại nghỉ phép';
            document.getElementById('loaiPhepId').value = loai.id;
            document.getElementById('inputTen').value = loai.Ten;
            document.getElementById('inputHuongLuong').value = loai.HuongLuong;
            document.getElementById('inputCoHanMuc').value = loai.CoHanMuc;
            document.getElementById('inputHanMucToiDa').value = loai.HanMucToiDa || '';
            toggleHanMucField(); // Sync visibility after setting values
            document.getElementById('loaiPhepModal').classList.add('show');
        }

        function saveLoaiPhep(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('{{ route("nghi-phep.config.save") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                            confirmButtonColor: '#0BAA4B'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi hệ thống',
                        text: 'Có lỗi xảy ra khi lưu dữ liệu.',
                        confirmButtonColor: '#0BAA4B'
                    });
                });
        }

        function deleteLoaiPhep(id) {
            Swal.fire({
                title: 'Xác nhận khóa?',
                text: "Loại nghỉ phép này sẽ không còn hiển thị cho nhân viên chọn nữa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Đồng ý khóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('nghi-phep/config/delete') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã khóa',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Không thể khóa',
                                    text: data.message,
                                    confirmButtonColor: '#0BAA4B'
                                });
                            }
                        });
                }
            });
        }
    </script>
@endpush
