<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Không có quyền truy cập</title>
    <link rel="icon" href="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}">
    <style>
        :root {
            --primary-color: #0BAA4B;
            --primary-hover: #0d7a3a;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-body: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 24px;
            letter-spacing: -2px;
            opacity: 0.1;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        .icon-wrapper {
            width: 120px;
            height: 120px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            box-shadow: 0 10px 25px rgba(11, 170, 75, 0.1);
            position: relative;
            overflow: hidden;
        }

        .icon-wrapper img {
            max-width: 80%;
            height: auto;
            object-fit: contain;
        }

        .icon-wrapper svg {
            width: 60px;
            height: 60px;
            color: var(--primary-color);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text-main);
        }

        p {
            color: var(--text-muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(11, 170, 75, 0.2);
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(11, 170, 75, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .nav-links {
            margin-top: 48px;
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .icon-wrapper {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <div class="error-code">403</div>

    <div class="container">
        <div class="icon-wrapper">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1>Truy cập bị từ chối</h1>
        <p>Rất tiếc, bạn không có quyền truy cập vào trang này. Vui lòng liên hệ với quản trị viên hệ thống nếu bạn tin
            rằng đây là một sự nhầm lẫn.</p>

        <a href="{{ route('dashboard') }}" class="btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Quay lại trang chủ
        </a>

        <div class="nav-links">
            <a href="javascript:history.back()">Quay lại trang trước</a>
            <a href="{{ route('logout') }}">Đăng xuất</a>
        </div>
    </div>
</body>

</html>
