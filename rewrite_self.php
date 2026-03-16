<?php
$content = <<<'EOD'
@extends('layouts.app')

@section('title', 'Đăng ký tăng ca cá nhân')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 24px;">
    <div class="page-header" style="margin-bottom: 32px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 8px;">Đăng ký tăng ca cá nhân</h1>
        <p style="color: #6b7280; font-size: 16px;">Gửi yêu cầu và theo dõi trạng thái các đơn tăng ca của bạn</p>
    </div>

    @if(isset($error))
        <div class="card" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; color: #dc2626;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span style="font-weight: 500;">{{ $error }}</span>
            </div>
        </div>
    @else
        <!-- Form and List logic would go here -->
        <div class="card" style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <p>Hồ sơ của bạn: <strong>{{ $nhanVien->Ten }}</strong> ({{ $nhanVien->Ma }})</p>
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <!-- Placeholder for actual content -->
            <p>Tính năng này đang được bảo trì phần giao diện. Vui lòng quay lại sau.</p>
        </div>
    @endif
</div>
@endsection
EOD;

file_put_contents('c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\self.blade.php', $content);
echo "Successfully rewrote self.blade.php\n";
