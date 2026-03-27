@extends('layouts.app')

@section('title', 'Import nhân viên - Vietnam Rubber Group')

@section('content')
    <div class="page-header">
        <h1>Import nhân viên</h1>
        <p>Thêm nhân viên hàng loạt từ file Excel</p>
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
                Đã import thành công {{ session('import_success_count') }} nhân viên.
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger"
                style="margin-bottom: 24px; padding: 16px; background-color: #fee2e2; color: #991b1b; border-radius: 8px;">
                <div style="font-weight: 600; margin-bottom: 8px;">
                    <svg style="width: 20px; height: 20px; display: inline-block; margin-right: 8px; vertical-align: text-bottom;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Có lỗi xảy ra:
                </div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Detail Import Errors -->
        @if(session('import_errors'))
            <div class="alert alert-warning"
                style="margin-bottom: 24px; padding: 16px; background-color: #fef9c3; color: #854d0e; border-radius: 8px;">
                <div style="font-weight: 600; margin-bottom: 8px;">
                    <svg style="width: 20px; height: 20px; display: inline-block; margin-right: 8px; vertical-align: text-bottom;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Một số dòng dữ liệu có lỗi và đã bị bỏ qua:
                </div>
                <ul style="margin: 0; padding-left: 20px; max-height: 250px; overflow-y: auto;">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('nhan-vien.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" style="font-weight: 600; margin-bottom: 8px; display: block;">Chọn file Excel tập
                    tin đầu vào <span style="color: #dc2626;">*</span></label>

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
                style="margin-top: 24px; padding: 16px; background-color: #f3f4f6; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="font-size: 14px; font-weight: 600; color: #1e3a8a; margin: 0;">Lưu ý cấu trúc file:</h4>
                    <a href="{{ asset('Folder/Template Import Employee 3W.xlsx') }}" download class="btn btn-primary" style="padding: 6px 12px; font-size: 13px; height: auto;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Tải file mẫu Excel
                    </a>
                </div>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- Cột A: STT</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- Cột B-H: Thông tin cá
                    nhân (Mã, Tên, Căn cước, ...)</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5; margin-bottom: 4px;">- Cột I-K: Thông tin công
                    tác (Phòng ban, Chức vụ, Ngày vào làm)</p>
                <p style="font-size: 13px; color: #4b5563; line-height: 1.5;">- Dòng đầu tiên cần được sử dụng làm Tiêu đề
                    (Header). Import sẽ tự động bỏ qua dòng này.</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button id="submitBtn" type="submit" class="btn btn-primary" disabled
                    style="opacity: 0.6; cursor: not-allowed;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Bắt đầu Import
                </button>
                <a href="{{ route('nhan-vien.danh-sach') }}" class="btn btn-secondary">Hủy bỏ</a>
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
                    dropZone.style.borderColor = '#0BAA4B';
                    dropZone.style.backgroundColor = 'rgba(15, 81, 50, 0.05)';
                } else {
                    fileNameDisplay.textContent = '';
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                    dropZone.style.borderColor = '#d1d5db';
                    dropZone.style.backgroundColor = '#f9fafb';
                }
            });

            // Add drag and drop logic
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropZone.style.borderColor = '#0BAA4B';
                dropZone.style.backgroundColor = 'rgba(15, 81, 50, 0.05)';
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

                    // Manually trigger change event
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            });
        });
    </script>
@endpush
