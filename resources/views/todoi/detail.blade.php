@extends('layouts.app')

@section('title', 'Chi tiết Tổ Đội - Vietnam Rubber Group')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('to-doi.danh-sach') }}" class="text-decoration-none text-muted mb-2 d-inline-block" style="color: #6c757d;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách
            </a>
            <h1>Chi tiết Tổ Đội: {{ $todoi->Ten }}</h1>
            <p class="text-muted">Thông tin chung và danh sách nhân sự của tổ đội</p>
        </div>
        <div>
            <!-- Bạn có thể thêm nút sửa ở đây nếu cần -->
            <a href="#" class="btn btn-primary" style="background-color: #0BAA4B; border-color: #0BAA4B;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 4px; display: inline-block;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- Cards Layout -->
    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
        
        <!-- Cột thông tin chung -->
        <div style="flex: 1; min-width: 300px; max-width: 400px;">
            <div class="card" style="height: 100%;">
                <div style="padding: 20px 24px; border-bottom: 1px solid #e5e7eb;">
                    <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Thông tin chung</h5>
                </div>
                <div style="padding: 24px;">
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 16px;">
                        <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e5e7eb; padding-bottom: 16px;">
                            <span style="color: #6b7280; font-size: 14px;">Mã Tổ Đội</span>
                            <span style="font-weight: 600; color: #111827;">{{ $todoi->Ma }}</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e5e7eb; padding-bottom: 16px;">
                            <span style="color: #6b7280; font-size: 14px;">Tên Tổ Đội</span>
                            <span style="font-weight: 600; color: #111827;">{{ $todoi->Ten }}</span>
                        </li>

                        <li style="border-bottom: 1px dashed #e5e7eb; padding-bottom: 16px;">
                            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Thuộc Phòng Ban</div>
                            <div style="font-weight: 600; color: #111827;">{{ $todoi->phongBan->Ten ?? 'Chưa xác định' }}</div>
                        </li>
                        <li style="border-bottom: 1px dashed #e5e7eb; padding-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div style="color: #6b7280; font-size: 14px;">Tổ trưởng hiện tại</div>
                                <button type="button" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px; height: auto;" onclick="openChangeLeaderModal()">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px; margin-right: 4px; display: inline-block;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Thay đổi
                                </button>
                            </div>
                            @if ($truongTo)
                                <div>
                                    <div class="truong-to-badge">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $truongTo->Ten }}
                                    </div>
                                    <div style="color: #6b7280; font-size: 13px; margin-top: 6px; margin-left: 2px;">
                                        {{ $truongTo->Ma }} - {{ $truongTo->ttCongViec->chucVu->Ten ?? 'Chưa rõ chức vụ' }}
                                    </div>
                                </div>
                            @else
                                <span style="color: #9ca3af; font-style: italic; font-size: 14px;">Chưa phân công</span>
                            @endif
                        </li>
                        <li>
                            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Ghi chú</div>
                            <div style="color: #111827; font-size: 14px;">{{ $todoi->GhiChu ?: 'Không có ghi chú' }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cột danh sách thành viên -->
        <div style="flex: 2; min-width: 0;">
            <div class="card" style="height: 100%;">
                <div class="action-bar" style="border-bottom: 1px solid #e5e7eb; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; background: transparent; box-shadow: none;">
                    <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #111827;">Danh sách thành viên ({{ $thanhViens->count() }})</h5>
                    <button type="button" class="btn btn-secondary" onclick="openAddMemberModal()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-right: 4px; display: inline-block;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Thêm thành viên
                    </button>
                </div>
                
                <div class="table-container" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">STT</th>
                                <th>Mã NV</th>
                                <th>Họ và Tên</th>
                                <th>Chức vụ</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($thanhViens as $nv)
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>{{ $nv->Ma }}</td>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <strong style="color: #111827; font-weight: 500;">{{ $nv->Ten }}</strong>
                                            @if ($truongTo && $truongTo->id == $nv->id)
                                                <span style="background-color: #d1fae5; color: #065f46; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">
                                                    Tổ trưởng
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $nv->ttCongViec->chucVu->Ten ?? 'Chưa rõ' }}</td>
                                    <td>
                                        @if ($nv->ttCongViec && $nv->ttCongViec->TrangThai == 'DangLamViec')
                                            <span style="display: inline-flex; align-items: center; padding: 4px 8px; font-size: 12px; font-weight: 500; color: #0BAA4B; background-color: #e8f5e9; border-radius: 9999px;">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #198754; margin-right: 6px;"></span>
                                                Đang làm việc
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; padding: 4px 8px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 9999px;">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #9ca3af; margin-right: 6px;"></span>
                                                Đã nghỉ việc
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px 0; color: #6b7280;">
                                        <div style="margin-bottom: 8px;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto; color: #d1d5db;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        Chưa có thành viên nào trong tổ đội này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Tái sử dụng badge xanh lá từ trang index */
        .truong-to-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background-color: #f0fdf4;
            color: #0BAA4B;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            font-weight: 500;
            font-size: 13px;
            gap: 6px;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }
    </style>
    <!-- Add Member Modal -->
    <div class="modal" id="addMemberModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm thành viên vào Tổ Đội</h2>
                <button type="button" class="close-modal" onclick="closeAddMemberModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('to-doi.add-member', $todoi->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="NhanVienIds" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Chọn nhân viên <span class="text-danger" style="color: #ef4444;">*</span></label>
                        <select class="form-select select2-multiple @error('NhanVienIds') is-invalid @enderror" id="NhanVienIds" name="NhanVienIds[]" multiple="multiple" required style="width: 100%;">
                            @foreach($nhanVienKhacs as $nv)
                                <option value="{{ $nv->id }}" {{ (is_array(old('NhanVienIds')) && in_array($nv->id, old('NhanVienIds'))) ? 'selected' : '' }}>
                                    {{ $nv->Ma }} - {{ $nv->Ten }} ({{ $nv->ttCongViec->chucVu->Ten ?? 'Chưa rõ chức vụ' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted" style="margin-top: 8px; font-size: 13px; color: #6b7280; line-height: 1.5;">
                            Danh sách hiển thị các nhân viên thuộc phòng ban <strong>{{ $todoi->phongBan->Ten ?? '' }}</strong> nhưng chưa thuộc tổ đội này. Bạn có thể chọn nhiều nhân viên cùng lúc.
                        </div>
                        @error('NhanVienIds')
                            <div class="invalid-feedback" style="color: #ef4444; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddMemberModal()">Đóng</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #0BAA4B; border-color: #0BAA4B;">Thêm vào tổ đội</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Leader Modal -->
    <div class="modal" id="changeLeaderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thay đổi Tổ trưởng</h2>
                <button type="button" class="close-modal" onclick="closeChangeLeaderModal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('to-doi.change-leader', $todoi->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="NewLeaderId" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Chọn Tổ trưởng mới <span class="text-danger" style="color: #ef4444;">*</span></label>
                        <select class="form-select select2-single @error('NewLeaderId') is-invalid @enderror" id="NewLeaderId" name="NewLeaderId" required style="width: 100%;">
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($thanhViens as $nv)
                                @if(!$truongTo || $truongTo->id != $nv->id)
                                    <option value="{{ $nv->id }}" {{ old('NewLeaderId') == $nv->id ? 'selected' : '' }}>
                                        {{ $nv->Ma }} - {{ $nv->Ten }} ({{ $nv->ttCongViec->chucVu->Ten ?? 'Chưa rõ chức vụ' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="form-text text-muted" style="margin-top: 8px; font-size: 13px; color: #6b7280; line-height: 1.5;">
                            Chỉ chọn từ danh sách các thành viên hiện tại của tổ đội.
                        </div>
                        @error('NewLeaderId')
                            <div class="invalid-feedback" style="color: #ef4444; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeChangeLeaderModal()">Đóng</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #0BAA4B; border-color: #0BAA4B;">Thay đổi</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .modal-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f9fafb;
    }

    .modal-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        background-color: #f9fafb;
    }

    .close-modal {
        background: none;
        border: none;
        cursor: pointer;
        color: #6b7280;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
        border-radius: 4px;
    }

    .close-modal:hover {
        color: #1f2937;
        background-color: #e5e7eb;
    }

    /* Select2 customization for modal */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        min-height: 42px;
        padding: 4px 8px;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0BAA4B;
        box-shadow: 0 0 0 3px rgba(15, 81, 50, 0.1);
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e8f5e9;
        border: 1px solid #bbf7d0;
        color: #0BAA4B;
        border-radius: 4px;
        padding: 4px 8px;
        margin-top: 4px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #0BAA4B;
        margin-right: 6px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: transparent;
        color: #065f46;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiple selection
        $('#NhanVienIds').select2({
            placeholder: '-- Chọn một hoặc nhiều nhân viên --',
            allowClear: true,
            dropdownParent: $('#addMemberModal')
        });

        // Initialize Select2 for single selection
        $('#NewLeaderId').select2({
            placeholder: '-- Chọn một nhân viên --',
            allowClear: true,
            dropdownParent: $('#changeLeaderModal')
        });
    });

    // Modal Add Member
    function openAddMemberModal() {
        document.getElementById('addMemberModal').classList.add('show');
    }

    function closeAddMemberModal() {
        document.getElementById('addMemberModal').classList.remove('show');
    }

    document.getElementById('addMemberModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeAddMemberModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeAddMemberModal();
            closeChangeLeaderModal();
        }
    });

    // Modal Change Leader
    function openChangeLeaderModal() {
        document.getElementById('changeLeaderModal').classList.add('show');
    }

    function closeChangeLeaderModal() {
        document.getElementById('changeLeaderModal').classList.remove('show');
    }

    document.getElementById('changeLeaderModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeChangeLeaderModal();
        }
    });
</script>
@endpush
