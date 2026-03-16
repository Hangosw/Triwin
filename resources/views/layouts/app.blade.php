<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vietnam Rubber Group HRM')</title>
    <link rel="icon" href="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background-color: white;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid #e5e7eb;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 24px;
            background-color: white;
            border-bottom: 1px solid #f3f4f6;
            text-align: center;
        }

        .sidebar-logo {
            width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: contain;
            margin: 0 auto;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 0;
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .sidebar-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .nav-item:hover {
            background-color: #f9fafb;
            color: #0BAA4B;
        }

        .nav-item.active {
            background-color: #f0fdf4;
            border-left-color: #0BAA4B;
            color: #0BAA4B;
        }

        .nav-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* Submenu Styles */
        .nav-item-parent {
            position: relative;
        }

        .nav-item-parent>.nav-item {
            justify-content: space-between;
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: #f9fafb;
        }

        .submenu.open {
            max-height: 500px;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px 10px 56px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-size: 14px;
            font-weight: 400;
        }

        .submenu-item:hover {
            background-color: #f0fdf4;
            color: #0BAA4B;
        }

        .submenu-item.active {
            background-color: #f0fdf4;
            border-left-color: #0BAA4B;
            color: #0BAA4B;
            font-weight: 500;
        }

        .chevron-icon {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
        }

        .chevron-icon.rotate {
            transform: rotate(180deg);
        }

        .sidebar-footer {
            padding: 24px;
            background-color: #f9fafb;
            border-top: 1px solid #f3f4f6;
            color: #1f2937;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0fdf4;
            color: #0BAA4B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background-color: #dc2626;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            justify-content: center;
        }

        .logout-btn:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 30px;
            font-weight: 700;
            color: #1f2937;
        }

        .page-header p {
            color: #6b7280;
            margin-top: 8px;
        }

        .content-wrapper {
            padding: 32px;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #0BAA4B;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0d7a3a;
        }

        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        /* Input Styles */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            padding: 16px 24px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
            cursor: pointer;
        }

        .table td {
            padding: 16px 24px;
            font-size: 14px;
        }

        /* Badge Styles */
        .badge {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-orange {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .badge-gray {
            background-color: #f3f4f6;
            color: #374151;
        }

        .badge-purple {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        /* Action Buttons */
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.2s;
            padding: 0;
        }

        .btn-icon:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .btn-icon svg {
            width: 20px;
            height: 20px;
        }

        /* Search Bar */
        .search-bar {
            position: relative;
            flex: 1;
            min-width: 300px;
        }

        .search-bar input {
            width: 100%;
            padding-left: 40px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Avatar */
        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #0BAA4B;
        }

        .stat-card .label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }

        /* Utility Classes */
        .text-primary {
            color: #0BAA4B;
        }

        .text-gray {
            color: #6b7280;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-medium {
            font-weight: 500;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-4 {
            gap: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* ========================================
           DATATABLES CUSTOM STYLING
           ======================================== */

        /* Wrapper Layout - Length menu left, Search right */
        .dataTables_wrapper {
            padding: 20px 0;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 16px;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 16px;
        }

        /* Length Menu Styling */
        .dataTables_wrapper .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 6px 32px 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 16px;
            appearance: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_length select:hover {
            border-color: #0BAA4B;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
        }

        /* Search Input Styling */
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 8px 12px 8px 40px !important;
            display: inline-block;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            width: 280px;
            transition: all 0.2s;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
            background-position: 12px center;
            background-repeat: no-repeat;
            background-size: 18px;
            background-color: white;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 6px 32px 6px 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            font-size: 14px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
            background-position: right 10px center !important;
            background-repeat: no-repeat !important;
            background-size: 12px !important;
            background-color: white !important;
            min-width: 60px;
        }

        .dataTables_wrapper .dataTables_filter input:hover {
            border-color: #0BAA4B;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
        }

        /* Info Text Styling */
        .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 16px;
            font-size: 14px;
            color: #6b7280;
        }

        /* Pagination Container */
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 12px;
        }

        /* Pagination Buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            margin: 0 2px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: white;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #f9fafb;
            border-color: #0BAA4B;
            color: #0BAA4B;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #0BAA4B !important;
            border-color: #0BAA4B !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(11, 170, 75, 0.2);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #0d7a3a !important;
            border-color: #0d7a3a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-weight: 600;
        }

        /* Table Header Styling */
        table.dataTable thead th {
            border-bottom: 2px solid #e5e7eb !important;
            background-color: #f9fafb;
            font-weight: 600;
            color: #1f2937;
            padding: 14px 16px;
        }

        table.dataTable thead th:hover {
            background-color: #f3f4f6;
        }

        /* Sorting Icons */
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 32px;
        }

        /* Table Body */
        table.dataTable tbody tr {
            transition: all 0.2s;
        }

        table.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }

        table.dataTable tbody td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        /* Processing Indicator */
        .dataTables_wrapper .dataTables_processing {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #374151;
        }

        /* Clear floats */
        .dataTables_wrapper::after {
            content: "";
            display: table;
            clear: both;
        }

        /* STT Column */
        .stt-checkbox-col {
            text-align: center;
            width: 60px;
        }

        .stt-text {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1f2937;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                text-align: left;
                margin-bottom: 12px;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
            }
        }
    </style>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}" alt="Logo" class="sidebar-logo"
                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27120%27 height=%27120%27%3E%3Ccircle cx=%2760%27 cy=%2760%27 r=%2750%27 fill=%27%230BAA4B%27/%3E%3Ctext x=%2760%27 y=%2770%27 font-size=%2724%27 fill=%27white%27 text-anchor=%27middle%27%3ETRIWIN%3C/text%3E%3C/svg%3E'">
            </div>
            <nav class="sidebar-nav">
                @php
                    $authUser = auth()->user();
                    $authNV = $authUser?->nhanVien;
                    $activeContract = $authNV ? ($authNV->hopDongs->where('TrangThai', 1)->first() ?? $authNV->hopDongs->first()) : null;
                @endphp
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Trang chủ</span>
                </a>

                @hasrole('Employee|Nhân viên')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('nhan-vien.info') || request()->routeIs('hop-dong.info') ? 'active' : '' }}"
                            onclick="toggleSubmenu('profile-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <span>Hồ sơ cá nhân</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="profile-submenu">
                            <a href="{{ $authNV ? route('nhan-vien.info', $authNV->id) : '#' }}"
                                class="submenu-item {{ request()->routeIs('nhan-vien.info') && request()->route('id') == ($authNV?->id ?? 0) ? 'active' : '' }}">
                                <span>Thông tin cá nhân</span>
                            </a>
                            <a href="{{ $activeContract ? route('hop-dong.info', $activeContract->id) : '#' }}"
                                class="submenu-item {{ request()->routeIs('hop-dong.info') && request()->route('id') == ($activeContract?->id ?? 0) ? 'active' : '' }}">
                                <span>Hợp đồng cá nhân</span>
                            </a>
                        </div>
                    </div>
                @endhasrole
                @can('Quản lý người dùng')
                    <a href="{{ route('nguoi-dung.danh-sach') }}"
                        class="nav-item {{ request()->routeIs('nguoi-dung.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Người dùng</span>
                    </a>
                @endcan

                @can('Quản lý tổ chức')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('don-vi.*') || request()->routeIs('phong-ban.*') || request()->routeIs('chuc-vu.*') || request()->routeIs('to-doi.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('don-vi-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Cơ cấu tổ chức</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="don-vi-submenu">

                            <a href="{{ route('phong-ban.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('phong-ban.*') ? 'active' : '' }}">
                                <span>Phòng ban</span>
                            </a>
                            <a href="{{ route('to-doi.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('to-doi.*') ? 'active' : '' }}">
                                <span>Tổ đội</span>
                            </a>
                            <a href="{{ route('chuc-vu.index') }}"
                                class="submenu-item {{ request()->routeIs('chuc-vu.*') ? 'active' : '' }}">
                                <span>Chức vụ</span>
                            </a>
                        </div>
                    </div>
                @endcan

                @unlessrole('Employee|Nhân viên')
                @can('Xem nhân viên')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('nhan-vien.*') || request()->routeIs('dieu-chuyen.*') || request()->routeIs('cong-tac.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('employees-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Nhân viên</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="employees-submenu">
                            <a href="{{ route('nhan-vien.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('nhan-vien.danh-sach') ? 'active' : '' }}">
                                <span>Danh sách nhân viên</span>
                            </a>
                            <a href="{{ route('dieu-chuyen.index') }}"
                                class="submenu-item {{ request()->routeIs('dieu-chuyen.*') ? 'active' : '' }}">
                                <span>Điều chuyển nội bộ</span>
                            </a>
                            @can('Xem công tác')
                                <a href="{{ route('cong-tac.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('cong-tac.*') ? 'active' : '' }}">
                                    <span>Công tác</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcan
                @endunlessrole

                @unlessrole('Employee|Nhân viên')
                @can('Xem hợp đồng')
                    <a href="{{ route('hop-dong.danh-sach') }}"
                        class="nav-item {{ request()->routeIs('hop-dong.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Hợp đồng</span>
                    </a>
                @endcan
                @endunlessrole

                @can('Xem chấm công')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('cham-cong.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('attendance-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Chấm công</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="attendance-submenu">
                            <a href="{{ route('cham-cong.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('cham-cong.danh-sach') ? 'active' : '' }}">
                                <span>Danh sách chấm công</span>
                            </a>
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('cham-cong.taoView') }}"
                                class="submenu-item {{ request()->routeIs('cham-cong.taoView') ? 'active' : '' }}">
                                <span>Chấm công (Admin)</span>
                            </a>
                            @endunlessrole
                            <a href="{{ route('cham-cong.ca-nhan') }}"
                                class="submenu-item {{ request()->routeIs('cham-cong.ca-nhan') ? 'active' : '' }}">
                                <span>Chấm công cá nhân</span>
                            </a>
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('cham-cong.schedule') }}"
                                class="submenu-item {{ request()->routeIs('cham-cong.schedule') ? 'active' : '' }}">
                                <span>Lịch làm việc</span>
                            </a>
                            @endunlessrole
                        </div>
                    </div>
                @endcan

                @can('Xem tăng ca nghỉ phép')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('tang-ca.*') || request()->routeIs('nghi-phep.*') || request()->routeIs('overtime-leave.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('overtime-leave-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Tăng ca & Nghỉ phép</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="overtime-leave-submenu">
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('tang-ca.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('tang-ca.danh-sach') ? 'active' : '' }}">
                                <span>Tăng ca (Admin)</span>
                            </a>
                            @endunlessrole
                            <a href="{{ route('tang-ca.ca-nhan') }}"
                                class="submenu-item {{ request()->routeIs('tang-ca.ca-nhan') ? 'active' : '' }}">
                                <span>Đăng ký tăng ca</span>
                            </a>
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('nghi-phep.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('nghi-phep.danh-sach') ? 'active' : '' }}">
                                <span>Nghỉ phép (Admin)</span>
                            </a>
                            @endunlessrole
                            <a href="{{ route('nghi-phep.ca-nhan') }}"
                                class="submenu-item {{ request()->routeIs('nghi-phep.ca-nhan') ? 'active' : '' }}">
                                <span>Đăng ký nghỉ phép</span>
                            </a>
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('nghi-phep.config') }}"
                                class="submenu-item {{ request()->routeIs('nghi-phep.config') ? 'active' : '' }}">
                                <span>Cấu hình nghỉ phép</span>
                            </a>
                            @endunlessrole
                        </div>
                    </div>
                @endcan

                @can('Xem lương')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('salary.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('salary-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Lương</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="salary-submenu">
                            <a href="{{ route('salary.index') }}"
                                class="submenu-item {{ request()->routeIs('salary.index') ? 'active' : '' }}">
                                <span>Danh sách lương</span>
                            </a>

                        </div>
                    </div>
                @endcan

                @can('Quản lý hệ thống')
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('roles-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Phân quyền</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="roles-submenu">
                            <a href="{{ route('roles.index') }}"
                                class="submenu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <span>Quản lý nhóm quyền</span>
                            </a>
                            <a href="{{ route('permissions.index') }}"
                                class="submenu-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                <span>Quản lý quyền</span>
                            </a>
                        </div>
                    </div>

                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('config.*') || request()->routeIs('lich-su.*') || request()->routeIs('settings.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('settings-main-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Cài đặt</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="settings-main-submenu">
                            <a href="{{ route('config.index') }}"
                                class="submenu-item {{ request()->routeIs('config.*') ? 'active' : '' }}">
                                <span>Cấu hình</span>
                            </a>
                            <a href="{{ route('lich-su.index') }}"
                                class="submenu-item {{ request()->routeIs('lich-su.*') ? 'active' : '' }}">
                                <span>Lịch sử hệ thống</span>
                            </a>
                        </div>
                    </div>
                @endcan
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        @php
                            $authUser = auth()->user();
                            $authNV = $authUser?->nhanVien;
                            $tenHienThi = $authNV?->Ten ?? $authUser?->TaiKhoan ?? 'Người dùng';
                            $chucVu = $authNV?->ttCongViec?->chucVu?->Ten ?? 'Quản trị viên';
                        @endphp
                        <div style="font-weight: 500; font-size: 14px;">{{ $tenHienThi }}</div>
                        <div style="font-size: 12px; color: #d1fae5;">{{ $chucVu }}</div>
                    </div>
                </div>
                <a href="#" class="nav-item"
                    style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); border-left: none;" onclick="event.preventDefault(); Swal.fire({
                        title: 'Xác nhận',
                        text: 'Bạn có chắc chắn muốn đăng xuất?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0BAA4B',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Đăng xuất',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('logout') }}';
                        }
                    })">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('mobile-open');
        }
        // Toggle submenu
        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            const chevron = event.currentTarget.querySelector('.chevron-icon');
            // Close all other submenus
            document.querySelectorAll('.submenu').forEach(menu => {
                if (menu.id !== submenuId) {
                    menu.classList.remove('open');
                }
            });
            // Close all other chevrons
            document.querySelectorAll('.chevron-icon').forEach(icon => {
                if (icon !== chevron) {
                    icon.classList.remove('rotate');
                }
            });
            // Toggle current submenu
            submenu.classList.toggle('open');
            chevron.classList.toggle('rotate');
        }
        // Auto-open submenu if current page is in submenu
        document.addEventListener('DOMContentLoaded', function () {
            // Check if any submenu item is active
            document.querySelectorAll('.submenu-item.active').forEach(item => {
                const submenu = item.closest('.submenu');
                if (submenu) {
                    submenu.classList.add('open');
                    const parent = submenu.closest('.nav-item-parent');
                    const chevron = parent.querySelector('.chevron-icon');
                    if (chevron) {
                        chevron.classList.add('rotate');
                    }
                }
            });
        });
        // Initialize Flatpickr for all date inputs globally
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.datepicker', {
                dateFormat: 'd/m/Y',        // Format: DD/MM/YYYY
                allowInput: true,           // Cho phép nhập tay
                disableMobile: true         // Không dùng native mobile picker
            });
        });

        // Global Select2 Initialization
        $(document).ready(function () {
            function initSelect2(container) {
                $(container).find('select:not(.no-select2):not(.select2-hidden-accessible):not(.flatpickr-monthDropdown-months)').each(function () {
                    const dropdownParent = $(this).closest('.modal').length ? $(this).closest('.modal') : null;
                    $(this).select2({
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Chọn một mục',
                        allowClear: true,
                        dropdownParent: dropdownParent
                    });
                });
            }

            // Initial call
            initSelect2(document);

            // Re-initialize for dynamic content (like Bootstrap modals)
            $(document).on('shown.bs.modal', function (e) {
                initSelect2(e.target);
            });
        });
    </script>
</body>

</html>