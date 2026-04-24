@extends('layouts.app')

@section('title', __('Cấu hình hệ thống') . ' - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        @can('Quản lý hệ thống')
            <h1>{{ __('Cấu hình hệ thống') }}</h1>
            <p>{{ __('Quản lý các cấu hình và tham số hệ thống') }}</p>
        @else
            <h1>{{ __('Cài đặt cá nhân') }}</h1>
            <p>{{ __('Tùy chỉnh giao diện và ngôn ngữ của bạn') }}</p>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success"
            style="color: #0BAA4B; background-color: #d1fae5; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif



    <form action="{{ route('config.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Language & Interface Settings -->
        <div class="config-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <!-- Language Card -->
            <div class="card" style="margin-bottom: 0 !important;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <i class="bi bi-translate text-primary" style="font-size: 20px;"></i>
                    <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Cấu hình ngôn ngữ') }}</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <label class="config-option" style="cursor: pointer;">
                        <input type="radio" name="default_language" value="vi" {{ (auth()->user()->language ?? $configs['default_language'] ?? 'vi') == 'vi' ? 'checked' : '' }} style="display: none;">
                        <div class="option-content">
                            <span style="font-size: 24px;">🇻🇳</span>
                            <span style="font-weight: 500;">{{ __('Tiếng Việt') }}</span>
                            <i class="bi bi-check-circle-fill check-icon"></i>
                        </div>
                    </label>
                    <label class="config-option" style="cursor: pointer;">
                        <input type="radio" name="default_language" value="en" {{ (auth()->user()->language ?? $configs['default_language'] ?? '') == 'en' ? 'checked' : '' }} style="display: none;">
                        <div class="option-content">
                            <span style="font-size: 24px;">🇺🇸</span>
                            <span style="font-weight: 500;">{{ __('Tiếng Anh') }}</span>
                            <i class="bi bi-check-circle-fill check-icon"></i>
                        </div>
                    </label>
                </div>
                
                <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('Lưu cài đặt này') }}
                    </button>
                </div>
            </div>

            <!-- Theme Card -->
            <div class="card" style="margin-bottom: 0 !important;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <i class="bi bi-palette text-primary" style="font-size: 20px;"></i>
                    <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Cấu hình giao diện') }}</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <label class="config-option" style="cursor: pointer;">
                        <input type="radio" name="default_theme" value="light" {{ (auth()->user()->theme ?? $configs['default_theme'] ?? 'light') == 'light' ? 'checked' : '' }} style="display: none;">
                        <div class="option-content">
                            <i class="bi bi-sun" style="font-size: 24px; color: #f59e0b;"></i>
                            <span style="font-weight: 500;">{{ __('Sáng (Light)') }}</span>
                            <i class="bi bi-check-circle-fill check-icon"></i>
                        </div>
                    </label>
                    <label class="config-option" style="cursor: pointer;">
                        <input type="radio" name="default_theme" value="dark" {{ (auth()->user()->theme ?? $configs['default_theme'] ?? '') == 'dark' ? 'checked' : '' }} style="display: none;">
                        <div class="option-content">
                            <i class="bi bi-moon-stars" style="font-size: 24px; color: #6366f1;"></i>
                            <span style="font-weight: 500;">{{ __('Tối (Dark)') }}</span>
                            <i class="bi bi-check-circle-fill check-icon"></i>
                        </div>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('Lưu cài đặt này') }}
                    </button>
                </div>
            </div>
        </div>
        @can('Quản lý hệ thống')
        <!-- General Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Thông tin đơn vị & Người đại diện') }}</h3>
                <span class="badge" style="background: #f0fdf4; color: #166534; padding: 6px 12px; border-radius: 6px; font-size: 12px;">{{ __('Cấu hình chung') }}</span>
            </div>

            <div class="config-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <!-- Left Column: Company Info -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label">{{ __('Tên công ty / Đơn vị') }}</label>
                        <input type="text" name="company_name" class="form-control"
                            value="{{ $configs['company_name'] ?? '' }}" placeholder="{{ __('Nhập tên đầy đủ') }}">
                    </div>
                    
                    <div class="config-grid-2-inner" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">{{ __('Mã số thuế') }}</label>
                            <input type="text" name="company_tax_code" class="form-control"
                                value="{{ $configs['company_tax_code'] ?? '' }}" placeholder="0123456789">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Số điện thoại (Hotline)') }}</label>
                            <input type="text" name="company_hotline" class="form-control"
                                value="{{ $configs['company_hotline'] ?? '' }}" placeholder="028.xxxx.xxxx">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Địa chỉ trụ sở') }}</label>
                        <textarea name="company_address" class="form-control"
                            rows="2" placeholder="{{ __('Số nhà, tên đường, quận/huyện...') }}">{{ $configs['company_address'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Right Column: Representative & Logo -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" style="display: flex; align-items: center; gap: 6px;">
                            <i class="bi bi-person-badge"></i> {{ __('Người đại diện ký tên') }}
                        </label>
                        <select name="signer_id" class="form-control select2" data-placeholder="{{ __('Chọn nhân viên ký tên') }}">
                            <option value=""></option>
                            @foreach($nhanViens as $nv)
                                <option value="{{ $nv->id }}" {{ ($configs['signer_id'] ?? '') == $nv->id ? 'selected' : '' }}>
                                    [{{ $nv->Ma }}] {{ $nv->Ten }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: #6b7280; font-size: 12px; margin-top: 4px;">{{ __('* Nhân viên này sẽ xuất hiện trên các văn bản, hợp đồng của hệ thống.') }}</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('Logo công ty') }}</label>
                        <div style="display: flex; align-items: center; gap: 20px; padding: 16px; border: 1px dashed #d1d5db; border-radius: 12px; background: #f9fafb;">
                            @if(isset($configs['company_logo']))
                                <div style="position: relative; width: 80px; height: 80px;">
                                    <img src="{{ asset($configs['company_logo']) }}" alt="Logo" 
                                        style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px; background: white;">
                                </div>
                            @else
                                <div style="width: 80px; height: 80px; border: 1px solid #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: white; color: #9ca3af; font-size: 12px; text-align: center;">
                                    {{ __('Chưa có logo') }}
                                </div>
                            @endif
                            <div style="flex: 1;">
                                <input type="file" name="company_logo" class="form-control" accept="image/*" style="font-size: 13px;">
                                <p style="font-size: 11px; color: #6b7280; margin-top: 6px; margin-bottom: 0;">{{ __('Khuyên dùng ảnh PNG trong suốt. Tối đa 2MB.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="bi bi-check2-circle"></i> {{ __('Lưu cấu hình đơn vị') }}
                </button>
            </div>
        </div>

        <!-- Work Time Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-clock-history text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Cấu hình thời gian & Nghỉ phép') }}</h3>
            </div>

            <div class="config-grid-3" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">{{ __('Giờ bắt đầu làm việc') }}</label>
                    <input type="time" name="work_time_start" class="form-control"
                        value="{{ $configs['work_time_start'] ?? '08:00' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Giờ kết thúc ca') }}</label>
                    <input type="time" name="work_time_end" class="form-control"
                        value="{{ $configs['work_time_end'] ?? '17:30' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Nghỉ trưa (phút)') }}</label>
                    <input type="number" name="lunch_break_minutes" class="form-control"
                        value="{{ $configs['lunch_break_minutes'] ?? 60 }}">
                </div>
                <div class="form-group">
                    <label class="form-label text-truncate" title="{{ __('Số ngày công chuẩn (1 tháng)') }}">{{ __('Công chuẩn / tháng') }}</label>
                    <input type="number" name="standard_work_days" class="form-control"
                        value="{{ $configs['standard_work_days'] ?? 26 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Phép năm mặc định') }}</label>
                    <input type="number" name="annual_leave_days" class="form-control"
                        value="{{ $configs['annual_leave_days'] ?? 12 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Giới hạn nghỉ / lần') }}</label>
                    <input type="number" name="annual_leave_limit_per_request" class="form-control"
                        value="{{ $configs['annual_leave_limit_per_request'] ?? 5 }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ __('Lưu cài đặt thời gian') }}
                </button>
            </div>
        </div>

        <!-- Salary Settings Redesign -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-cash-stack text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Định mức lương & Khấu trừ') }}</h3>
            </div>

            <div class="config-grid-3" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">{{ __('Lương cơ sở (VNĐ)') }}</label>
                    <input type="number" name="base_salary" class="form-control"
                        value="{{ $configs['base_salary'] ?? 2340000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Giảm trừ cá nhân') }}</label>
                    <input type="number" name="tax_deduction_personal" class="form-control"
                        value="{{ $configs['tax_deduction_personal'] ?? 11000000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Giảm trừ phụ thuộc') }}</label>
                    <input type="number" name="tax_deduction_dependent" class="form-control"
                        value="{{ $configs['tax_deduction_dependent'] ?? 4400000 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Bảo hiểm XH (%)') }}</label>
                    <input type="number" name="insurance_bhxh_emp" class="form-control"
                        value="{{ $configs['insurance_bhxh_emp'] ?? 8 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Bảo hiểm Y tế (%)') }}</label>
                    <input type="number" name="insurance_bhyt_emp" class="form-control"
                        value="{{ $configs['insurance_bhyt_emp'] ?? 1.5 }}" step="0.1">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Bảo hiểm TN (%)') }}</label>
                    <input type="number" name="insurance_bhtn_emp" class="form-control"
                        value="{{ $configs['insurance_bhtn_emp'] ?? 1 }}" step="0.1">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ __('Lưu thông số tài chính') }}
                </button>
            </div>
        </div>

        <!-- WFH Redesign (Replacing Overtime since Overtime is removed) -->
        <div class="card">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <i class="bi bi-laptop text-primary" style="font-size: 20px;"></i>
                <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B; margin: 0;">{{ __('Cấu hình Work From Home') }}</h3>
            </div>

            <div class="config-grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">{{ __('Số ngày WFH tối đa / tháng') }}</label>
                    <input type="number" name="max_wfh_days_per_month" class="form-control"
                        value="{{ $configs['max_wfh_days_per_month'] ?? 4 }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Quy chuẩn hưởng lương (%)') }}</label>
                    <input type="number" name="wfh_salary_rate" class="form-control"
                        value="{{ $configs['wfh_salary_rate'] ?? 100 }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ __('Lưu cài đặt WFH') }}
                </button>
            </div>
        </div>
        @endcan
    </form>
        @can('Quản lý hệ thống')
    <form action="{{ route('config.lich-lam-viec.update') }}" method="POST">
        @csrf
        <div class="card" style="margin-top: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">{{ __('Cấu hình ngày làm việc trong tuần') }}</h3>
            
            <div class="table-container">
                <table class="table table-bordered table-hover config-table" id="scheduleTable" style="width: 100%;">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th style="width: 200px;">{{ __('Thứ') }}</th>
                            <th>{{ __('Hình thức làm việc') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lichLamViecs as $lich)
                            <tr>
                                <td style="font-weight: 500;">{{ __($lich->MoTa) }}</td>
                                <td>
                                    <div class="schedule-radios" style="display: flex; gap: 30px; align-items: center;">
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="full" 
                                                {{ ($lich->CoLamViec == 1 && $lich->HeSoNgayCong == 1) ? 'checked' : '' }}>
                                            {{ __('Làm cả ngày (1.0)') }}
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="half" 
                                                {{ ($lich->CoLamViec == 1 && $lich->HeSoNgayCong == 0.5) ? 'checked' : '' }}>
                                            {{ __('Làm nửa ngày (0.5)') }}
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                            <input type="radio" name="lich_lam_viecs[{{ $lich->id }}][type]" value="off" 
                                                {{ $lich->CoLamViec == 0 ? 'checked' : '' }}>
                                            {{ __('Nghỉ (0.0)') }}
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
                {{ __('Lưu cấu hình ngày làm việc') }}
            </button>
        </div>
    </form>

    <hr style="margin: 40px 0; border: none; border-top: 1px solid #e5e7eb;">

    <form action="{{ route('config.ca-lam-viec.update') }}" method="POST">
        @csrf
        <div class="card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">{{ __('Lịch trình ca làm việc') }}</h3>

            <div class="table-container">
                <table class="table table-bordered table-hover config-table" id="shiftTable" style="width: 100%;">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>{{ __('Mã Ca') }}</th>
                            <th>{{ __('Tên Ca') }}</th>
                            <th>{{ __('Giờ Vào') }}</th>
                            <th>{{ __('Giờ Ra') }}</th>
                            <th>{{ __('Bắt Đầu Nghỉ') }}</th>
                            <th>{{ __('Kết Thúc Nghỉ') }}</th>
                            <th>{{ __('Qua Đêm?') }}</th>
                            <th>{{ __('Phụ Cấp Đêm (%)') }}</th>
                            <th>{{ __('Ghi Chú') }}</th>
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
                {{ __('Lưu lịch trình ca làm việc') }}
            </button>
        </div>
    </form>
    @endcan
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

        /* Config Option Styles */
        .config-option .option-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 20px;
            border: 2px solid var(--border-color, #e5e7eb);
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            background: white;
        }

        .config-option:hover .option-content {
            border-color: #0BAA4B;
            background-color: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .config-option input:checked + .option-content {
            border-color: #0BAA4B;
            background-color: #f0fdf4;
            box-shadow: 0 0 0 1px #0BAA4B;
        }

        .config-option .check-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #0BAA4B;
            display: none;
            font-size: 18px;
        }

        .config-option input:checked + .option-content .check-icon {
            display: block;
        }
        
        body.dark-theme .config-option .option-content {
            background: #1a1d27;
            border-color: #2e3349;
        }
        
        body.dark-theme .config-option:hover .option-content {
            background-color: rgba(11, 170, 75, 0.1);
        }
        
        body.dark-theme .config-option input:checked + .option-content {
            background-color: rgba(11, 170, 75, 0.15);
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 992px) {
            .config-grid-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .config-grid-2,
            .config-grid-3,
            .config-grid-2-inner {
                grid-template-columns: 1fr !important;
            }

            .schedule-radios {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px !important;
            }
        }
    </style>



@endsection

@push('scripts')
<script>
        $(document).ready(function() {
        $('#scheduleTable, #shiftTable').DataTable({
            
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
