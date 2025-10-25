<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đổi mật khẩu - Web Thi Tiếng Anh</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }

        .header h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .password-input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            padding-right: 50px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #666;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success-message {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .field-error {
            color: #c33;
            font-size: 13px;
            margin-top: 5px;
        }

        .password-requirements {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }

        .password-requirements ul {
            margin: 8px 0 0 20px;
        }

        .password-requirements li {
            margin: 4px 0;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h2>Đổi mật khẩu</h2>
            <p>Vui lòng nhập mật khẩu hiện tại và mật khẩu mới</p>
        </div>

        @if ($errors->any())
        <div class="error-message">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('change-password') }}">
            @csrf

            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <div class="password-input-wrapper">
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           placeholder="Nhập mật khẩu hiện tại"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                        🙈
                    </button>
                </div>
                @error('current_password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <div class="password-input-wrapper">
                    <input type="password" 
                           id="new_password" 
                           name="new_password" 
                           placeholder="Nhập mật khẩu mới"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                        🙈
                    </button>
                </div>
                @error('new_password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                <div class="password-input-wrapper">
                    <input type="password" 
                           id="new_password_confirmation" 
                           name="new_password_confirmation" 
                           placeholder="Nhập lại mật khẩu mới"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password_confirmation')">
                        🙈
                    </button>
                </div>
            </div>

            <div class="password-requirements">
                <strong>Yêu cầu mật khẩu:</strong>
                <ul>
                    <li>Tối thiểu 8 ký tự</li>
                    <li>Không trùng với mật khẩu hiện tại</li>
                </ul>
            </div>

            <button type="submit" class="btn-submit">Đổi mật khẩu</button>
        </form>

        <div class="back-link">
            <a href="{{ Auth::user()->isAdmin() ? '/admin' : route('student.exam-sessions') }}">
                ← Quay lại
            </a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const button = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }
    </script>
</body>
</html>