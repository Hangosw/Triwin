@extends('layouts.app')

@push('styles')
<style>
    /* Dark mode overrides for permission section */
    #roles-container {
        border: 1px solid #d1d5db;
        transition: border-color 0.2s, background-color 0.2s;
    }
    body.dark-theme #roles-container {
        border-color: #2e3349 !important;
        background-color: #21263a;
    }
    body.dark-theme #roles-container span,
    body.dark-theme .role-label span { color: #e8eaf0; }

    #permissions-container {
        transition: border-color 0.2s, background-color 0.2s;
    }
    body.dark-theme #permissions-container {
        background: #21263a !important;
        border-color: #2e3349 !important;
    }
    body.dark-theme #permissions-container #permSelectedCount { color: #6b7492 !important; }

    /* Permission group cards */
    .perm-group-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        transition: border-color 0.2s;
    }
    body.dark-theme .perm-group-card { border-color: #2e3349; }

    .perm-group-header {
        background: #f3f4f6;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #e5e7eb;
        transition: background 0.2s, border-color 0.2s;
    }
    body.dark-theme .perm-group-header {
        background: #21263a !important;
        border-color: #2e3349 !important;
    }
    body.dark-theme .perm-group-header span { color: #c3c8da !important; }
    body.dark-theme .perm-group-header label { color: #6b7492 !important; }

    /* Permission badge pills */
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
        border-color: #0BAA4B;
        background: rgba(11,170,75,0.08);
    }
    body.dark-theme .perm-label:has(input:checked) {
        background: rgba(11,170,75,0.15);
        border-color: #0BAA4B;
        color: #4ade80;
    }
    body.dark-theme .perm-group-body { background: #13161f; }

    #togglePermissions {
        color: #374151;
        border: 1px solid #9ca3af;
        background: none;
        border-radius: 6px;
        padding: 4px 12px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s, border-color 0.2s;
    }
    body.dark-theme #togglePermissions { color: #c3c8da; border-color: #2e3349; }
    body.dark-theme #togglePermissions:hover { color: #0BAA4B; border-color: #0BAA4B; }
</style>
@endpush


@section('title', 'Chỉnh sửa người dùng - Vietnam Rubber Group')


@push('scripts')
    <script>
        $(document).ready(function() {
            // All script logic is consolidated in the push('scripts') block at the bottom of the file
            // to prevent duplication and toggle conflicts.
        });
    </script>
@endpush

@section('content')
    <div class="page-header">
        <h1>Chỉnh sửa người dùng</h1>
        <p>Cập nhật thông tin tài khoản người dùng ID: {{ $id }}</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <form action="{{ route('nguoi-dung.cap-nhat', $id) }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 24px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: 24px;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="Ten" class="form-control" value="{{ old('Ten', $user->Ten) }}"
                        placeholder="Nhập họ và tên">
                </div>

                <div class="form-group">
                    <label class="form-label">Tài khoản</label>
                    <input type="text" name="TaiKhoan" class="form-control" value="{{ old('TaiKhoan', $user->TaiKhoan) }}"
                        placeholder="Nhập tài khoản" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="SoDienThoai" class="form-control"
                        value="{{ old('SoDienThoai', $user->SoDienThoai) }}" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="Email" class="form-control" value="{{ old('Email', $user->Email) }}"
                        placeholder="Nhập email">
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu (Để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                </div>

                <div class="form-group">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Nhập lại mật khẩu mới">
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select name="TrangThai" class="form-control">
                        <option value="1" {{ old('TrangThai', $user->TrangThai) == 1 ? 'selected' : '' }}>Đang hoạt động
                        </option>
                        <option value="0" {{ old('TrangThai', $user->TrangThai) == 0 ? 'selected' : '' }}>Bị Khóa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Phân quyền (Roles) <span style="font-size:12px;color:gray;">(Chỉ có System
                            Admin mới chỉnh được hệ thống cao nhất)</span></label>
                    <div id="roles-container"
                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; padding: 12px; border-radius: 8px;">
                        @foreach ($roles as $role)
                            <label style="display:flex; align-items:center; gap:8px; cursor: pointer;" class="role-label">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="role-checkbox"
                                    {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Quyền trực tiếp (Direct Permissions) --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">
                        Quyền trực tiếp (Permissions)
                        <span style="font-size:12px;color:gray;"> – cấp thêm ngoài vai trò</span>
                    </label>

                    {{-- Toggle show/hide --}}
                    <div style="margin-top:6px; margin-bottom:6px;">
                        <button type="button" id="togglePermissions">
                            <svg id="toggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;transition:transform .2s;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span id="toggleLabel">Hiển thị danh sách quyền</span>
                        </button>
                    </div>

                    <div id="permissions-container" style="display:none; border:1px solid #d1d5db; border-radius:8px; padding:16px; background:#fafafa;">
                        {{-- Select All / Deselect All --}}
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
                            // Định nghĩa nhóm quyền theo từ khóa (case-insensitive keyword matching)
                            $permGroups = [
                                ['icon' => '👤', 'label' => 'Nhân viên',          'keywords' => ['nhân viên', 'thông tin']],
                                ['icon' => '🏢', 'label' => 'Tổ chức',            'keywords' => ['tổ chức', 'phòng ban', 'chức vụ']],
                                ['icon' => '🔑', 'label' => 'Người dùng',         'keywords' => ['người dùng']],
                                ['icon' => '📄', 'label' => 'Hợp đồng',           'keywords' => ['hợp đồng']],
                                ['icon' => '⏰', 'label' => 'Chấm công',          'keywords' => ['chấm công']],
                                ['icon' => '🕐', 'label' => 'Tăng ca',             'keywords' => ['tăng ca']],
                                ['icon' => '🌴', 'label' => 'Nghỉ phép',           'keywords' => ['nghỉ phép', 'duyệt']],
                                ['icon' => '💰', 'label' => 'Lương',              'keywords' => ['lương']],
                                ['icon' => '📝', 'label' => 'Văn thư',            'keywords' => ['văn thư']],
                                ['icon' => '✈️', 'label' => 'Công tác',           'keywords' => ['công tác']],
                                ['icon' => '⚙️', 'label' => 'Hệ thống',          'keywords' => ['hệ thống']],
                            ];

                            // Phân loại quyền vào nhóm bằng keyword matching
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

                            // Quyền chưa match nhóm nào
                            $ungrouped = collect($permissions)->filter(fn($p) => !in_array($p->id, $shownPermIds));
                        @endphp

                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            @foreach($permGroups as $gi => $group)
                                @php $groupPerms = $groupedPerms[$gi] ?? []; @endphp
                                @if(!empty($groupPerms))
                                    <div style="border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                                        {{-- Group header --}}
                                        <div class="perm-group-header">
                                            <span style="font-size:16px;">{{ $group['icon'] }}</span>
                                            <span style="font-size:13px; font-weight:700; color:#374151; flex:1;">{{ $group['label'] }}</span>
                                            <label style="display:flex; align-items:center; gap:5px; font-size:12px; color:#6b7280; cursor:pointer;">
                                                <input type="checkbox" class="group-select-all" data-group="{{ $gi }}" style="width:14px;height:14px;">
                                                <span>Chọn nhóm</span>
                                            </label>
                                        </div>
                                        {{-- Group permissions --}}
                                        <div style="padding:10px 14px; display:flex; flex-wrap:wrap; gap:8px;">
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
                                <div style="border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                                    <div class="perm-group-header" style="background:#f3f4f6; padding:8px 14px; font-size:13px; font-weight:700; color:#374151;">
                                        🔒 Khác
                                    </div>
                                    <div style="padding:10px 14px; display:flex; flex-wrap:wrap; gap:8px;">
                                        @foreach($ungrouped as $perm)
                                            <label class="perm-label"
                                                   style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:13px;
                                                          padding:5px 10px; border-radius:20px; border:1px solid #d1d5db;
                                                          background:white; transition:all .15s; white-space:nowrap;">
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

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật thông tin
                </button>
                <a href="{{ route('nguoi-dung.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>
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

        // 1. Toggle show/hide permissions with slide effect
        $toggleBtn.on('click', function() {
            $container.slideToggle(200, function() {
                const isVisible = $container.is(':visible');
                $toggleIcon.css('transform', isVisible ? 'rotate(180deg)' : 'rotate(0deg)');
                $toggleLabel.text(isVisible ? 'Thu gọn danh sách quyền' : 'Hiển thị danh sách quyền');
            });
        });

        // 2. Select All permissions
        $selectAllChk.on('change', function() {
            $permCheckboxes.prop('checked', this.checked);
            updateAllGroupCheckboxes();
            updateCounter();
        });

        // 3. Select by Group
        $(document).on('change', '.group-select-all', function() {
            const groupId = $(this).data('group');
            $(`.perm-checkbox[data-group="${groupId}"]`).prop('checked', this.checked);
            updateGlobalCheckbox();
            updateCounter();
        });

        // 4. Update individual permission change
        $(document).on('change', '.perm-checkbox', function() {
            const groupId = $(this).data('group');
            updateGroupCheckbox(groupId);
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

        // 5. Sync Roles -> Permissions
        $roleCheckboxes.on('change', function() {
            const roleName = $(this).val();
            const perms = rolePermissions[roleName] || [];
            
            if (this.checked) {
                // Check all permissions of this role
                perms.forEach(pName => {
                    $(`.perm-checkbox[value="${pName}"]`).prop('checked', true);
                });
            } else {
                // Uncheck only if not granted by other selected roles
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
            $toggleLabel.text('Thu gọn danh sách quyền');
        }
    });
</script>

<style>
    #togglePermissions {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    #togglePermissions:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }
    body.dark-theme #togglePermissions {
        background: #21263a;
        border-color: #2e3349;
        color: #c3c8da;
    }
    body.dark-theme #togglePermissions:hover {
        background: #2e3349;
    }
</style>
@endpush
