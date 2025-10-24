<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .email-body p {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .credentials-box {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-box .label {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 5px;
        }
        .credentials-box .value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
            padding: 10px;
            background: white;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
        }
        .email-footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Web Thi Tiếng Anh</h1>
        </div>
        
        <div class="email-body">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            
            <p>Tài khoản của bạn đã được tạo thành công trên hệ thống Web Thi Tiếng Anh.</p>
            
            <div class="credentials-box">
                <div class="label">Email đăng nhập:</div>
                <div class="value">{{ $user->email }}</div>
                
                <div class="label">Mật khẩu:</div>
                <div class="value">{{ $password }}</div>
                
                <div class="label">Vai trò:</div>
                <div class="value">{{ ucfirst($user->role->name) }}</div>
            </div>
            
            <div class="warning-box">
                <p><strong>Lưu ý quan trọng:</strong></p>
                <p>Vui lòng đổi mật khẩu ngay sau lần đăng nhập đầu tiên để bảo mật tài khoản của bạn.</p>
            </div>
            
            <center>
                <a href="{{ url('/login') }}" class="login-button">Đăng nhập ngay</a>
            </center>
            
            <p style="margin-top: 30px;">Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với quản trị viên.</p>
            
            <p>Trân trọng,<br>
            <strong>Đội ngũ Web Thi Tiếng Anh</strong></p>
        </div>
        
        <div class="email-footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>