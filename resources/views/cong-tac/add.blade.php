@extends('layouts.app')

@section('title', 'Thêm mới Công tác - HRM')

@push('styles')
    <style>
        .custom-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0;
        }

        .custom-tab-btn {
            padding: 12px 24px;
            background: none;
            border: none;
            font-size: 15px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .custom-tab-btn:hover {
            color: #1f2937;
        }

        .custom-tab-btn.active {
            color: #0BAA4B;
        }

        .custom-tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #0BAA4B;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
        <!-- Tabs Header -->
        <div class="custom-tabs">
            @can('Quản lý công tác')
                <button class="custom-tab-btn active" onclick="switchTab('top-down', this)">
                    <i class="bi bi-diagram-3" style="margin-right:8px;"></i> Quản lý Phân công (Top-down)
                </button>
            @endcan
            <button class="custom-tab-btn {{ !auth()->user()->can('create cong-tac') ? 'active' : '' }}"
                onclick="switchTab('bottom-up', this)">
                <i class="bi bi-person-lines-fill" style="margin-right:8px;"></i> Cá nhân Đăng ký (Bottom-up)
            </button>
        </div>

        <!-- Tab 1: Top-down (Phân công) -->
        @can('Quản lý công tác')
            <div id="tab-top-down" class="tab-pane active">
                <form action="{{ route('cong-tac.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="top-down">

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
                    </div>

                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">Lưu Nhiệm vụ Công tác</button>
                        <a href="{{ route('cong-tac.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        @endcan

        <!-- Tab 2: Bottom-up (Cá nhân tự khai báo) -->
        <div id="tab-bottom-up" class="tab-pane {{ !auth()->user()->can('create cong-tac') ? 'active' : '' }}">
            <form action="{{ route('cong-tac.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="bottom_up">

                <div class="auth-user-card">
                    <div class="auth-avatar">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: #64748b;">Hồ sơ nhân sự tự khai báo</div>
                        <div style="font-size: 16px; font-weight: 600; color: #0f172a;">
                            {{ auth()->user()->nhanVien->Ten ?? auth()->user()->TaiKhoan }}
                        </div>
                        <div style="font-size: 13px; color: #64748b;">Mã NV:
                            {{ auth()->user()->nhanVien->Ma ?? 'Chưa liên kết hồ sơ' }}
                        </div>
                    </div>
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
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary" style="background-color: #4f46e5;">Đăng ký Quá trình Công
                        tác</button>
                    <a href="{{ route('cong-tac.danh-sach') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });

        function switchTab(tabId, btn) {
            // Cập nhật trạng thái nút
            document.querySelectorAll('.custom-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Ẩn/hiện nội dung
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');
        }
    </script>
@endpush
