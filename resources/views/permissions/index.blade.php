@extends('layouts.app')

@section('title', 'Quản lý Quyền - HRM')

@push('styles')
<style>
    /* Group card styling */
    .perm-group-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .perm-group-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .perm-group-header {
        background: #f8fafc;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e5e7eb;
        border-left: 5px solid #0BAA4B;
    }
    .perm-group-body {
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        background: #ffffff;
    }

    /* Permission Pill Styling */
    .perm-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s;
        position: relative;
        cursor: default;
    }
    .perm-pill:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .perm-pill .role-badge {
        font-size: 11px;
        background: #cbd5e1;
        color: #475569;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }
    .perm-pill:hover .role-badge {
        background: #94a3b8;
        color: white;
    }

    /* Actions inside pill */
    .pill-actions {
        display: flex;
        gap: 4px;
        margin-left: 4px;
        opacity: 0;
        transition: opacity 0.2s;
        border-left: 1px solid #cbd5e1;
        padding-left: 8px;
    }
    .perm-pill:hover .pill-actions {
        opacity: 1;
    }
    .action-btn {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
        color: #64748b;
    }
    .action-btn:hover {
        background: rgba(0,0,0,0.05);
    }
    .action-btn.edit:hover { color: #6366f1; }
    .action-btn.delete:hover { color: #ef4444; }

    /* Search bar focus */
    .search-input:focus {
        border-color: #0BAA4B;
        box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
    }

    body.dark-theme .perm-group-card { background: #1a1d27; border-color: #2e3349; }
    body.dark-theme .perm-group-header { background: #21263a; border-color: #2e3349; }
    body.dark-theme .perm-group-body { background: #1a1d27; }
    body.dark-theme .perm-pill { background: #2e3349; border-color: #3f4662; color: #e2e8f0; }
    body.dark-theme .perm-pill:hover { background: #3f4662; }
    body.dark-theme .perm-pill .role-badge { background: #1a1d27; color: #94a3b8; }
</style>
@endpush

@section('content')
    <div class="page-header" style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 style="font-weight: 800; letter-spacing: -0.5px;">Quản lý Quyền (Permissions)</h1>
                <p style="color: #64748b;">Liệt kê và thiết lập danh sách quyền hạn trung tâm</p>
            </div>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary" style="border-radius: 10px; padding: 12px 24px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(11, 170, 75, 0.2);">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Thêm Quyền Mới
            </a>
        </div>
    </div>

    <!-- Search Section -->
    <div style="margin-bottom: 30px; position: relative; max-width: 600px;">
        <input type="text" id="permissionSearch" class="form-control search-input" placeholder="Tìm kiếm nhanh quyền hoặc mục..." 
               style="padding: 14px 20px 14px 50px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 15px; width: 100%;">
        <svg style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; color: #94a3b8;" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>

    <!-- Permission Groups -->
    <div id="permissionsGrid">
        @foreach ($groupedPermissions as $groupName => $permissions)
            <div class="perm-group-card" data-group-name="{{ strtolower($groupName) }}">
                <div class="perm-group-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 16px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 1px;">
                            {{ $groupName }}
                        </span>
                        <span style="background: #e2e8f0; color: #475569; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                            {{ count($permissions) }}
                        </span>
                    </div>
                </div>
                <div class="perm-group-body">
                    @foreach ($permissions as $p)
                        <div class="perm-pill" data-name="{{ strtolower($p->name) }}">
                            <span>{{ $p->name }}</span>
                            @if($p->roles_count > 0)
                                <span class="role-badge" title="Sử dụng bởi {{ $p->roles_count }} vai trò">{{ $p->roles_count }}</span>
                            @endif
                            
                            <div class="pill-actions">
                                <a href="{{ route('permissions.edit', $p->id) }}" class="action-btn edit" title="Sửa">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('permissions.destroy', $p->id) }}" method="POST" style="display: inline;" class="delete-form">
                                    @csrf
                                    <button type="button" class="action-btn delete delete-confirm" title="Xóa" data-message="Bạn có chắc chắn muốn xóa quyền '{{ $p->name }}'?" style="border: none; background: transparent; cursor: pointer; padding: 0;">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- No Results State -->
    <div id="noResults" style="display: none; text-align: center; padding: 100px 20px; background: white; border-radius: 12px; border: 2px dashed #e2e8f0;">
        <div style="background: #f8fafc; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <svg style="width: 32px; height: 32px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h3 style="color: #1e293b; font-weight: 700; margin-bottom: 8px;">Không tìm thấy quyền phù hợp</h3>
        <p style="color: #64748b;">Thử thay đổi từ khóa tìm kiếm của bạn xem sao.</p>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('permissionSearch');
            const groupCards = document.querySelectorAll('.perm-group-card');
            const noResults = document.getElementById('noResults');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let totalVisibleResults = 0;

                groupCards.forEach(card => {
                    const groupName = card.getAttribute('data-group-name');
                    const pills = card.querySelectorAll('.perm-pill');
                    let visiblePillsInGroup = 0;

                    pills.forEach(pill => {
                        const name = pill.getAttribute('data-name');
                        if (name.includes(query) || groupName.includes(query)) {
                            pill.style.display = 'inline-flex';
                            visiblePillsInGroup++;
                        } else {
                            pill.style.display = 'none';
                        }
                    });

                    if (visiblePillsInGroup > 0) {
                        card.style.display = 'block';
                        totalVisibleResults += visiblePillsInGroup;
                    } else {
                        card.style.display = 'none';
                    }
                });

                noResults.style.display = totalVisibleResults === 0 ? 'block' : 'none';
            });

            // Re-bind SweetAlert for delete
            document.querySelectorAll('.delete-confirm').forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    const message = this.getAttribute('data-message');
                    
                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Đồng ý, xóa!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0BAA4B',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@endpush
@endsection

