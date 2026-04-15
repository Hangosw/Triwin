@extends('layouts.app')

@section('title', 'Cấu hình lương - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Cấu hình lương</h1>
        <p>Chọn phương thức tính lương áp dụng cho toàn hệ thống</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"
            style="color: #0BAA4B; background-color: #d1fae5; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #0BAA4B;">Phương thức tính lương</h3>

        <form action="{{ route('salary.config-global.save') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label class="form-label mb-3">Chọn hình thức tính lương mặc định cho toàn hệ thống:</label>
                
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" class="config-option {{ $salaryCalculationType == 'contract' ? 'active-option' : '' }}">
                        <input type="radio" name="salary_calculation_type" value="contract" style="margin-top: 4px;" 
                            {{ $salaryCalculationType == 'contract' ? 'checked' : '' }}>
                        <div>
                            <span style="display: block; font-weight: 600; color: #1f2937;">Tính theo hợp đồng</span>
                            <span style="display: block; font-size: 13px; color: #6b7280; margin-top: 4px;">– Dựa trên các khoản trong hợp đồng: lương cứng + phụ cấp - khấu trừ.</span>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; transition: all 0.2s;" class="config-option {{ $salaryCalculationType == 'attendance' ? 'active-option' : '' }}">
                        <input type="radio" name="salary_calculation_type" value="attendance" style="margin-top: 4px;"
                            {{ $salaryCalculationType == 'attendance' ? 'checked' : '' }}>
                        <div>
                            <span style="display: block; font-weight: 600; color: #1f2937;">Tính theo chấm công</span>
                            <span style="display: block; font-size: 13px; color: #6b7280; margin-top: 4px;">– Công thức: Lương cứng / số ngày công tháng * số công trong bảng chấm công của nhân viên trong tháng đó.</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu cấu hình
            </button>
        </form>
    </div>

    <div class="card" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #0BAA4B;">Tham số lương hệ thống</h3>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('paramForm').style.display = 'block'; this.style.display = 'none';">
                <i class="bi bi-plus-lg"></i> Thêm tham số
            </button>
        </div>

        <div id="paramForm" style="display: none; background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #e5e7eb;">
            <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 15px;">Thêm tham số lương mới</h4>
            <form action="{{ route('salary.config-global.save-params') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Ngày áp dụng</label>
                        <input type="text" name="NgayApDung" class="form-control datepicker" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mức lương cơ sở (VNĐ)</label>
                        <input type="number" name="MucLuongCoSo" class="form-control" placeholder="VD: 4350000" required>
                    </div>
                </div>
                <div style="margin-top: 16px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Lưu tham số</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('paramForm').style.display = 'none'; document.querySelector('.btn-sm').style.display = 'inline-flex';">Hủy</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table table-bordered table-hover" style="width: 100%;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 60px;">STT</th>
                        <th>Ngày áp dụng</th>
                        <th>Mức lương cơ sở</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @php $today = now()->format('Y-m-d'); @endphp
                    @forelse($thamSoLuongs as $index => $param)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $param->NgayApDung->format('d/m/Y') }}</td>
                            <td style="font-weight: 600; color: #0BAA4B;">{{ number_format($param->MucLuongCoSo, 0, ',', '.') }} VNĐ</td>
                            <td>
                                @if($param->NgayApDung->format('Y-m-d') <= $today && ($index == 0 || $thamSoLuongs[$index-1]->NgayApDung->format('Y-m-d') > $today))
                                    <span class="badge badge-success">Đang áp dụng</span>
                                @elseif($param->NgayApDung->format('Y-m-d') > $today)
                                    <span class="badge" style="background-color: #fef9c3; color: #854d0e;">Tương lai</span>
                                @else
                                    <span class="badge" style="background-color: #f3f4f6; color: #6b7280;">Lịch sử</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #6b7280; padding: 30px;">Chưa có tham số lương nào được cấu hình.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger"
            style="color: #dc2626; background-color: #fee2e2; padding: 12px; border-radius: 8px; margin-top: 16px;">
            {{ session('error') }}
        </div>
    @endif

    <style>
        .config-option:hover {
            border-color: #0BAA4B !important;
            background-color: #f9fafb;
        }
        .active-option {
            border-color: #0BAA4B !important;
            background-color: #f0fdf4 !important;
        }
        .config-option input[type="radio"]:checked + div span:first-child {
            color: #0BAA4B !important;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }

        /* Dark Mode Overrides */
        body.dark-theme .config-option {
            background-color: #1a1d27;
            border-color: #2e3349 !important;
        }
        body.dark-theme .config-option span[style*="color: #1f2937"] {
            color: #e8eaf0 !important;
        }
        body.dark-theme .config-option span[style*="color: #6b7280"] {
            color: #8b93a8 !important;
        }
        body.dark-theme .config-option:hover {
            background-color: #21263a !important;
        }
        body.dark-theme .active-option {
            border-color: #0BAA4B !important;
            background-color: rgba(11, 170, 75, 0.1) !important;
        }
        body.dark-theme #paramForm {
            background-color: #1a1d27 !important;
            border-color: #2e3349 !important;
        }
        body.dark-theme #paramForm h4 {
            color: #e8eaf0 !important;
        }
        body.dark-theme thead[style*="background-color: #f8f9fa"] {
            background-color: #21263a !important;
        }
        body.dark-theme th {
            color: #8b93a8 !important;
            border-color: #2e3349 !important;
        }
        body.dark-theme .table-container td {
            border-color: #2e3349 !important;
        }
        body.dark-theme .badge-success {
            background-color: rgba(11, 170, 75, 0.2);
            color: #0BAA4B;
        }
        body.dark-theme .alert-success[style*="background-color: #d1fae5"] {
            background-color: rgba(11, 170, 75, 0.1) !important;
            border: 1px solid #0BAA4B !important;
        }
        body.dark-theme .alert-danger[style*="background-color: #fee2e2"] {
            background-color: rgba(220, 38, 38, 0.1) !important;
            border: 1px solid #dc2626 !important;
        }
    </style>

    <script>
        document.querySelectorAll('input[name="salary_calculation_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.config-option').forEach(option => {
                    option.classList.remove('active-option');
                });
                if (this.checked) {
                    this.closest('.config-option').classList.add('active-option');
                }
            });
        });
    </script>
@endsection
