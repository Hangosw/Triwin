@extends('layouts.app')

@push('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0BAA4B'
            });
        @endif
    </script>
@endpush

@section('title', 'User Management - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
    <style>
        .user-name-link {
            font-weight: 500;
            color: #0BAA4B;
            text-decoration: none;
        }

        .user-name-link:hover {
            text-decoration: underline;
            color: #09933f;
        }

        .text-not-updated {
            color: #9ca3af;
            font-style: italic;
            font-size: 13px;
        }

        body.dark-theme .text-not-updated {
            color: #8b93a8;
        }

        #usersTable tbody tr {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        body.dark-theme #usersTable tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        #usersTable tbody tr:hover {
            background-color: rgba(11, 170, 75, 0.05) !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1>User Management</h1>
        <p>List of all users in the system</p>
    </div>

    <!-- Actions Bar -->
    <div class="card filter-bar-container">
        <div class="action-bar d-flex justify-content-between align-items-center flex-wrap gap-3" style="padding: 16px 24px;">
            <div class="filter-group d-flex align-items-end flex-wrap gap-2">
                {{-- Trạng thái --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 12px; margin-bottom: 4px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Status</label>
                    <div class="dropdown custom-filter-dropdown" data-default="All Statuses">
                        <input type="hidden" name="trang_thai" id="filterTrangThai" value="">
                        <div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="dropdown-text" style="color: #6c757d;">All Statuses</span>
                            <span class="dropdown-icon">
                                <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 14px;"></i>
                            </span>
                        </div>
                        <div class="dropdown-menu p-2 shadow"
                            style="min-width: 220px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="mb-2 text-center pb-2" style="border-bottom: 1px solid #e5e7eb;">
                                <span class="fw-bold" style="font-size: 13px; color: #4b5563;">SELECT STATUS</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 4px;">
                                <button type="button" class="btn btn-sm btn-primary filter-btn fw-bold shadow-sm"
                                    data-val="" data-label="All Statuses"
                                    onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="background-color: #3b82f6; color: #fff; text-align: left;">All Statuses</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="1"
                                    data-label="Active" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Active</button>
                                <button type="button" class="btn btn-sm btn-light filter-btn" data-val="0"
                                    data-label="Inactive" onclick="applyFilterAJAX('filterTrangThai', this)"
                                    style="text-align: left;">Inactive</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="action-buttons">
                <button id="btnDeleteSelected" class="btn btn-danger"
                    style="display: none; background-color: #dc2626; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Selected (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('nguoi-dung.tao') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">
                            <div style="text-align: center;">
                                <div><strong>#</strong></div>
                                <div style="margin-top: 4px;">
                                    <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                </div>
                            </div>
                        </th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let selectedIds = [];

            const table = $('#usersTable').DataTable({
                processing: true,
                serverSide: false, // Client-side logic as per NguoiDungController@DataNguoiDung
                ajax: "{{ route('nguoi-dung.data') }}",
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return `
                                    <div style="text-align: center;">
                                        <div><strong class="stt-value"></strong></div>
                                        <div style="margin-top: 4px;">
                                            <input type="checkbox" class="user-checkbox" value="${row.id}" style="cursor: pointer;" ${selectedIds.includes(row.id) ? 'checked' : ''}>
                                        </div>
                                    </div>
                                `;
                        }
                    },
                    {
                        data: 'Ten',
                        render: function (data, type, row) {
                            if (!data) return '<span class="text-not-updated">Not updated</span>';
                            return `<span class="user-name-link">${data}</span>`;
                        }
                    },
                    { data: 'TaiKhoan' },
                    { data: 'Email' },
                    { data: 'SoDienThoai', render: function (data) { return data || '--'; } },
                    {
                        data: 'TrangThai',
                        render: function (data) {
                            if (data == 1) {
                                return '<span class="badge badge-success">Active</span>';
                            }
                            return '<span class="badge badge-gray">Inactive</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function (data, type, row) {
                            const currentUserId = {{ \Illuminate\Support\Facades\Auth::id() }};
                            const isSelf = row.id == currentUserId;

                            const statusIcon = row.TrangThai == 1
                                ? `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>`
                                : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>`;

                            const statusTitle = row.TrangThai == 1 ? 'Lock user' : 'Unlock user';
                            const statusClass = row.TrangThai == 1 ? 'text-warning' : 'text-success';

                            return `
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <button type="button" class="btn-icon ${statusClass} btn-toggle-status" data-id="${row.id}" title="${statusTitle}" style="background: none; border: none; cursor: pointer;">
                                            ${statusIcon}
                                        </button>
                                        ${!isSelf ? `
                                        <button type="button" class="btn-icon text-danger btn-delete" data-id="${row.id}" title="Delete" style="background: none; border: none; cursor: pointer; color: #dc2626;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                        ` : `
                                        <span class="badge badge-info" style="font-size: 11px;">You</span>
                                        `}
                                    </div>
                                `;
                        }
                    }
                ],
                
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: [0], responsivePriority: 1 }, // STT + Checkbox
                    { targets: 1, responsivePriority: 2 }, // Full Name
                    { targets: 2, responsivePriority: 4 }, // Username
                    { targets: 3, responsivePriority: 5 }, // Email
                    { targets: 4, responsivePriority: 6 }, // Phone Number
                    { targets: 5, responsivePriority: 7 }, // Status
                    { targets: 6, responsivePriority: 3 }  // Actions
                ],
                order: [], // Respect server order (latest ID)
            });

            // Row click navigation
            $('#usersTable tbody').on('click', 'tr', function (e) {
                // Don't trigger if clicking on checkbox, action button, or selection-related elements
                if ($(e.target).closest('.btn-icon, .user-checkbox, #selectAll, .dtr-control').length) {
                    return;
                }

                const data = table.row(this).data();
                if (data && data.id) {
                    window.location.href = `/nguoi-dung/sua/${data.id}`;
                }
            });

            // Keep STT sequential regardless of sorting
            table.on('order.dt search.dt', function () {
                let info = table.page.info();
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, index) {
                    $(cell).find('.stt-value').html(index + 1 + info.start);
                });
            }).draw();

            // Custom filtering logic for Trang Thai
            window.applyFilterAJAX = function (inputId, btnEl) {
                const val = btnEl.dataset.val;
                const label = btnEl.dataset.label;
                const dropdown = $(btnEl).closest('.custom-filter-dropdown');

                const input = document.getElementById(inputId);
                input.value = val;
                table.draw();

                const textSpan = dropdown.find('.dropdown-text');
                textSpan.text(label);

                const iconSpan = dropdown.find('.dropdown-icon');
                const isDefault = (val === '');
                const isDark = $('body').hasClass('dark-theme');
                const activeColor = isDark ? '#e8eaf0' : '#212529';
                const mutedColor = isDark ? '#8b93a8' : '#6c757d';

                if (isDefault) {
                    textSpan.css('color', mutedColor);
                    iconSpan.html('<i class="bi bi-chevron-down ms-2" style="font-size: 14px; color: ' + mutedColor + ';"></i>');
                } else {
                    textSpan.css('color', activeColor);
                    const closeIconColor = isDark ? '#f87171' : '#dc2626';
                    iconSpan.html('<i class="bi bi-x-circle-fill ms-2" style="font-size: 12px; padding: 4px; border-radius: 50%; color: ' + mutedColor + ';" onclick="event.stopPropagation(); resetFilterAJAX(\'' + inputId + '\');" onmouseover="this.style.color=\'' + closeIconColor + '\'" onmouseout="this.style.color=\'' + mutedColor + '\'"></i>');
                }

                dropdown.find('.filter-btn').each(function () {
                    const b = $(this);
                    const bVal = b.data('val');
                    b.removeClass('btn-primary fw-bold shadow-sm').addClass('btn-light').css({
                        'background-color': isDark ? '#2e3349' : '#f9fafb',
                        'color': isDark ? '#c3c8da' : '#374151'
                    });
                    if (String(bVal) === String(val)) {
                        b.removeClass('btn-light').addClass('btn-primary fw-bold shadow-sm').css({
                            'background-color': '#3b82f6',
                            'color': '#fff'
                        });
                    }
                });
            };

            window.resetFilterAJAX = function (inputId) {
                const dropdown = $('#' + inputId).closest('.custom-filter-dropdown');
                const defaultBtn = dropdown.find(`.filter-btn[data-val=""]`);
                if (defaultBtn.length) {
                    applyFilterAJAX(inputId, defaultBtn[0]);
                }
            };



            // Custom search logic for DataTables trang_thai if client-side
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    if (settings.nTable.id !== 'usersTable') return true;

                    const filterStatus = $('#filterTrangThai').val();
                    if (filterStatus === '') return true;

                    const rowData = table.row(dataIndex).data();
                    return String(rowData.TrangThai) === String(filterStatus);
                }
            );

            // Select All Logic
            $('#selectAll').on('change', function () {
                const isChecked = this.checked;
                if (isChecked) {
                    // Add all IDs from the entire dataset
                    selectedIds = table.rows().data().toArray().map(row => row.id);
                } else {
                    selectedIds = [];
                }

                // Update checkboxes in current view
                $('.user-checkbox').prop('checked', isChecked);
                updateDeleteButton();
            });

            $(document).on('change', '.user-checkbox', function () {
                const id = parseInt($(this).val());
                if (this.checked) {
                    if (!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(itemId => itemId !== id);
                }

                const allData = table.rows().data().toArray();
                const allChecked = selectedIds.length === allData.length && allData.length > 0;
                $('#selectAll').prop('checked', allChecked);
                updateDeleteButton();
            });

            function updateDeleteButton() {
                const selectedCount = selectedIds.length;
                if (selectedCount > 0) {
                    $('#btnDeleteSelected').show();
                    $('#selectedCount').text(selectedCount);
                } else {
                    $('#btnDeleteSelected').hide();
                }
            }

            // Single Delete
            $(document).on('click', '.btn-delete', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm deletion?',
                    text: "Data cannot be recovered!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/nguoi-dung/xoa/${id}`, {
                            _token: '{{ csrf_token() }}'
                        }, function (res) {
                            if (res.success) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', res.message, 'success');
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        }).fail(function (xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while deleting user.';
                            Swal.fire('Failed!', msg, 'error');
                        });
                    }
                });
            });

            // Bulk Delete
            $('#btnDeleteSelected').on('click', function () {
                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Delete selected items?',
                    text: `You have selected ${selectedIds.length} users for deletion.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Delete now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('nguoi-dung.xoa-nhieu') }}", {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        }, function (res) {
                            if (res.success) {
                                table.ajax.reload();
                                selectedIds = [];
                                $('#selectAll').prop('checked', false);
                                updateDeleteButton();
                                Swal.fire('Success!', res.message, 'success');
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        }).fail(function (xhr) {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while deleting selected users.';
                            Swal.fire('Failed!', msg, 'error');
                        });
                    }
                });
            });

            // Toggle Status
            $(document).on('click', '.btn-toggle-status', function () {
                const id = $(this).data('id');
                const btn = $(this);

                $.post(`/nguoi-dung/toggle-status/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function (res) {
                    if (res.success) {
                        table.ajax.reload(null, false); // Reload without resetting paging
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    }
                });
            });
        });
    </script>
@endpush