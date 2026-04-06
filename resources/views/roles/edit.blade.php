@extends('layouts.app')

@section('title', 'Sửa Role - HRM')

@section('content')
    <div class="page-header">
        <h1>Cập nhật Role: {{ $role->name }}</h1>
        <p>Thay đổi tên Role và phân lại quyền</p>
    </div>

@push('styles')
<style>
    /* Tag-like checkbox styling */
    .perm-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 13px;
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #d1d5db;
        background: white;
        transition: all 0.15s;
        white-space: nowrap;
        user-select: none;
    }
    .perm-label:hover {
        border-color: #0BAA4B;
        background: #f0fdf4;
    }
    .perm-label input[type="checkbox"] {
        display: none;
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

    /* Group card styling */
    .perm-group-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .perm-group-header {
        background: #f3f4f6;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e5e7eb;
    }
    body.dark-theme .perm-group-card { border-color: #2e3349; }
    body.dark-theme .perm-group-header {
        background: #21263a !important;
        border-color: #2e3349 !important;
    }
    .perm-group-body {
        padding: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        background: #fdfdfd;
    }
    body.dark-theme .perm-group-body { background: #13161f; }
</style>
@endpush

    <div class="card">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            <div class="form-group mb-5">
                <label class="form-label">Tên Role (Vai trò) <span style="color:red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required placeholder="Nhập tên Role (vd: Nhân Viên IT)">
                @error('name')
                    <div style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #e5e7eb;">
                    <label class="form-label" style="margin-bottom:0;">Thiết lập quyền hạn (Permissions)</label>
                    <div style="display:flex; align-items:center; gap:15px;">
                         <span style="font-size:13px; color:#6b7280;" id="permSelectedCount">0 / 0 được chọn</span>
                         <label style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; cursor:pointer;">
                             <input type="checkbox" id="selectAllPermissions" style="width:16px; height:16px;">
                             <span>Chọn tất cả</span>
                         </label>
                    </div>
                </div>

                @php
                    // Define permission groups (same keywords as in users.edit)
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

                <div class="permissions-list">
                    @foreach($permGroups as $gi => $group)
                        @php $groupPerms = $groupedPerms[$gi] ?? []; @endphp
                        @if(!empty($groupPerms))
                            <div class="perm-group-card">
                                <div class="perm-group-header">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="font-size:18px;">{{ $group['icon'] }}</span>
                                        <span style="font-size:14px; font-weight:700; color:#374151;">{{ $group['label'] }}</span>
                                    </div>
                                    <label style="display:flex; align-items:center; gap:6px; font-size:12px; color:#6b7280; cursor:pointer;">
                                        <input type="checkbox" class="group-select-all" data-group="{{ $gi }}" style="width:14px; height:14px;">
                                        <span>Chọn nhóm</span>
                                    </label>
                                </div>
                                <div class="perm-group-body">
                                    @foreach($groupPerms as $perm)
                                        <label class="perm-label">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                                class="perm-checkbox" data-group="{{ $gi }}"
                                                {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
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
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="font-size:18px;">🔒</span>
                                    <span style="font-size:14px; font-weight:700; color:#374151;">Khác</span>
                                </div>
                            </div>
                            <div class="perm-group-body">
                                @foreach($ungrouped as $perm)
                                    <label class="perm-label">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                            class="perm-checkbox"
                                            {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                                        <span>{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 35px; padding-top:20px; border-top:1px solid #eee;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;margin-right:5px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </form>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllChk = document.getElementById('selectAllPermissions');
        const permCheckboxes = document.querySelectorAll('.perm-checkbox');
        const counter = document.getElementById('permSelectedCount');

        // Initial setup
        updateCounter();
        document.querySelectorAll('.group-select-all').forEach(groupChk => {
            updateGroupState(groupChk.dataset.group);
        });

        // 1. Master Select All
        selectAllChk.addEventListener('change', function() {
            permCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            document.querySelectorAll('.group-select-all').forEach(groupChk => {
                groupChk.checked = this.checked;
                groupChk.indeterminate = false;
            });
            updateCounter();
        });

        // 2. Group Select All
        document.querySelectorAll('.group-select-all').forEach(groupChk => {
            groupChk.addEventListener('change', function() {
                const groupId = this.dataset.group;
                document.querySelectorAll(`.perm-checkbox[data-group="${groupId}"]`).forEach(cb => {
                    cb.checked = this.checked;
                });
                updateGlobalState();
                updateCounter();
            });
        });

        // 3. Single checkbox change
        permCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const groupId = this.dataset.group;
                if (groupId) {
                    updateGroupState(groupId);
                }
                updateGlobalState();
                updateCounter();
            });
        });

        function updateGroupState(groupId) {
            const groupCheckboxes = document.querySelectorAll(`.perm-checkbox[data-group="${groupId}"]`);
            const checkedGroup = Array.from(groupCheckboxes).filter(cb => cb.checked).length;
            const groupAll = document.querySelector(`.group-select-all[data-group="${groupId}"]`);

            if (groupAll) {
                groupAll.checked = (checkedGroup === groupCheckboxes.length);
                groupAll.indeterminate = (checkedGroup > 0 && checkedGroup < groupCheckboxes.length);
            }
        }

        function updateGlobalState() {
            const total = permCheckboxes.length;
            const checked = Array.from(permCheckboxes).filter(cb => cb.checked).length;
            selectAllChk.checked = (checked === total && total > 0);
            selectAllChk.indeterminate = (checked > 0 && checked < total);
        }

        function updateCounter() {
            const total = permCheckboxes.length;
            const checked = Array.from(permCheckboxes).filter(cb => cb.checked).length;
            counter.textContent = `${checked} / ${total} được chọn`;
        }
    });
</script>
@endpush
@endsection
