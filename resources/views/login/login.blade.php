<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Hệ Thống Quản Lý Nhân Sự</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0BAA4B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin-bottom: 20px;
        }

        .login-header {
            background: #ffffff;
            padding: 40px 30px 10px 30px;
            text-align: center;
            color: #0BAA4B;
        }

        .login-header h1 {
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .login-logo {
            width: 100%;
            max-width: 150px;
            height: auto;
            max-height: 100px;
            margin-bottom: 20px;
            object-fit: contain;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .company-name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            color: #666;
        }

        .login-body {
            padding: 30px 40px 40px 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #0BAA4B;
            box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0BAA4B;
        }

        .remember-me label {
            cursor: pointer;
            color: #666;
            user-select: none;
        }

        .forgot-password {
            color: #0BAA4B;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #088c3d;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0BAA4B;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #088c3d;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 170, 75, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fee;
            border-left: 4px solid #dc3545;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #721c24;
            font-size: 14px;
        }

        .login-footer {
            text-align: center;
            padding: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .login-container {
                border-radius: 12px;
            }

            .login-header {
                padding: 30px 20px 5px 20px;
            }

            .login-body {
                padding: 20px 25px 30px 25px;
            }

            .login-logo {
                width: 80px;
                height: 80px;
            }

            .login-header h1 {
                font-size: 18px;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset(\App\Models\SystemConfig::getValue('company_logo', 'logo_triwin.png')) }}" alt="Logo" class="login-logo">
            <div class="company-name" style="color: #666; font-size: 14px; margin-bottom: 5px;">{{ \App\Models\SystemConfig::getValue('company_name', 'TRIWIN CO., LTD.') }}</div>
            <h1>Hệ Thống Quản Lý Nhân Sự</h1>
            <p style="color: #666;">Đăng nhập để tiếp tục</p>
        </div>

        <div class="login-body">
            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="TaiKhoan">Tài khoản</label>
                    <input type="text" id="TaiKhoan" name="TaiKhoan" value="{{ old('TaiKhoan') }}"
                        placeholder="Nhập tài khoản" required autofocus>
                </div>

                <div class="form-group">
                    <label for="MatKhau">Mật khẩu</label>
                    <input type="password" id="MatKhau" name="MatKhau" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="#" class="forgot-password">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login">Đăng Nhập</button>
            </form>
        </div>
    </div>
    <div class="login-footer">
        &copy; {{ date('Y') }} Triwin. All rights reserved.
    </div>
</body>

</html>
