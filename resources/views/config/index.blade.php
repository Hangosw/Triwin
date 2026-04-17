@extends('layouts.app')

@section('title', 'Cấu hình hệ thống - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Cấu hình hệ thống</h1>
        <p>Quản lý các cấu hình và tham số hệ thống</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"
            style="color: #0BAA4B; background-color: #d1fae5; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif



    <form action="{{ route('config.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- General Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">Thông tin đơn vị & Người đại diện</h3>
                <span class="badge" style="background: #f0fdf4; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 12px;">Cấu hình chung</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <!-- Left Column: Company Info -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">Tên công ty / Đơn vị</label>
                        <input type="text" name="company_name" class="form-control"
                            value="{{ $configs['company_name'] ?? '' }}" placeholder="Nhập tên đầy đủ">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Mã số thuế</label>
                            <input type="text" name="company_tax_code" class="form-control"
                                value="{{ $configs['company_tax_code'] ?? '' }}" placeholder="0123456789">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số điện thoại (Hotline)</label>
                            <input type="text" name="company_hotline" class="form-control"
                                value="{{ $configs['company_hotline'] ?? '' }}" placeholder="028.xxxx.xxxx">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Địa chỉ trụ sở</label>
                        <textarea name="company_address" class="form-control"
                            rows="2" placeholder="Số nhà, tên đường, quận/huyện...">{{ $configs['company_address'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Right Column: Representative & Logo -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" style="display: flex; align-items: center; gap: 6px;">
                            <i class="bi bi-person-badge"></i> Người đại diện ký tên
                        </label>
                        <select name="signer_id" class="form-control select2" data-placeholder="Chọn nhân viên ký tên">
                            <option value=""></option>
                            @foreach($nhanViens as $nv)
                                <option value="{{ $nv->id }}" {{ ($configs['signer_id'] ?? '') == $nv->id ? 'selected' : '' }}>
                                    [{{ $nv->Ma }}] {{ $nv->Ten }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: #6b7280; font-size: 12px; margin-top: 4px;">* Nhân viên này sẽ xuất hiện trên các văn bản, hợp đồng của hệ thống.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Logo công ty</label>
                        <div style="display: flex; align-items: center; gap: 20px; padding: 16px; border: 1px dashed #d1d5db; border-radius: 12px; background: #f9fafb;">
                            @if(isset($configs['company_logo']))
                                <div style="position: relative; width: 80px; height: 80px;">
                                    <img src="{{ asset($configs['company_logo']) }}" alt="Logo" 
                                        style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px; background: white;">
                                </div>
                            @else
                                <div style="width: 80px; height: 80px; border: 1px solid #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: white; color: #9ca3af; font-size: 12px; text-align: center;">
                                    Chưa có logo
                                </div>
                            @endif
                            <div style="flex: 1;">
                                <input type="file" name="company_logo" class="form-control" accept="image/*" style="font-size: 13px;">
                                <p style="font-size: 11px; color: #6b7280; margin-top: 6px; margin-bottom: 0;">Khuyên dùng ảnh PNG trong suốt. Tối đa 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="bi bi-check2-circle"></i> Lưu cấu hình đơn vị
                </button>
            </div>
        </div>

        <!-- Work Time Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-clock-history text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">Cấu hình thời gian & Nghỉ phép</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Giờ bắt đầu làm việc</label>
                    <input type="time" name="work_time_start" class="form-control"
                        value="{{ $configs['work_time_start'] ?? '08:00' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giờ kết thúc ca</label>
                    <input type="time" name="work_time_end" class="form-control"
                        value="{{ $configs['work_time_end'] ?? '17:30' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Nghỉ trưa (phút)</label>
                    <input type="number" name="lunch_break_minutes" class="form-control"
                        value="{{ $configs['lunch_break_minutes'] ?? 60 }}">
                </div>
                <div class="form-group">
                    <label class="form-label text-truncate" title="Số ngày công chuẩn (1 tháng)">Công chuẩn / tháng</label>
                    <input type="number" name="standard_work_days" class="form-control"
                        value="{{ $configs['standard_work_days'] ?? 26 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phép năm mặc định</label>
                    <input type="number" name="annual_leave_days" class="form-control"
                        value="{{ $configs['annual_leave_days'] ?? 12 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giới hạn nghỉ / lần</label>
                    <input type="number" name="annual_leave_limit_per_request" class="form-control"
                        value="{{ $configs['annual_leave_limit_per_request'] ?? 5 }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu cài đặt thời gian
                </button>
            </div>
        </div>

        <!-- Salary Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-cash-stack text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">Định mức lương & Khấu trừ</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Lương cơ sở (VNĐ)</label>
                    <input type="number" name="base_salary" class="form-control"
                        value="{{ $configs['base_salary'] ?? 2340000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giảm trừ cá nhân</label>
                    <input type="number" name="tax_deduction_personal" class="form-control"
                        value="{{ $configs['tax_deduction_personal'] ?? 11000000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giảm trừ phụ thuộc</label>
                    <input type="number" name="tax_deduction_dependent" class="form-control"
                        value="{{ $configs['tax_deduction_dependent'] ?? 4400000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Bảo hiểm XH (%)</label>
                    <input type="number" name="insurance_bhxh_emp" class="form-control"
                        value="{{ $configs['insurance_bhxh_emp'] ?? 8 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Bảo hiểm Y tế (%)</label>
                    <input type="number" name="insurance_bhyt_emp" class="form-control"
                        value="{{ $configs['insurance_bhyt_emp'] ?? 1.5 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Bảo hiểm TN (%)</label>
                    <input type="number" name="insurance_bhtn_emp" class="form-control"
                        value="{{ $configs['insurance_bhtn_emp'] ?? 1 }}" step="0.1">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu thông số tài chính
                </button>
            </div>
        </div>

        <!-- WFH Redesign (Replacing Overtime since Overtime is removed) -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-laptop text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">Cấu hình Work From Home</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Số ngày WFH tối đa / tháng</label>
                    <input type="number" name="max_wfh_days_per_month" class="form-control"
                        value="{{ $configs['max_wfh_days_per_month'] ?? 4 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Quy chuẩn hưởng lương (%)</label>
                    <input type="number" name="wfh_salary_rate" class="form-control"
                        value="{{ $configs['wfh_salary_rate'] ?? 100 }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu cài đặt WFH
                </button>
            </div>
        </div>

    </form>

    <form action="{{ route('config.lich-lam-viec.update') }}" method="POST">
        @csrf
        <div class="card" style="margin-top: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Cấu hình ngày làm việc trong tuần</h3>
            
            <div class="table-container">
                <table class="table table-bordered table-hover config-table" id="scheduleTable" style="width: 100%;">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th style="width: 200px;">Thứ</th>
                            <th>Hình thức làm việc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lichLamViecs as $lich)
                            <tr>
                                <td style="font-weight: 500;">{{ $lich->MoTa }}</td>
                                <td>
                                    <div style="display: flex; gap: 30px; align-items: center;">
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="full" 
                                                {{ ($lich->CoLamViec == 1 && $lich->HeSoNgayCong == 1) ? 'checked' : '' }}>
                                            Làm cả ngày (1.0)
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="half" 
                                                {{ ($lich->CoLamViec == 1 && $lich->HeSoNgayCong == 0.5) ? 'checked' : '' }}>
                                            Làm nửa ngày (0.5)
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="off" 
                                                {{ $lich->CoLamViec == 0 ? 'checked' : '' }}>
                                            Nghỉ (0.0)
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu cấu hình ngày làm việc
            </button>
        </div>
    </form>

    <hr style="margin: 40px 0; border: none; border-top: 1px solid #e5e7eb;">

    <form action="{{ route('config.ca-lam-viec.update') }}" method="POST">
        @csrf
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Lịch trình ca làm việc</h3>

            <div class="table-container">
                <table class="table table-bordered table-hover config-table" id="shiftTable" style="width: 100%;">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>Mã Ca</th>
                            <th>Tên Ca</th>
                            <th>Giờ Vào</th>
                            <th>Giờ Ra</th>
                            <th>Bắt Đầu Nghỉ</th>
                            <th>Kết Thúc Nghỉ</th>
                            <th>Qua Đêm?</th>
                            <th>Phụ Cấp Đêm (%)</th>
                            <th>Ghi Chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($caLamViecs as $ca)
                            <tr>
                                <td>
                                    <input type="text" name="ca_lam_viecs[{{ $ca->id }}][MaCa]" class="form-control"
                                        value="{{ $ca->MaCa }}">
                                </td>
                                <td>
                                    <input type="text" name="ca_lam_viecs[{{ $ca->id }}][TenCa]" class="form-control"
                                        value="{{ $ca->TenCa }}">
                                </td>
                                <td>
                                    <input type="time" name="ca_lam_viecs[{{ $ca->id }}][GioVao]" class="form-control"
                                        value="{{ $ca->GioVao }}">
                                </td>
                                <td>
                                    <input type="time" name="ca_lam_viecs[{{ $ca->id }}][GioRa]" class="form-control"
                                        value="{{ $ca->GioRa }}">
                                </td>
                                <td>
                                    <input type="time" name="ca_lam_viecs[{{ $ca->id }}][BatDauNghi]" class="form-control"
                                        value="{{ $ca->BatDauNghi }}">
                                </td>
                                <td>
                                    <input type="time" name="ca_lam_viecs[{{ $ca->id }}][KetThucNghi]" class="form-control"
                                        value="{{ $ca->KetThucNghi }}">
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="ca_lam_viecs[{{ $ca->id }}][LaCaQuaDem]" value="1" {{ $ca->LaCaQuaDem ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                </td>
                                <td>
                                    <input type="number" name="ca_lam_viecs[{{ $ca->id }}][PhuCapCaDem]" class="form-control"
                                        value="{{ $ca->PhuCapCaDem }}" step="1">
                                </td>
                                <td>
                                    <input type="text" name="ca_lam_viecs[{{ $ca->id }}][GhiChu]" class="form-control"
                                        value="{{ $ca->GhiChu }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu lịch trình ca làm việc
            </button>
        </div>
    </form>

    <style>
        .config-table th {
            white-space: nowrap;
            font-size: 14px;
        }

        .config-table td {
            padding: 8px !important;
        }

        .config-table .form-control {
            font-size: 13px;
            padding: 6px 10px;
            height: auto;
        }
    </style>



@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#scheduleTable, #shiftTable').DataTable({
            language: {
                "sProcessing": "Đang xử lý...",
                "sLengthMenu": "Hiển thị _MENU_ mục",
                "sZeroRecords": "Không tìm thấy dữ liệu",
                "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
                "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
                "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                "sSearch": "Tìm kiếm:",
                "oPaginate": {
                    "sFirst": "Đầu",
                    "sPrevious": "Trước",
                    "sNext": "Tiếp",
                    "sLast": "Cuối"
                }
            },
            responsive: true,
            autoWidth: false,
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });
    });


</script>
@endpush
