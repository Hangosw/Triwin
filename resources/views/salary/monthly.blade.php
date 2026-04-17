@extends('layouts.app')

@section('title', 'Bảng lương tháng - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
<div class="page-header">
    <h1>Bảng lương tháng</h1>
    <p>Quản lý bảng lương theo tháng của nhân viên</p>
</div>

<!-- Filter Bar -->
<div class="card">
    <div class="action-bar">
        <div style="display: flex; gap: 16px; align-items: center;">
            <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                <select class="form-control">
                    <option>Tháng 1/2024</option>
                    <option selected>Tháng 2/2024</option>
                    <option>Tháng 3/2024</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                <select class="form-control">
                    <option value="">Tất cả phòng ban</option>
                    <option>Phòng Kỹ thuật</option>
                    <option>Phòng Nhân sự</option>
                    <option>Phòng Kinh doanh</option>
                </select>
            </div>
        </div>
        <div class="action-buttons">
{{-- 
            <button class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Xuất Excel
            </button>
--}}
            <button class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tính lương
            </button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><strong>STT</strong></th>
                    <th>Họ và tên</th>
                    <th>Phòng ban</th>
                    <th>Lương cơ bản</th>
                    <th>Phụ cấp</th>
                    <th>Tăng ca</th>
                    <th>Khấu trừ</th>
                    <th>Thực lãnh</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>1</strong></td>
                    <td>
                        <div class="font-medium">Nguyễn Văn An</div>
                        <div class="text-gray" style="font-size: 13px;">NV001</div>
                    </td>
                    <td>Phòng Kỹ thuật</td>
                    <td>15,000,000 đ</td>
                    <td>3,000,000 đ</td>
                    <td>2,500,000 đ</td>
                    <td>2,050,000 đ</td>
                    <td class="text-primary font-medium">18,450,000 đ</td>
                    <td><span class="badge badge-success">Đã chốt</span></td>
                </tr>
                <tr>
                    <td><strong>2</strong></td>
                    <td>
                        <div class="font-medium">Trần Thị Bình</div>
                        <div class="text-gray" style="font-size: 13px;">NV002</div>
                    </td>
                    <td>Phòng Nhân sự</td>
                    <td>12,000,000 đ</td>
                    <td>2,000,000 đ</td>
                    <td>1,500,000 đ</td>
                    <td>1,525,000 đ</td>
                    <td class="text-primary font-medium">13,975,000 đ</td>
                    <td><span class="badge badge-warning">Chờ duyệt</span></td>
                </tr>
                <tr>
                    <td><strong>3</strong></td>
                    <td>
                        <div class="font-medium">Lê Hoàng Cường</div>
                        <div class="text-gray" style="font-size: 13px;">NV003</div>
                    </td>
                    <td>Phòng Kinh doanh</td>
                    <td>18,000,000 đ</td>
                    <td>4,000,000 đ</td>
                    <td>3,200,000 đ</td>
                    <td>2,520,000 đ</td>
                    <td class="text-primary font-medium">22,680,000 đ</td>
                    <td><span class="badge badge-success">Đã chốt</span></td>
                </tr>
                <tr>
                    <td><strong>4</strong></td>
                    <td>
                        <div class="font-medium">Phạm Thu Dung</div>
                        <div class="text-gray" style="font-size: 13px;">NV004</div>
                    </td>
                    <td>Phòng Sản xuất</td>
                    <td>10,000,000 đ</td>
                    <td>1,500,000 đ</td>
                    <td>2,000,000 đ</td>
                    <td>1,350,000 đ</td>
                    <td class="text-primary font-medium">12,150,000 đ</td>
                    <td><span class="badge badge-warning">Chờ duyệt</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Summary Card -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">Tổng lương cơ bản</div>
        <div class="value" style="color: #3b82f6;">55,000,000 đ</div>
    </div>
    <div class="stat-card">
        <div class="label">Tổng phụ cấp</div>
        <div class="value" style="color: #8b5cf6;">10,500,000 đ</div>
    </div>
    <div class="stat-card">
        <div class="label">Tổng tăng ca</div>
        <div class="value" style="color: #f97316;">9,200,000 đ</div>
    </div>
    <div class="stat-card">
        <div class="label">Tổng thực lãnh</div>
        <div class="value" style="color: #0BAA4B;">67,255,000 đ</div>
    </div>
</div>
@endsection
