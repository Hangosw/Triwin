<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\SystemConfig::getValue('company_name') . ' HRM')</title>
    <link rel="icon" href="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-main: #f9fafb;
            --bg-card: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --accent-color: #0BAA4B;
            --accent-hover: #0d7a3a;
            --swal-bg: #ffffff;
            --swal-text: #1f2937;
        }

        body.dark-theme {
            --bg-main: #0f1117;
            --bg-card: #1a1d27;
            --bg-sidebar: #13161f;
            --text-primary: #e8eaf0;
            --text-secondary: #8b93a8;
            --border-color: #2e3349;
            --swal-bg: #1a1d27;
            --swal-text: #e8eaf0;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }

        .app-container {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            height: 64px;
            background: white;
            padding: 0 16px;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #4b5563;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
        }

        .mobile-toggle:hover {
            background-color: #f3f4f6;
        }

        .mobile-toggle svg {
            width: 24px;
            height: 24px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 90;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
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
            background-color: var(--bg-sidebar);
            border-top: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .theme-toggle-container {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .theme-toggle-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            margin-bottom: 8px;
            display: block;
            font-weight: 600;
        }

        .theme-select {
            width: 100%;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 13px;
            cursor: pointer;
            outline: none;
            transition: all 0.2s;
        }

        .theme-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(11, 170, 75, 0.1);
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
            overflow-x: hidden;
            height: 100%;
            overscroll-behavior-y: contain;
            position: relative;
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
            background-color: #f3f4f6;
            color: #111827;
            border-color: #9ca3af;
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
        @media (max-width: 1024px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                z-index: 100;
                transition: left 0.3s ease;
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .mobile-header,
            .mobile-toggle {
                display: flex;
            }

            .main-content {
                margin-top: 64px;
            }

            .content-wrapper {
                padding: 16px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .action-buttons {
                flex-direction: row;
                flex-wrap: nowrap;
                justify-content: space-between;
                width: 100%;
                gap: 8px;
            }

            .btn {
                flex: 1;
                min-width: 0;
                padding: 10px !important;
                justify-content: center;
                gap: 0 !important;
                font-size: 0 !important;
            }

            .btn svg,
            .btn i {
                margin: 0 !important;
                width: 20px !important;
                height: 20px !important;
                font-size: 18px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .btn-secondary {
                width: auto !important;
            }

            .card {
                padding: 12px !important;
            }

            table.dataTable {
                width: 100% !important;
                margin: 0 !important;
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

        table.dataTable {
            width: 100% !important;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: visible;
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

        /* ========================================
           GLOBAL SELECT2 STYLING
           Matches form-control look & fixes clear
           icon vertical alignment everywhere.
           ======================================== */

        /* --- Single selection box ---
           align-items: stretch → rendered child fills full 42px height
           so the inner flex can truly center × and text. */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 0 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: stretch !important;
            /* KEY: stretch, not center */
            background-color: white !important;
            position: relative !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
            box-sizing: border-box !important;
        }

        /* Rendered text area — fills full 42px then centers content inside */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            display: flex !important;
            align-items: center !important;
            /* centers × and text within 42px */
            flex: 1 !important;
            min-width: 0 !important;
            padding: 0 28px 0 0 !important;
            /* right-pad for arrow */
            line-height: 1 !important;
            color: #374151 !important;
            font-size: 14px !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            text-overflow: ellipsis !important;
        }

        /* Clear (×) icon — in-flow flex child, perfectly centered in circle */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            float: none !important;
            position: static !important;
            order: -1 !important;
            flex-shrink: 0 !important;
            margin: 0 8px 0 0 !important;
            padding: 0 !important;
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            min-height: 18px !important;
            line-height: 18px !important;
            /* match height → centers baseline */
            font-size: 13px !important;
            /* smaller → breathing room in 18px circle */
            font-weight: 900 !important;
            font-family: Arial, sans-serif !important;
            /* more predictable glyph metrics */
            text-align: center !important;
            vertical-align: middle !important;
            color: #ef4444 !important;
            cursor: pointer !important;
            border-radius: 50% !important;
            transition: background-color 0.15s !important;
        }

        /* Select2 v4+ wraps × in an inner <span aria-hidden> — reset it */
        .select2-container--default .select2-selection--single .select2-selection__clear span {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            line-height: 18px !important;
            text-align: center !important;
            font-size: 13px !important;
            font-weight: 900 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            background-color: rgba(239, 68, 68, 0.12) !important;
            color: #dc2626 !important;
        }

        /* Arrow — absolute so it never compresses the flex row */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute !important;
            top: 50% !important;
            right: 10px !important;
            transform: translateY(-50%) !important;
            height: auto !important;
            width: 20px !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Focus ring */
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1) !important;
            outline: none !important;
        }

        /* Dropdown panel */
        .select2-dropdown {
            background-color: white !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }

        /* Search field inside dropdown */
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding: 7px 10px !important;
            font-size: 14px !important;
            color: #374151 !important;
            background-color: #f9fafb !important;
            outline: none !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 2px rgba(11, 170, 75, 0.1) !important;
        }

        /* Option items */
        .select2-container--default .select2-results__option {
            padding: 8px 12px !important;
            font-size: 14px !important;
            color: #374151 !important;
            background-color: white !important;
            transition: background-color 0.15s !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f0fdf4 !important;
            color: #0BAA4B !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0BAA4B !important;
            color: white !important;
        }

        /* --- Multiple selection --- */
        .select2-container--default .select2-selection--multiple {
            min-height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background-color: white !important;
            padding: 4px 8px !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin: 2px 4px 2px 0 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            background-color: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
            color: #065f46 !important;
            font-size: 13px !important;
        }

        /* Clear icon inside multi-choice tag */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            float: none !important;
            color: #ef4444 !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            line-height: 1 !important;
            margin-right: 4px !important;
            cursor: pointer !important;
        }

        /* Placeholder */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        /* ========================================
           DARK THEME — Modern Deep Dark
           Background : #0f1117  (near-black)
           Surface L1 : #1a1d27  (cards, sidebar)
           Surface L2 : #21263a  (table header, modal header/footer)
           Border     : #2e3349
           Text prim  : #e8eaf0
           Text sec   : #8b93a8
           Accent     : #0BAA4B / hover #09933f
           ======================================== */
        body.dark-theme {
            background-color: #0f1117;
            color: #e8eaf0;
        }

        body.dark-theme .app-container {
            background-color: #0f1117;
        }

        /* Cards & main surfaces */
        body.dark-theme .card,
        body.dark-theme .stat-card,
        body.dark-theme .dataTables_wrapper .dataTables_processing {
            background-color: #1a1d27;
            border-color: #2e3349;
            color: #e8eaf0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
        }

        /* Sidebar & mobile header */
        body.dark-theme .sidebar,
        body.dark-theme .sidebar-header,
        body.dark-theme .sidebar-footer,
        body.dark-theme .mobile-header {
            background-color: #13161f;
            border-color: #2e3349;
            color: #e8eaf0;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.4);
        }

        /* Page text */
        body.dark-theme .page-header h1,
        body.dark-theme .page-header p,
        body.dark-theme .stat-card .value,
        body.dark-theme .stat-card .label,
        body.dark-theme .form-label,
        body.dark-theme .text-gray {
            color: #e8eaf0;
        }

        /* Navigation items */
        body.dark-theme .nav-item {
            color: #8b93a8;
            border-radius: 8px;
            transition: all 0.18s ease;
        }

        body.dark-theme .nav-item:hover {
            background-color: rgba(11, 170, 75, 0.1);
            color: #0BAA4B;
        }

        body.dark-theme .nav-item.active {
            background-color: rgba(11, 170, 75, 0.15);
            color: #0BAA4B;
            border-left-color: #0BAA4B;
        }

        /* Submenu */
        body.dark-theme .submenu {
            background-color: #0f1117;
        }

        body.dark-theme .submenu-item {
            color: #6b7492;
        }

        body.dark-theme .submenu-item:hover {
            background-color: rgba(11, 170, 75, 0.08);
            color: #0BAA4B;
        }

        body.dark-theme .submenu-item.active {
            background-color: rgba(11, 170, 75, 0.12);
            color: #0BAA4B;
        }

        /* Tables */
        body.dark-theme .table thead,
        body.dark-theme table.dataTable thead,
        body.dark-theme .table thead th,
        body.dark-theme table.dataTable thead th {
            background-color: #21263a;
            color: #c3c8da;
            border-color: #2e3349 !important;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        body.dark-theme .table tbody tr,
        body.dark-theme table.dataTable tbody tr {
            background-color: #1a1d27;
            color: #e8eaf0;
            border-color: #2e3349 !important;
            transition: background 0.15s;
        }

        body.dark-theme .table tbody tr:hover,
        body.dark-theme table.dataTable tbody tr:hover {
            background-color: #21263a;
        }

        body.dark-theme .table td,
        body.dark-theme table.dataTable tbody td {
            border-color: #2e3349 !important;
        }

        /* Form controls & DataTable inputs */
        body.dark-theme .dataTables_wrapper .dataTables_length select,
        body.dark-theme .dataTables_wrapper .dataTables_filter input,
        body.dark-theme .form-control,
        body.dark-theme .form-select {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        body.dark-theme .dataTables_wrapper .dataTables_length select:focus,
        body.dark-theme .dataTables_wrapper .dataTables_filter input:focus,
        body.dark-theme .form-control:focus {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.15) !important;
        }

        /* Fix native date/time picker icon visibility in dark mode */
        body.dark-theme input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        body.dark-theme .dataTables_wrapper .dataTables_length label,
        body.dark-theme .dataTables_wrapper .dataTables_filter label {
            color: #8b93a8;
        }

        /* Buttons */
        body.dark-theme .btn-secondary {
            background-color: #21263a;
            border-color: #2e3349;
            color: #c3c8da;
        }

        body.dark-theme .btn-secondary:hover {
            background-color: #2e3349;
            color: #e8eaf0;
        }

        /* Pagination */
        body.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #21263a;
            border-color: #2e3349;
            color: #c3c8da !important;
            border-radius: 6px;
            transition: all 0.15s;
        }

        body.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #2e3349;
            border-color: #0BAA4B;
            color: #0BAA4B !important;
        }

        body.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #0BAA4B !important;
            border-color: #0BAA4B !important;
            color: #fff !important;
        }

        /* DataTables info text */
        body.dark-theme .dataTables_wrapper .dataTables_info {
            color: #6b7492;
        }

        /* Misc text */
        body.dark-theme .stt-text {
            color: #c3c8da;
        }

        body.dark-theme .action-bar {
            color: #e8eaf0;
        }

        /* ========================================
           DARK MODE — Select2 Complete Override
           ======================================== */

        /* Single selection box */
        body.dark-theme .select2-container--default .select2-selection--single {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .select2-container--default.select2-container--focus .select2-selection--single,
        body.dark-theme .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.15) !important;
        }

        /* Rendered text */
        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e8eaf0 !important;
        }

        /* Placeholder text */
        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6b7492 !important;
        }

        /* Clear (×) icon in dark */
        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__clear {
            color: #f87171 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            background-color: rgba(248, 113, 113, 0.15) !important;
            color: #ef4444 !important;
        }

        /* Arrow icon */
        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #8b93a8 transparent transparent transparent !important;
        }

        body.dark-theme .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #8b93a8 transparent !important;
        }

        /* Multiple selection box */
        body.dark-theme .select2-container--default .select2-selection--multiple {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #2e3349 !important;
            border-color: #3d445e !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #f87171 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444 !important;
        }

        /* Dropdown panel */
        body.dark-theme .select2-dropdown {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
        }

        /* Search field inside dropdown */
        body.dark-theme .select2-search--dropdown .select2-search__field {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .select2-search--dropdown .select2-search__field:focus {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 2px rgba(11, 170, 75, 0.15) !important;
        }

        body.dark-theme .select2-search--dropdown .select2-search__field::placeholder {
            color: #6b7492 !important;
        }

        /* Option items — default state (must override the light-mode white bg) */
        body.dark-theme .select2-container--default .select2-results__option {
            background-color: #1a1d27 !important;
            color: #c3c8da !important;
        }

        /* Option — hover (non-highlighted) */
        body.dark-theme .select2-container--default .select2-results__option[aria-selected]:not([aria-selected=true]):hover {
            background-color: #21263a !important;
            color: #e8eaf0 !important;
        }

        /* Option — already-selected item */
        body.dark-theme .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #21263a !important;
            color: #0BAA4B !important;
        }

        /* Option — keyboard/mouse highlighted */
        body.dark-theme .select2-container--default .select2-results__option--highlighted,
        body.dark-theme .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0BAA4B !important;
            color: #ffffff !important;
        }

        /* Options group label */
        body.dark-theme .select2-container--default .select2-results__group {
            background-color: #21263a !important;
            color: #8b93a8 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
        }

        /* Results container scrollbar track */
        body.dark-theme .select2-results {
            background-color: #1a1d27 !important;
        }

        /* --- Global Clear Filter Button --- */
        .btn-clear-filter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 500;
            gap: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            background-color: #fff1f2;
            border: 1px solid #fecaca;
            color: #e11d48;
            cursor: pointer;
        }

        .btn-clear-filter:hover {
            background-color: #ffe4e6;
            border-color: #fb7185;
            color: #be123c;
            text-decoration: none;
        }

        .btn-clear-filter svg {
            width: 14px;
            height: 14px;
        }

        /* Clear Filter Button - Dark Theme */
        body.dark-theme .btn-clear-filter {
            background-color: rgba(225, 29, 72, 0.1);
            border-color: rgba(225, 29, 72, 0.3);
            color: #fb7185;
        }

        body.dark-theme .btn-clear-filter:hover {
            background-color: rgba(225, 29, 72, 0.2);
            border-color: #fb7185;
            color: #fff;
        }

        /* Modals */
        body.dark-theme .modal-content {
            background-color: #1a1d27;
            border-color: #2e3349;
            color: #e8eaf0;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.7);
        }

        body.dark-theme .modal-header {
            background-color: #21263a;
            border-color: #2e3349;
        }

        body.dark-theme .modal-footer {
            background-color: #21263a;
            border-color: #2e3349;
        }

        body.dark-theme .close {
            color: #8b93a8;
            opacity: 0.8;
        }

        body.dark-theme .close:hover {
            color: #e8eaf0;
            opacity: 1;
        }

        /* Badges & misc */
        body.dark-theme .badge {
            opacity: 0.9;
        }

        body.dark-theme hr {
            border-color: #2e3349;
        }

        /* Disable Select2 in restricted areas */
        .no-select2-parent .select2-container,
        .dataTables_length .select2-container,
        .swal2-container .select2-container,
        .swal2-container select {
            display: none !important;
        }

        /* ========================================
           DARK MODE — Bootstrap 5 Table Override
           Overrides --bs-table-* CSS variables so
           ALL table variants work in dark theme.
           ======================================== */

        /* Override Bootstrap 5 CSS custom properties for .table */
        body.dark-theme .table {
            --bs-table-bg: #1a1d27;
            --bs-table-color: #e8eaf0;
            --bs-table-border-color: #2e3349;
            --bs-table-striped-bg: #1e2231;
            --bs-table-striped-color: #e8eaf0;
            --bs-table-active-bg: #21263a;
            --bs-table-active-color: #e8eaf0;
            --bs-table-hover-bg: #21263a;
            --bs-table-hover-color: #e8eaf0;
            color: #e8eaf0;
            border-color: #2e3349;
        }

        /* thead for Bootstrap tables */
        body.dark-theme .table>thead {
            background-color: #21263a !important;
            color: #c3c8da !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .table>thead>tr>th,
        body.dark-theme .table>thead>tr>td {
            background-color: #21263a !important;
            color: #c3c8da !important;
            border-color: #2e3349 !important;
            font-weight: 600;
        }

        /* tbody for Bootstrap tables */
        body.dark-theme .table>tbody>tr>td,
        body.dark-theme .table>tbody>tr>th {
            background-color: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .table>tbody>tr:hover>td,
        body.dark-theme .table>tbody>tr:hover>th {
            background-color: #21263a !important;
            color: #e8eaf0 !important;
        }

        /* tfoot for Bootstrap tables */
        body.dark-theme .table>tfoot>tr>td,
        body.dark-theme .table>tfoot>tr>th {
            background-color: #21263a !important;
            color: #c3c8da !important;
            border-color: #2e3349 !important;
        }

        /* table-bordered variant */
        body.dark-theme .table-bordered> :not(caption)>* {
            border-color: #2e3349 !important;
        }

        body.dark-theme .table-bordered> :not(caption)>*>* {
            border-color: #2e3349 !important;
        }

        /* table-hover variant (re-override hover variables) */
        body.dark-theme .table-hover>tbody>tr:hover>* {
            --bs-table-accent-bg: #21263a;
            background-color: #21263a !important;
            color: #e8eaf0 !important;
        }

        /* table-striped variant */
        body.dark-theme .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: #1e2231;
            background-color: #1e2231 !important;
            color: #e8eaf0 !important;
        }

        /* table-sm variant */
        body.dark-theme .table-sm> :not(caption)>*>* {
            background-color: inherit;
            color: inherit;
            border-color: #2e3349 !important;
        }

        /* Plain <table> elements (no .table class) inside cards/content */
        body.dark-theme .card table:not(.dataTable),
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) {
            width: 100%;
            border-collapse: collapse;
            color: #e8eaf0;
        }

        body.dark-theme .card table:not(.dataTable) thead tr,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) thead tr {
            background-color: #21263a !important;
        }

        body.dark-theme .card table:not(.dataTable) thead th,
        body.dark-theme .card table:not(.dataTable) thead td,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) thead th,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) thead td {
            background-color: #21263a !important;
            color: #c3c8da !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .card table:not(.dataTable) tbody td,
        body.dark-theme .card table:not(.dataTable) tbody th,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) tbody td,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) tbody th {
            background-color: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .card table:not(.dataTable) tbody tr:hover td,
        body.dark-theme .card table:not(.dataTable) tbody tr:hover th,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) tbody tr:hover td,
        body.dark-theme .content-wrapper table:not(.dataTable):not(.table) tbody tr:hover th {
            background-color: #21263a !important;
        }

        /* table-wrapper / table-responsive container in dark mode */
        body.dark-theme .table-responsive {
            border-color: #2e3349;
        }

        /* Fix Bootstrap table caption */
        body.dark-theme .table>caption {
            color: #8b93a8;
        }

        /* Fix any inline background-color: white on td/th */
        body.dark-theme .table td[style*="background-color: white"],
        body.dark-theme .table td[style*="background-color:#fff"],
        body.dark-theme .table td[style*="background:#fff"],
        body.dark-theme .table th[style*="background-color: white"],
        body.dark-theme .table th[style*="background-color:#fff"],
        body.dark-theme .table th[style*="background:#fff"] {
            background-color: #1a1d27 !important;
            color: #e8eaf0 !important;
        }

        /* Modal tables */
        body.dark-theme .modal-body .table>thead>tr>th,
        body.dark-theme .modal-body .table>thead>tr>td {
            background-color: #2a2f45 !important;
            color: #c3c8da !important;
            border-color: #3d445e !important;
        }

        body.dark-theme .modal-body .table>tbody>tr>td,
        body.dark-theme .modal-body .table>tbody>tr>th {
            background-color: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #3d445e !important;
        }

        body.dark-theme .modal-body .table>tbody>tr:hover>td,
        body.dark-theme .modal-body .table>tbody>tr:hover>th {
            background-color: #21263a !important;
        }

        /* Bootstrap table inside .card - no extra background bleed */
        body.dark-theme .card .table-responsive,
        body.dark-theme .card .table-container {
            background-color: #1a1d27;
        }

        /* ========================================
           DARK MODE — Global Sweep
           Catches ALL common white-background
           elements that can't be handled via class
           overrides alone (inline styles, local
           class redeclarations, etc.)
           ======================================== */

        /* --- Inline style catcher: background: white / #fff / #ffffff --- */
        body.dark-theme [style*="background: white"],
        body.dark-theme [style*="background:white"],
        body.dark-theme [style*="background-color: white"],
        body.dark-theme [style*="background-color:white"] {
            background-color: #1a1d27 !important;
            background: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme [style*="background: #fff"],
        body.dark-theme [style*="background:#fff"],
        body.dark-theme [style*="background-color: #fff"],
        body.dark-theme [style*="background-color:#fff"],
        body.dark-theme [style*="background: #ffffff"],
        body.dark-theme [style*="background:#ffffff"],
        body.dark-theme [style*="background-color: #ffffff"],
        body.dark-theme [style*="background-color:#ffffff"] {
            background-color: #1a1d27 !important;
            background: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        /* --- Light background colors that should flip in dark mode --- */
        body.dark-theme [style*="background: #f8fafc"],
        body.dark-theme [style*="background-color: #f8fafc"],
        body.dark-theme [style*="background: #f9fafb"],
        body.dark-theme [style*="background-color: #f9fafb"],
        body.dark-theme [style*="background: #f3f4f6"],
        body.dark-theme [style*="background-color: #f3f4f6"],
        body.dark-theme [style*="background: #f1f5f9"],
        body.dark-theme [style*="background-color: #f1f5f9"],
        body.dark-theme [style*="background: #f8f9fa"],
        body.dark-theme [style*="background-color: #f8f9fa"] {
            background-color: #21263a !important;
            background: #21263a !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        /* --- Specific named components in dark mode --- */

        /* Clock container (attendance/self) */
        body.dark-theme .clock-container {
            background: #1a1d27 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4) !important;
            border: 1px solid #2e3349;
        }

        body.dark-theme #live-clock {
            color: #e8eaf0 !important;
        }

        body.dark-theme #live-date {
            color: #8b93a8 !important;
        }

        /* User card (attendance self) */
        body.dark-theme .user-card {
            background: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .user-details h2 {
            color: #e8eaf0 !important;
        }

        body.dark-theme .user-details p {
            color: #8b93a8 !important;
        }

        /* Recent activity (attendance self) */
        body.dark-theme .recent-activity {
            background: #1a1d27 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
        }

        /* Contract cards (employee tab_contracts) */
        body.dark-theme .contract-card {
            background: #1a1d27 !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .contract-card:hover {
            border-color: #0BAA4B !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
        }

        /* Local .card redeclaration (ngach-luong, etc.) */
        body.dark-theme .card {
            background: #1a1d27 !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        /* Fix browser default indicators (clock/calendar) in dark mode */
        body.dark-theme {
            color-scheme: dark;
        }

        body.dark-theme input::-webkit-calendar-picker-indicator {
            filter: invert(1) brightness(1.5) contrast(1.5) !important;
            cursor: pointer;
        }

        /* Support for other browser variants of the indicator */
        body.dark-theme input::-webkit-inner-spin-button,
        body.dark-theme input::-webkit-clear-button {
            filter: invert(1) brightness(1.5) !important;
        }

        /* ========================================
           DARK MODE — SweetAlert2 Global
           ======================================== */
        body.dark-theme .swal2-popup {
            background-color: var(--bg-card) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.7);
        }

        body.dark-theme .swal2-title,
        body.dark-theme .swal2-html-container,
        body.dark-theme .swal2-content {
            color: var(--text-primary) !important;
        }

        body.dark-theme .swal2-confirm {
            background-color: var(--accent-color) !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.25) !important;
        }

        body.dark-theme .swal2-cancel {
            background-color: var(--bg-main) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
        }

        /* Override backgrounds in icons (success/error circles) */
        body.dark-theme .swal2-success-circular-line-left,
        body.dark-theme .swal2-success-circular-line-right,
        body.dark-theme .swal2-success-fix,
        body.dark-theme .swal2-timer-progress-bar {
            background-color: var(--bg-card) !important;
        }


        /* Bootstrap dropdown-menu */
        body.dark-theme .dropdown-menu {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .dropdown-menu .dropdown-item {
            color: #c3c8da !important;
        }

        body.dark-theme .dropdown-menu .dropdown-item:hover,
        body.dark-theme .dropdown-menu .dropdown-item:focus {
            background-color: #21263a !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .dropdown-menu .dropdown-divider {
            border-color: #2e3349 !important;
        }

        /* Custom day/month/year picker dropdowns (attendance/index, leave/index) */
        body.dark-theme .custom-day-picker .dropdown-menu {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .custom-day-picker .form-control {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .custom-day-picker .dropdown-menu span.fw-bold {
            color: #8b93a8 !important;
        }

        /* Bootstrap .btn-light in dark mode */
        body.dark-theme .btn-light,
        body.dark-theme .btn-outline-light {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #c3c8da !important;
        }

        body.dark-theme .btn-light:hover {
            background-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        /* Bootstrap alerts */
        body.dark-theme .alert {
            border-color: #2e3349 !important;
        }

        body.dark-theme .alert:not(.alert-danger):not(.alert-warning):not(.alert-success):not(.alert-info) {
            background-color: #21263a !important;
            color: #e8eaf0 !important;
        }

        /* Bootstrap .form-control override (inline bg-color: #fff on custom dropdowns) */
        body.dark-theme .form-control {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-control:focus {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.15) !important;
        }

        body.dark-theme .form-control::placeholder {
            color: #6b7492 !important;
        }

        /* Bootstrap .form-select */
        body.dark-theme .form-select {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .form-select:focus {
            border-color: #0BAA4B !important;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.15) !important;
        }

        /* Bootstrap input-group */
        body.dark-theme .input-group-text {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #8b93a8 !important;
        }

        /* Text colors in various contexts */
        body.dark-theme [style*="color: #374151"],
        body.dark-theme [style*="color:#374151"],
        body.dark-theme [style*="color: #1f2937"],
        body.dark-theme [style*="color:#1f2937"],
        body.dark-theme [style*="color: #111827"],
        body.dark-theme [style*="color:#111827"],
        body.dark-theme [style*="color: #212529"],
        body.dark-theme [style*="color:#212529"] {
            color: #e8eaf0 !important;
        }

        body.dark-theme [style*="color: #4b5563"],
        body.dark-theme [style*="color:#4b5563"],
        body.dark-theme [style*="color: #6b7280"],
        body.dark-theme [style*="color:#6b7280"],
        body.dark-theme [style*="color: #6c757d"],
        body.dark-theme [style*="color:#6c757d"] {
            color: #8b93a8 !important;
        }

        /* Border colors (light gray borders) */
        body.dark-theme [style*="border: 1px solid #e5e7eb"],
        body.dark-theme [style*="border: 1px solid #d1d5db"],
        body.dark-theme [style*="border-color: #e5e7eb"],
        body.dark-theme [style*="border-color: #d1d5db"],
        body.dark-theme [style*="border-bottom: 1px solid #e5e7eb"],
        body.dark-theme [style*="border-top: 1px solid #e5e7eb"] {
            border-color: #2e3349 !important;
        }

        /* Tab navigation (BS tabs) */
        body.dark-theme .nav-tabs {
            border-color: #2e3349 !important;
        }

        body.dark-theme .nav-tabs .nav-link {
            color: #8b93a8 !important;
            border-color: transparent !important;
        }

        body.dark-theme .nav-tabs .nav-link:hover {
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .nav-tabs .nav-link.active {
            background-color: #1a1d27 !important;
            border-color: #2e3349 #2e3349 #1a1d27 !important;
            color: #0BAA4B !important;
        }

        body.dark-theme .tab-content {
            color: #e8eaf0;
        }

        /* detail-section headings & borders */
        body.dark-theme .detail-section {
            background-color: #1a1d27;
            color: #e8eaf0;
        }

        body.dark-theme .detail-section h2 {
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }

        /* Show employee page local styles */
        body.dark-theme .info-grid-item,
        body.dark-theme .info-section {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        /* Bootstrap list-group */
        body.dark-theme .list-group-item {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
            color: #e8eaf0 !important;
        }

        body.dark-theme .list-group-item:hover {
            background-color: #21263a !important;
        }

        /* Bootstrap card variants used in individual views */
        body.dark-theme .card-header {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
            color: #c3c8da !important;
        }

        body.dark-theme .card-footer {
            background-color: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .card-body {
            color: #e8eaf0 !important;
        }

        /* Daily overview cards (attendance) */
        body.dark-theme .card[style*="background: white"],
        body.dark-theme .card[style*="background-color: white"],
        body.dark-theme .card[style*="background: #fff"],
        body.dark-theme .card[style*="background-color: #fff"] {
            background-color: #1a1d27 !important;
            background: #1a1d27 !important;
            border-color: #2e3349 !important;
        }

        /* Flatpickr calendar in dark mode */
        body.dark-theme .flatpickr-calendar {
            background: #1a1d27 !important;
            border-color: #2e3349 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
        }

        body.dark-theme .flatpickr-month,
        body.dark-theme .flatpickr-weekdays,
        body.dark-theme span.flatpickr-weekday {
            background: #21263a !important;
            color: #c3c8da !important;
            fill: #c3c8da !important;
        }

        body.dark-theme .flatpickr-day {
            color: #c3c8da !important;
        }

        body.dark-theme .flatpickr-day:hover {
            background: #21263a !important;
            border-color: #2e3349 !important;
        }

        body.dark-theme .flatpickr-day.selected,
        body.dark-theme .flatpickr-day.startRange,
        body.dark-theme .flatpickr-day.endRange {
            background: #0BAA4B !important;
            border-color: #0BAA4B !important;
            color: white !important;
        }

        body.dark-theme .flatpickr-day.flatpickr-disabled {
            color: #3d445e !important;
        }

        body.dark-theme .numInput,
        body.dark-theme .cur-year {
            color: #e8eaf0 !important;
        }

        body.dark-theme .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: #21263a !important;
            color: #e8eaf0 !important;
        }

        /* action-card (attendance/self.blade check-in cards) */
        body.dark-theme .action-card.in {
            background-color: rgba(11, 170, 75, 0.15) !important;
            color: #4ade80 !important;
        }

        /* Bootstrap .badge-secondary override */
        body.dark-theme .badge-secondary,
        body.dark-theme .badge.badge-secondary {
            background-color: #2e3349 !important;
            color: #c3c8da !important;
            border-color: #3d445e !important;
        }

        /* Salary & config views' local .card redeclaration */
        body.dark-theme .content-wrapper .card {
            background-color: #1a1d27 !important;
            color: #e8eaf0 !important;
            border-color: #2e3349 !important;
        }
    </style>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
    @php
        $isUserBirthday = false;
        if (auth()->check() && auth()->user()->nhanVien && auth()->user()->nhanVien->NgaySinh) {
            $dob = \Carbon\Carbon::parse(auth()->user()->nhanVien->NgaySinh);
            if ($dob->isBirthday()) {
                $isUserBirthday = true;
            }
        }
    @endphp
    @if($isUserBirthday)
        <style>
            /* Birthday Theme Overrides */
            .nav-item:hover,
            .submenu-item:hover {
                background-color: #fdf2f8 !important;
                color: #db2777 !important;
                border-left-color: #db2777 !important;
            }

            .nav-item.active,
            .submenu-item.active {
                background-color: #fce7f3 !important;
                border-left-color: #db2777 !important;
                color: #db2777 !important;
            }

            .btn-primary {
                background-color: #f472b6 !important;
            }

            .btn-primary:hover {
                background-color: #db2777 !important;
            }

            .user-avatar {
                background-color: #fce7f3 !important;
                color: #db2777 !important;
            }

            .badge-success {
                background-color: #fce7f3 !important;
                color: #be185d !important;
            }

            .text-primary {
                color: #db2777 !important;
            }

            .mobile-header span {
                color: #db2777 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: #f472b6 !important;
                border-color: #f472b6 !important;
                color: white !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background-color: #db2777 !important;
                border-color: #db2777 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                border-color: #f472b6 !important;
                color: #f472b6 !important;
            }

            .form-control:focus,
            .dataTables_wrapper .dataTables_length select:focus,
            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #f472b6 !important;
                box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.2) !important;
            }

            .stat-card:hover {
                border-color: #f472b6 !important;
            }
        </style>
    @endif
</head>

<body>
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.body.classList.add('dark-theme');
                }
            } catch (e) { }
        })();
    </script>
    @if($isUserBirthday)
        <div
            style="background: linear-gradient(90deg, #fbcfe8, #f472b6, #db2777, #f472b6, #fbcfe8); background-size: 200% 100%; animation: gradientShine 3s ease infinite; color: white; text-align: center; padding: 12px; font-weight: bold; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px; z-index: 9999; position: relative; box-shadow: 0 4px 15px rgba(244,114,182,0.4);">
            <span style="font-size: 24px;">🎂</span>
            <span>Chúc mừng sinh nhật {{ auth()->user()->nhanVien->Ten }}! Chúc bạn một ngày ngập tràn niềm vui và hạnh
                phúc! 🎀 💝</span>
            <span style="font-size: 24px;">🎈</span>
        </div>
        <style>
            @keyframes gradientShine {
                0% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }
        </style>
    @endif
    <div class="app-container">
        <div class="sidebar-overlay" onclick="toggleMobileMenu()"></div>

        <!-- Mobile Header -->
        <header class="mobile-header">
            <a href="{{ route('dashboard') }}"
                style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}" alt="Logo"
                    style="height: 32px; width: auto;">
                <span style="font-weight: 700; font-size: 18px; color: #0BAA4B;">TRIWIN</span>
            </a>
            <button class="mobile-toggle" onclick="toggleMobileMenu()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}"
                        alt="Logo" class="sidebar-logo"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27120%27 height=%27120%27%3E%3Ccircle cx=%2760%27 cy=%2760%27 r=%2750%27 fill=%27%230BAA4B%27/%3E%3Ctext x=%2760%27 y=%2770%27 font-size=%2724%27 fill=%27white%27 text-anchor=%27middle%27%3ETRIWIN%3C/text%3E%3C/svg%3E'">
                </a>
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

                @canany(['Xem Thông Tin Cá Nhân', 'Xem Hợp Đồng Cá Nhân'])
                    @unlessrole('System Admin')
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
                            @can('Xem Thông Tin Cá Nhân')
                                <a href="{{ $authNV ? route('nhan-vien.info', $authNV->id) : '#' }}"
                                    class="submenu-item {{ request()->routeIs('nhan-vien.info') && request()->route('id') == ($authNV?->id ?? 0) ? 'active' : '' }}">
                                    <span>Thông tin cá nhân</span>
                                </a>
                            @endcan
                            @can('Xem Hợp Đồng Cá Nhân')
                                <a href="{{ $activeContract ? route('hop-dong.info', $activeContract->id) : '#' }}"
                                    class="submenu-item {{ request()->routeIs('hop-dong.info') && request()->route('id') == ($activeContract?->id ?? 0) ? 'active' : '' }}">
                                    <span>Hợp đồng cá nhân</span>
                                </a>
                            @endcan
                            <a href="{{ route('profile.settings') }}"
                                class="submenu-item {{ request()->routeIs('profile.settings') ? 'active' : '' }}">
                                <span>Cài đặt tài khoản</span>
                            </a>
                        </div>
                    </div>
                    @endunlessrole
                @endcanany
                @can('Xem Danh Sách Người Dùng')
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
                        <div class="nav-item {{ request()->routeIs('phong-ban.*') || request()->routeIs('chuc-vu.*') ? 'active' : '' }}"
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
                            <a href="{{ route('chuc-vu.index') }}"
                                class="submenu-item {{ request()->routeIs('chuc-vu.*') ? 'active' : '' }}">
                                <span>Chức vụ</span>
                            </a>
                        </div>
                    </div>
                @endcan

                @canany(['Xem Nhân Viên', 'Xem Danh Sách Công Tác', 'Điều Chuyển Công Tác'])
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
                            @can('Xem Nhân Viên')
                                <a href="{{ route('nhan-vien.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('nhan-vien.danh-sach') ? 'active' : '' }}">
                                    <span>Danh sách nhân viên</span>
                                </a>
                            @endcan

                            @canany(['Xem Danh Sách Công Tác', 'Tạo Yêu Cầu Công Tác', 'Xem Chi Tiết Công Tác', 'Điều Chuyển Công Tác'])
                                <a href="{{ route('cong-tac.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('cong-tac.*') ? 'active' : '' }}">
                                    <span>Công tác</span>
                                </a>
                            @endcanany
                        </div>
                    </div>
                @endcanany

                @canany(['Xem Danh Sách Hợp Đồng'])
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('hop-dong.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('contract-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Hợp đồng</span>
                            </div>
                            <svg class="chevron-icon {{ request()->routeIs('hop-dong.*') ? 'rotate' : '' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu {{ request()->routeIs('hop-dong.*') ? 'open' : '' }}" id="contract-submenu">
                            <a href="{{ route('hop-dong.danh-sach') }}"
                                class="submenu-item {{ request()->routeIs('hop-dong.danh-sach') ? 'active' : '' }}">
                                <span>Danh sách hợp đồng</span>
                            </a>

                            <a href="{{ route('hop-dong.loai-hop-dong.index') }}"
                                class="submenu-item {{ request()->routeIs('hop-dong.loai-hop-dong.*') ? 'active' : '' }}">
                                <span>Cấu hình hợp đồng</span>
                            </a>
                        </div>
                    </div>
                @endcanany

                @canany(['Xem Danh Sách Chấm Công', 'Xem Chấm Công Cá Nhân'])
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('cham-cong.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('attendance-submenu')" style="pointer: cursor;">
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
                            @can('Xem Danh Sách Chấm Công')
                                <a href="{{ route('cham-cong.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('cham-cong.danh-sach') ? 'active' : '' }}">
                                    <span>Danh sách chấm công</span>
                                </a>
                            @endcan
                            @unlessrole('Employee|Nhân viên')
                            <a href="{{ route('cham-cong.taoView') }}"
                                class="submenu-item {{ request()->routeIs('cham-cong.taoView') ? 'active' : '' }}">
                                <span>Chấm công (Admin)</span>
                            </a>
                            @endunlessrole
                            @can('Xem Chấm Công Cá Nhân')
                                <a href="{{ route('cham-cong.ca-nhan') }}"
                                    class="submenu-item {{ request()->routeIs('cham-cong.ca-nhan') ? 'active' : '' }}">
                                    <span>Chấm công cá nhân</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @canany(['Xem Danh Sách WFH', 'Tạo Phiếu WFH Cá Nhân', 'Duyệt WFH'])
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('wfh.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('wfh-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Work From Home</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="wfh-submenu">
                            @canany(['Xem Danh Sách WFH', 'Duyệt WFH'])
                                <a href="{{ route('wfh.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('wfh.danh-sach') ? 'active' : '' }}">
                                    <span>Danh sách WFH (Admin)</span>
                                </a>
                            @endcanany
                            @can('Tạo Phiếu WFH Cá Nhân')
                                <a href="{{ route('wfh.ca-nhan') }}"
                                    class="submenu-item {{ request()->routeIs('wfh.ca-nhan') ? 'active' : '' }}">
                                    <span>Đăng ký WFH</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @canany(['Xem Danh Sách Nghỉ Phép', 'Tạo Phiếu Nghỉ Phép Cá Nhân', 'Duyệt Nghỉ Phép'])
                    <div class="nav-item-parent">
                        <div class="nav-item {{ request()->routeIs('nghi-phep.*') ? 'active' : '' }}"
                            onclick="toggleSubmenu('nghi-phep-submenu')" style="cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Nghỉ phép</span>
                            </div>
                            <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="submenu" id="nghi-phep-submenu">
                            @canany(['Xem Danh Sách Nghỉ Phép', 'Duyệt Nghỉ Phép'])
                                <a href="{{ route('nghi-phep.danh-sach') }}"
                                    class="submenu-item {{ request()->routeIs('nghi-phep.danh-sach') ? 'active' : '' }}">
                                    <span>Nghỉ phép (Admin)</span>
                                </a>
                                <a href="{{ route('nghi-phep.con-lai') }}"
                                    class="submenu-item {{ request()->routeIs('nghi-phep.con-lai') ? 'active' : '' }}">
                                    <span>Danh sách phép còn lại</span>
                                </a>
                                <a href="{{ route('nghi-phep.config') }}"
                                    class="submenu-item {{ request()->routeIs('nghi-phep.config') ? 'active' : '' }}">
                                    <span>Cấu hình nghỉ phép</span>
                                </a>
                            @endcanany
                            @can('Tạo Phiếu Nghỉ Phép Cá Nhân')
                                <a href="{{ route('nghi-phep.ca-nhan') }}"
                                    class="submenu-item {{ request()->routeIs('nghi-phep.ca-nhan') ? 'active' : '' }}">
                                    <span>Đăng ký nghỉ phép</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @canany(['Xem Danh Sách Lương', 'Xem Lương Cá Nhân'])
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
                            @can('Xem Danh Sách Lương')
                                <a href="{{ route('salary.index') }}"
                                    class="submenu-item {{ request()->routeIs('salary.index') ? 'active' : '' }}">
                                    <span>Danh sách lương</span>
                                </a>
                            @endcan
                            @can('Xem Lương Cá Nhân')
                                @unlessrole('System Admin')
                                <a href="{{ $authNV ? route('salary.detail', $authNV->id) : '#' }}"
                                    class="submenu-item {{ request()->routeIs('salary.detail') && request()->route('id') == ($authNV?->id ?? 0) ? 'active' : '' }}">
                                    <span>Lương cá nhân</span>
                                </a>
                                @endunlessrole
                            @endcan

                            @canany(['Xem Danh Sách Lương', 'Xem Lương Cá Nhân', 'Quản lý hệ thống'])
                                <a href="{{ route('tam-ung.index') }}"
                                    class="submenu-item {{ request()->routeIs('tam-ung.*') ? 'active' : '' }}">
                                    <span>Tạm ứng lương</span>
                                </a>
                            @endcanany

                        </div>
                    </div>
                @endcanany

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
                                <span>Cấu hình chung</span>
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
                <div class="theme-toggle-container">
                    <label class="theme-toggle-label">Giao diện hệ thống</label>
                    <select id="global-theme-selector" class="theme-select no-select2">
                        <option value="light">Chế độ Sáng</option>
                        <option value="dark">Chế độ Tối</option>
                    </select>
                </div>
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
                        <div style="font-size: 12px; color: #047857; font-weight: 500;">{{ $chucVu }}</div>
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
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
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
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            // Prevent body scroll when menu is open
            if (sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
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
        // Sidebar active state handling
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Auto-open submenu if current page is in submenu
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

            // 2. Auto-scroll sidebar to active item
            const activeItem = document.querySelector('.nav-item.active, .submenu-item.active');
            if (activeItem) {
                setTimeout(() => {
                    activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 200);
            }
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
                $(container).find('select').filter(function () {
                    const $select = $(this);
                    return !$select.hasClass('no-select2') &&
                        !$select.hasClass('select2-hidden-accessible') &&
                        !$select.hasClass('flatpickr-monthDropdown-months') &&
                        $select.closest('.no-select2, .no-select2-parent, .dataTables_length, .swal2-container, .swal2-popup').length === 0 &&
                        !($select.attr('name') && $select.attr('name').endsWith('_length'));
                }).each(function () {
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

            // Global Delete Confirmation with SweetAlert2
            $(document).on('click', '.delete-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const message = $(this).data('message') || 'Bạn có chắc chắn muốn xóa mục này?';

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Xác nhận xóa',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Global Theme Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const themeSelector = document.getElementById('global-theme-selector');
            if (themeSelector) {
                const currentTheme = localStorage.getItem('theme') || 'light';
                themeSelector.value = currentTheme;

                themeSelector.addEventListener('change', function (e) {
                    const selectedTheme = e.target.value;
                    if (selectedTheme === 'dark') {
                        document.body.classList.add('dark-theme');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.body.classList.remove('dark-theme');
                        localStorage.setItem('theme', 'light');
                    }
                });
            }
        });

    </script>
</body>

</html>