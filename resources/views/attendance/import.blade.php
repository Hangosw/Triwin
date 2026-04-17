@extends('layouts.app')

@section('title', 'Import chấm công - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header">
        <h1>Import chấm công</h1>
        <p>Thêm dữ liệu chấm công hàng loạt từ file Excel</p>
    </div>

    <div class="card" style="max-width: 800px;">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success"
                style="margin-bottom: 24px; padding: 12px 16px; background-color: #d1fae5; color: #065f46; border-radius: 8px;">
                <svg style="width: 20px; height: 20px; display: inline-block; margin-right: 8px; vertical-align: text-bottom;"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('import_success_count'))
            <div class="alert alert-success"
                style="margin-bottom: 24px; padding: 12px 16px; background-color: #d1fae5; color: #065f46; border-radius: 8px;">
                <svg style="width: 20px; height: 20px; display: inline-block; margin-right: 8px; vertical-align: text-bottom;"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Đã import thành công {{ session('import_success_count') }} bản ghi chấm công.
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"
                style="margin-bottom: 24px; padding: 16px; background-color: #fee2e2; color: #991b1b; border-radius: 8px;">
                <div style="font-weight: 600; margin-bottom: 8px;">Có lỗi xảy ra:</div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('import_errors'))
            <div class="alert alert-warning"
                style="margin-bottom: 24px; padding: 16px; background-color: #fef9c3; color: #854d0e; border-radius: 8px;">
                <div style="font-weight: 600; margin-bottom: 8px;">Một số dòng dữ liệu có lỗi và đã bị bỏ qua:</div>
                <ul style="margin: 0; padding-left: 20px; max-height: 250px; overflow-y: auto;">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger"
                style="margin-bottom: 24px; padding: 12px 16px; background-color: #fee2e2; color: #991b1b; border-radius: 8px;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('cham-cong.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" style="font-weight: 600; margin-bottom: 8px; display: block;">Chọn file Excel
                    <span style="color: #dc2626;">*</span></label>

                <div style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 40px; text-align: center; background-color: #f9fafb; cursor: pointer; transition: all 0.2s;"
                    id="drop-zone">
                    <svg style="width: 48px; height: 48px; color: #9ca3af; margin: 0 auto 16px;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p style="text-align: center; color: #4b5563; font-size: 16px; margin-bottom: 8px;">Kéo thả file vào đây
                        hoặc <span style="color: #0BAA4B; font-weight: 500;">nhấp chuột để chọn</span></p>
                    <p style="text-align: center; color: #6b7280; font-size: 14px; margin-bottom: 24px;">Hỗ trợ định dạng:
                        .xlsx, .xls</p>

                    <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" class="form-control"
                        style="display: none;" required>

                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('file').click()">
                        Duyệt file
                    </button>

                    <div id="file-name" style="margin-top: 16px; font-weight: 500; color: #0BAA4B;"></div>
                </div>
            </div>

            <div
                style="margin-top: 24px; padding: 16px; background-color: #f3f4f6; border-radius: 8px; border-left: 4px solid #10b981;">
                <h4 style="font-size: 14px; font-weight: 600; color: #065f46; margin-bottom: 8px;">Lưu ý cấu trúc file chấm
                    công:</h4>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- <strong>Hàng
                        11</strong>: Chứa tiêu đề ngày (VD: 2025-10-01, 2025-10-02, ...). Mỗi ngày chiếm 2 cột.</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- <strong>Hàng 12 trở
                        xuống</strong>: Dữ liệu từng nhân viên.</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- <strong>Cột E</strong>:
                    MSNV (Mã số nhân viên) — dùng để tra cứu trong hệ thống.</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- <strong>Từ cột F trở về
                        sau</strong>: Cột lẻ = Giờ vào, cột chẵn liền kề = Giờ ra. Dấu "-" hoặc ô trống = không có dữ liệu.
                </p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- Trạng thái chỉ được tính
                    khi có <strong>cả giờ vào và giờ ra</strong>, so sánh với Ca Hành Chính (HC).</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5;">- Nếu nhân viên đã có chấm công ngày đó, bản
                    ghi cũ sẽ <strong>bị ghi đè</strong>.</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button id="submitBtn" type="submit" class="btn btn-success" disabled
                    style="opacity: 0.6; cursor: not-allowed; background-color: #10b981; border-color: #10b981; color: white;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Bắt đầu Import
                </button>
                <a href="{{ route('cham-cong.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('file');
            const fileNameDisplay = document.getElementById('file-name');
            const submitBtn = document.getElementById('submitBtn');
            const dropZone = document.getElementById('drop-zone');

            fileInput.addEventListener('change', function (e) {
                if (this.files && this.files.length > 0) {
                    fileNameDisplay.textContent = 'File đã chọn: ' + this.files[0].name;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    dropZone.style.borderColor = '#10b981';
                    dropZone.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
                } else {
                    fileNameDisplay.textContent = '';
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                    dropZone.style.borderColor = '#d1d5db';
                    dropZone.style.backgroundColor = '#f9fafb';
                }
            });

            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropZone.style.borderColor = '#10b981';
                dropZone.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
            });

            dropZone.addEventListener('dragleave', function (e) {
                e.preventDefault();
                if (!fileInput.files.length) {
                    dropZone.style.borderColor = '#d1d5db';
                    dropZone.style.backgroundColor = '#f9fafb';
                }
            });

            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            });
        });
    </script>
@endpush
