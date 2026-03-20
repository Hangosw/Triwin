@extends('layouts.app')

@section('title', 'Cấu hình hệ thống - Vietnam Rubber Group')

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

        <!-- General Settings -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Thông tin công ty</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Tên công ty</label>
                    <input type="text" name="company_name" class="form-control"
                        value="{{ $configs['company_name'] ?? 'Vietnam Rubber Group' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Mã số thuế</label>
                    <input type="text" name="company_tax_code" class="form-control"
                        value="{{ $configs['company_tax_code'] ?? '0123456789' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="company_hotline" class="form-control"
                        value="{{ $configs['company_hotline'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="company_email" class="form-control"
                        value="{{ $configs['company_email'] ?? '' }}">
                </div>
                <div class="form-group" style="grid-column: span 1;">
                    <label class="form-label">Ảnh đại diện công ty</label>
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        @if(isset($configs['company_logo']))
                            <img src="{{ asset($configs['company_logo']) }}" alt="Logo" 
                                style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; padding: 5px; background: white;">
                        @else
                            <div style="width: 80px; height: 80px; border: 2px dashed #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 12px; text-align: center; padding: 5px;">
                                Chưa có logo
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <input type="file" name="company_logo" class="form-control" accept="image/*" style="padding: 8px;">
                            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">Định dạng: JPG, PNG, GIF. Tối đa 2MB.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label">Địa chỉ</label>
                <textarea name="company_address" class="form-control"
                    rows="3">{{ $configs['company_address'] ?? '123 Đường ABC, Phường XYZ, Thành phố Hồ Chí Minh' }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
        </div>

        <!-- Work Time Settings -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Cấu hình giờ làm việc</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Giờ vào làm</label>
                    <input type="time" name="work_time_start" class="form-control"
                        value="{{ $configs['work_time_start'] ?? '08:00' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giờ tan làm</label>
                    <input type="time" name="work_time_end" class="form-control"
                        value="{{ $configs['work_time_end'] ?? '17:30' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Thời gian nghỉ trưa (phút)</label>
                    <input type="number" name="lunch_break_minutes" class="form-control"
                        value="{{ $configs['lunch_break_minutes'] ?? 60 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Số ngày công chuẩn (1 tháng)</label>
                    <input type="number" name="standard_work_days" class="form-control"
                        value="{{ $configs['standard_work_days'] ?? 26 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Số ngày phép năm</label>
                    <input type="number" name="annual_leave_days" class="form-control"
                        value="{{ $configs['annual_leave_days'] ?? 12 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giới hạn ngày nghỉ phép năm / lần</label>
                    <input type="number" name="annual_leave_limit_per_request" class="form-control"
                        value="{{ $configs['annual_leave_limit_per_request'] ?? 5 }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
        </div>

        <!-- Salary Settings -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Cấu hình lương</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Lương cơ sở (VNĐ)</label>
                    <input type="number" name="base_salary" class="form-control"
                        value="{{ $configs['base_salary'] ?? 2340000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giảm trừ cá nhân (VNĐ)</label>
                    <input type="number" name="tax_deduction_personal" class="form-control"
                        value="{{ $configs['tax_deduction_personal'] ?? 11000000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Giảm trừ người phụ thuộc (VNĐ)</label>
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
                    <label class="form-label">Bảo hiểm thất nghiệp (%)</label>
                    <input type="number" name="insurance_bhtn_emp" class="form-control"
                        value="{{ $configs['insurance_bhtn_emp'] ?? 1 }}" step="0.1">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
        </div>

        <!-- Overtime Settings -->
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Cấu hình tăng ca</h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Hệ số tăng ca ngày thường</label>
                    <input type="number" name="ot_rate_normal" class="form-control"
                        value="{{ $configs['ot_rate_normal'] ?? 1.5 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Hệ số tăng ca cuối tuần</label>
                    <input type="number" name="ot_rate_weekend" class="form-control"
                        value="{{ $configs['ot_rate_weekend'] ?? 2.0 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Hệ số tăng ca ngày lễ</label>
                    <input type="number" name="ot_rate_holiday" class="form-control"
                        value="{{ $configs['ot_rate_holiday'] ?? 3.0 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">Hệ số tăng ca ban đêm</label>
                    <input type="number" name="ot_rate_night" class="form-control"
                        value="{{ $configs['ot_rate_night'] ?? 2.3 }}" step="0.1">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
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
