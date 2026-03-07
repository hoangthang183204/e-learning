<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - E-Learning</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #0b5e42 0%, #0a7e5a 100%);
            padding: 20px;
            position: relative;
            overflow-y: auto; /* Cho phép cuộn khi nội dung dài */
        }

        body::before {
            content: '';
            position: fixed;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
            top: 0;
            left: 0;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 24px;
            padding: 40px 35px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideUp 0.5s ease;
            margin: 20px auto; /* Canh giữa và có khoảng cách trên dưới */
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo i {
            font-size: 48px;
            color: #0b5e42;
            background: #e8f5e9;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 30px;
            margin-bottom: 16px;
            box-shadow: 0 10px 20px rgba(11, 94, 66, 0.2);
        }

        .logo h2 {
            font-size: 28px;
            font-weight: 600;
            color: #1a2e3f;
            margin-bottom: 8px;
        }

        .logo p {
            color: #64748b;
            font-size: 14px;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #166534;
        }

        .alert i {
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s;
            z-index: 1;
        }

        .textarea-icon {
            top: 25px;
            transform: none;
        }

        input, textarea {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
        }

        textarea {
            padding-top: 16px;
            padding-bottom: 16px;
            resize: vertical;
            min-height: 80px;
            max-height: 150px;
        }

        input:focus, textarea:focus {
            border-color: #0b5e42;
            background: white;
            box-shadow: 0 0 0 4px rgba(11, 94, 66, 0.1);
        }

        input:focus + .input-icon,
        textarea:focus + .input-icon {
            color: #0b5e42;
        }

        input::placeholder, textarea::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .field-error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            margin-left: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-error i {
            font-size: 12px;
        }

        .password-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
            margin-left: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .password-hint i {
            color: #0b5e42;
            font-size: 12px;
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(145deg, #0b5e42, #0a7e5a);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(11, 94, 66, 0.3);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(11, 94, 66, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        button i {
            font-size: 18px;
        }

        .extra-links {
            margin-top: 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .login-link {
            background: #f8fafc;
            padding: 14px;
            border-radius: 16px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }

        .login-link:hover {
            border-color: #0b5e42;
            background: #f0fdf4;
        }

        .login-link a {
            color: #0b5e42 !important;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .login-link a i {
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px auto;
            }

            .logo i {
                width: 64px;
                height: 64px;
                font-size: 32px;
                line-height: 64px;
            }

            .logo h2 {
                font-size: 24px;
            }

            input, textarea {
                padding: 14px 16px 14px 46px;
                font-size: 14px;
            }

            .input-icon {
                left: 14px;
                font-size: 16px;
            }

            .textarea-icon {
                top: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Logo -->
        <div class="logo">
            <i class="fas fa-user-graduate"></i>
            <h2>Tạo tài khoản mới</h2>
            <p>Đăng ký để bắt đầu học tập</p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Vui lòng kiểm tra lại thông tin</span>
            </div>
        @endif

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Họ tên -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" 
                           name="name" 
                           placeholder="Họ và tên" 
                           value="{{ old('name') }}" 
                           required>
                </div>
                @error('name')
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" 
                           name="email" 
                           placeholder="Email" 
                           value="{{ old('email') }}" 
                           required>
                </div>
                @error('email')
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           placeholder="Mật khẩu" 
                           required>
                </div>
                <div class="password-hint">
                    <i class="fas fa-info-circle"></i>
                    Mật khẩu phải có ít nhất 8 ký tự
                </div>
                @error('password')
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="Xác nhận mật khẩu" 
                           required>
                </div>
            </div>

            <!-- Số điện thoại (tùy chọn) -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-phone input-icon"></i>
                    <input type="text" 
                           name="phone" 
                           placeholder="Số điện thoại (không bắt buộc)" 
                           value="{{ old('phone') }}">
                </div>
                @error('phone')
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Địa chỉ (tùy chọn) -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-map-marker-alt input-icon textarea-icon"></i>
                    <textarea name="address" 
                              placeholder="Địa chỉ (không bắt buộc)">{{ old('address') }}</textarea>
                </div>
                @error('address')
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit">
                <i class="fas fa-user-plus"></i>
                Đăng ký
            </button>
        </form>

        <!-- Extra Links -->
        <div class="extra-links">
            <div class="divider">
                <span class="divider-line"></span>
                <span>đã có tài khoản?</span>
                <span class="divider-line"></span>
            </div>

            <div class="login-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập ngay
                </a>
            </div>
        </div>
    </div>
</body>
</html>