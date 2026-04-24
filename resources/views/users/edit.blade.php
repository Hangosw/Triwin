@extends('layouts.app')

@push('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #0BAA4B;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-row-3col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .form-row-3col {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .form-row-3col {
                grid-template-columns: 1fr;
            }
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group label span.text-danger {
            margin-left: 4px;
        }

        /* Dark Mode Overrides */
        body.dark-theme .form-section {
            background-color: #1a1d27;
            border-color: #2e3349;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        body.dark-theme .form-section h2 {
            color: #e8eaf0;
            border-bottom-color: #2e3349;
        }

        body.dark-theme .form-group label {
            color: #8b93a8;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
        }

        body.dark-theme .form-control, 
        body.dark-theme .form-select {
            background-color: #21263a;
            border-color: #2e3349;
            color: #e8eaf0;
        }

        /* Roles and Permissions Specific Styles */
        .roles-container {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.02);
            transition: border-color 0.2s, background-color 0.2s;
        }

        body.dark-theme .roles-container {
            border-color: #2e3349;
            background-color: #21263a;
        }

        .role-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            cursor: pointer;
            font-size: 14px;
        }

        body.dark-theme .role-label span {
            color: #e8eaf0;
        }

        #permissions-container {
            margin-top: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 16px;
            background: #fafafa;
            transition: border-color 0.2s, background-color 0.2s;
        }

        body.dark-theme #permissions-container {
            background: #21263a !important;
            border-color: #2e3349 !important;
        }

        .perm-group-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        body.dark-theme .perm-group-card {
            border-color: #2e3349;
        }

        .perm-group-header {
            background: #f9fafb;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #e5e7eb;
            border-left: 4px solid #0BAA4B;
        }

        body.dark-theme .perm-group-header {
            background: #1a1d27 !important;
            border-bottom-color: #2e3349 !important;
        }

        body.dark-theme .perm-group-header span {
            color: #e8eaf0 !important;
        }

        .perm-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid #d1d5db;
            background: white;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .perm-label:hover {
            border-color: #0BAA4B;
            background: #f0fdf4;
        }

        .perm-label:has(input:checked) {
            background: #dcfce7;
            border-color: #0BAA4B;
            color: #166534;
            font-weight: 600;
        }

        body.dark-theme .perm-label {
            background: #1a1d27;
            border-color: #2e3349;
            color: #c3c8da;
        }

        body.dark-theme .perm-label:hover {
            background: rgba(11, 170, 75, 0.1);
            border-color: #0BAA4B;
        }

        body.dark-theme .perm-label:has(input:checked) {
            background: rgba(11, 170, 75, 0.2);
            color: #4ade80;
        }

        #togglePermissions {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        body.dark-theme #togglePermissions {
            background: #21263a;
            border-color: #2e3349;
            color: #c3c8da;
        }

        #togglePermissions:hover {
            background: #e5e7eb;
        }

        body.dark-theme #togglePermissions:hover {
            background: #2e3349;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column-reverse;
            }
            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('title', 'Chỉnh sửa người dùng - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <!-- Header -->
    <div class="mb-4">
        <h1 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 8px; letter-spacing: -0.02em; line-height: 1.2;">
            Chỉnh sửa người dùng
        </h1>
        <p style="color: #6b7280; font-size: 16px; margin-bottom: 20px;">
            Cập nhật thông tin tài khoản người dùng ID: {{ $id }}
        </p>
        <a href="{{ route('nguoi-dung.danh-sach') }}" 
           style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; color: #1f2937; font-weight: 600; font-size: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;"
           onmouseover="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#f9fafb';"
           onmouseout="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white';">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    <form action="{{ route('nguoi-dung.cap-nhat', $id) }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Thông tin tài khoản -->
        <div class="form-section">
            <h2>Thông tin tài khoản</h2>
            <div class="form-row-3col">
                <div class="form-group">
                    <label>Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten', $user->Ten) }}"
                        placeholder="Nhập họ và tên" required>
                </div>

                <div class="form-group">
                    <label>Tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="TaiKhoan" class="form-control" value="{{ old('TaiKhoan', $user->TaiKhoan) }}"
                        placeholder="Nhập tài khoản" required>
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="TrangThai" class="form-select">
                        <option value="1" {{ old('TrangThai', $user->TrangThai) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="0" {{ old('TrangThai', $user->TrangThai) == 0 ? 'selected' : '' }}>Bị Khóa</option>
                    </select>
                </div>
            </div>

            <div class="form-row-3col">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="Email" class="form-control" value="{{ old('Email', $user->Email) }}"
                        placeholder="Nhập email">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control"
                        value="{{ old('SoDienThoai', $user->SoDienThoai) }}" placeholder="Nhập số điện thoại">
                </div>
            </div>
        </div>

        <!-- Đổi mật khẩu -->
        <div class="form-section">
            <h2>Đổi mật khẩu</h2>
            <p class="text-muted mb-3" style="font-size: 13px;">Để trống nếu không muốn thay đổi mật khẩu.</p>
            <div class="form-row-3col">
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label>Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="••••••••">
                </div>
            </div>
        </div>

        <!-- Vai trò & Quyền hạn -->
        <div class="form-section">
            <h2>Vai trò & Quyền hạn</h2>
            
            <div class="mb-4">
                <label class="form-label fw-bold mb-2">Vai trò (Roles)</label>
                <div class="roles-container">
                    <div class="row g-2">
                        @foreach ($roles as $role)
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="role-label">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="form-check-input role-checkbox"
                                        {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                    <span>{{ $role->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <small class="text-muted mt-1 d-block">System Admin mới chỉnh được hệ thống cao nhất.</small>
            </div>

            <div>
                <button type="button" id="togglePermissions">
                    <svg id="toggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;transition:transform .2s;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span id="toggleLabel">Hiển thị danh sách quyền trực tiếp</span>
                </button>

                <div id="permissions-container" style="display:none;">
                    <div style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:12px;">
                        <label style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; cursor:pointer;">
                            <input type="checkbox" id="selectAllPermissions">
                            <span>Chọn tất cả</span>
                        </label>
                        <span style="font-size:12px; color:#6b7280;" id="permSelectedCount">
                            {{ count($userPermissions) }} / {{ count($permissions) }} được chọn
                        </span>
                    </div>

                    @php
                        $permGroups = [
                            ['label' => 'Nhân viên',          'keywords' => ['nhân viên', 'thông tin']],
                            ['label' => 'Tổ chức',            'keywords' => ['tổ chức', 'phòng ban', 'chức vụ']],
                            ['label' => 'Người dùng',         'keywords' => ['người dùng']],
                            ['label' => 'Hợp đồng',           'keywords' => ['hợp đồng']],
                            ['label' => 'Chấm công',          'keywords' => ['chấm công']],
                            ['label' => 'Tăng ca',             'keywords' => ['tăng ca']],
                            ['label' => 'Work From Home',      'keywords' => ['wfh']],
                            ['label' => 'Nghỉ phép',           'keywords' => ['nghỉ phép', 'duyệt']],
                            ['label' => 'Lương',              'keywords' => ['lương']],
                            ['label' => 'Văn thư',            'keywords' => ['văn thư']],
                            ['label' => 'Công tác',           'keywords' => ['công tác']],
                            ['label' => 'Hệ thống',          'keywords' => ['hệ thống']],
                        ];

                        $groupedPerms = [];
                        $shownPermIds = [];

                        foreach ($permissions as $perm) {
                            $nameLower = mb_strtolower($perm->name);
                            $matched = false;
                            foreach ($permGroups as $gi => $group) {
                                foreach ($group['keywords'] as $kw) {
                                    if (str_contains($nameLower, mb_strtolower($kw))) {
                                        $groupedPerms[$gi][] = $perm;
                                        $shownPermIds[] = $perm->id;
                                        $matched = true;
                                        break;
                                    }
                                }
                                if ($matched) break;
                            }
                        }

                        $ungrouped = collect($permissions)->filter(fn($p) => !in_array($p->id, $shownPermIds));
                    @endphp

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        @foreach($permGroups as $gi => $group)
                            @php $groupPerms = $groupedPerms[$gi] ?? []; @endphp
                            @if(!empty($groupPerms))
                                <div class="perm-group-card">
                                    <div class="perm-group-header">
                                        <span style="font-size:14px; font-weight:700; color:#111827; text-transform: uppercase; flex:1;">{{ $group['label'] }}</span>
                                        <label style="display:flex; align-items:center; gap:5px; font-size:12px; color:#6b7280; cursor:pointer;">
                                            <input type="checkbox" class="group-select-all" data-group="{{ $gi }}">
                                            <span>Chọn nhóm</span>
                                        </label>
                                    </div>
                                    <div style="padding:15px; display:flex; flex-wrap:wrap; gap:8px;">
                                        @foreach($groupPerms as $perm)
                                            <label class="perm-label" data-group="{{ $gi }}">
                                                <input type="checkbox" name="permissions[]"
                                                       value="{{ $perm->name }}"
                                                       class="perm-checkbox"
                                                       data-group="{{ $gi }}"
                                                       {{ in_array($perm->name, $userPermissions) ? 'checked' : '' }}>
                                                <span>{{ $perm->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if($ungrouped->isNotEmpty())
                            <div class="perm-group-card">
                                <div class="perm-group-header">
                                    <span style="font-size:14px; font-weight:700; color:#111827; text-transform: uppercase; flex:1;">Khác</span>
                                </div>
                                <div style="padding:15px; display:flex; flex-wrap:wrap; gap:8px;">
                                    @foreach($ungrouped as $perm)
                                        <label class="perm-label">
                                            <input type="checkbox" name="permissions[]"
                                                   value="{{ $perm->name }}"
                                                   class="perm-checkbox"
                                                   {{ in_array($perm->name, $userPermissions) ? 'checked' : '' }}>
                                            <span>{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('nguoi-dung.danh-sach') }}" class="btn btn-light px-4 py-2" style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 500;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hủy bỏ
            </a>
            <button type="submit" class="btn btn-success px-4 py-2" style="display: inline-flex; align-items: center; gap: 8px; background: #0baa4b; border-color: #0baa4b; color: white; font-weight: 500;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Lưu thông tin
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const rolePermissions = @json($rolePermissions);

    $(document).ready(function() {
        const $toggleBtn = $('#togglePermissions');
        const $container = $('#permissions-container');
        const $toggleIcon = $('#toggleIcon');
        const $toggleLabel = $('#toggleLabel');
        const $selectAllChk = $('#selectAllPermissions');
        const $permCheckboxes = $('.perm-checkbox');
        const $roleCheckboxes = $('.role-checkbox');
        const $counter = $('#permSelectedCount');

        // Toggle show/hide permissions
        $toggleBtn.on('click', function() {
            $container.slideToggle(200, function() {
                const isVisible = $container.is(':visible');
                $toggleIcon.css('transform', isVisible ? 'rotate(180deg)' : 'rotate(0deg)');
                $toggleLabel.text(isVisible ? 'Thu gọn danh sách quyền trực tiếp' : 'Hiển thị danh sách quyền trực tiếp');
            });
        });

        // Select All permissions
        $selectAllChk.on('change', function() {
            $permCheckboxes.prop('checked', this.checked);
            updateAllGroupCheckboxes();
            updateCounter();
        });

        // Select by Group
        $(document).on('change', '.group-select-all', function() {
            const groupId = $(this).data('group');
            $(`.perm-checkbox[data-group="${groupId}"]`).prop('checked', this.checked);
            updateGlobalCheckbox();
            updateCounter();
        });

        // Update individual permission change
        $(document).on('change', '.perm-checkbox', function() {
            const groupId = $(this).data('group');
            if (groupId !== undefined) {
                updateGroupCheckbox(groupId);
            }
            updateGlobalCheckbox();
            updateCounter();
        });

        function updateGroupCheckbox(gi) {
            const $groupBoxes = $(`.perm-checkbox[data-group="${gi}"]`);
            const total = $groupBoxes.length;
            const checked = $groupBoxes.filter(':checked').length;
            $(`.group-select-all[data-group="${gi}"]`)
                .prop('checked', checked === total)
                .prop('indeterminate', checked > 0 && checked < total);
        }

        function updateAllGroupCheckboxes() {
            $('.group-select-all').each(function() {
                updateGroupCheckbox($(this).data('group'));
            });
        }

        function updateGlobalCheckbox() {
            const total = $permCheckboxes.length;
            const checked = $permCheckboxes.filter(':checked').length;
            $selectAllChk.prop('checked', total === checked && total > 0)
                         .prop('indeterminate', checked > 0 && checked < total);
        }

        function updateCounter() {
            const total = $permCheckboxes.length;
            const checked = $permCheckboxes.filter(':checked').length;
            $counter.text(`${checked} / ${total} được chọn`);
        }

        // Sync Roles -> Permissions
        $roleCheckboxes.on('change', function() {
            const roleName = $(this).val();
            const perms = rolePermissions[roleName] || [];
            
            if (this.checked) {
                perms.forEach(pName => {
                    $(`.perm-checkbox[value="${pName}"]`).prop('checked', true);
                });
            } else {
                const otherSelectedRoles = $roleCheckboxes.filter(':checked').map(function() {
                    return $(this).val();
                }).get();
                
                const permsFromOtherRoles = [];
                otherSelectedRoles.forEach(r => {
                    if (rolePermissions[r]) {
                        permsFromOtherRoles.push(...rolePermissions[r]);
                    }
                });

                perms.forEach(pName => {
                    if (!permsFromOtherRoles.includes(pName)) {
                        $(`.perm-checkbox[value="${pName}"]`).prop('checked', false);
                    }
                });
            }
            updateAllGroupCheckboxes();
            updateGlobalCheckbox();
            updateCounter();
        });

        // Initial setup
        updateAllGroupCheckboxes();
        updateGlobalCheckbox();
        updateCounter();

        // Auto-expand if permissions exist
        if ($permCheckboxes.filter(':checked').length > 0) {
            $container.show();
            $toggleIcon.css('transform', 'rotate(180deg)');
            $toggleLabel.text('Thu gọn danh sách quyền trực tiếp');
        }
    });
</script>
@endpush
