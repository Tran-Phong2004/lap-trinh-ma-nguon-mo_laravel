<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiên Thi Của Tôi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #667eea;
            font-size: 28px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logout-btn {
            background: #ff4757;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: #ee5a6f;
            transform: translateY(-2px);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        .exam-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .exam-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .exam-info {
            margin-bottom: 12px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .exam-info strong {
            color: #333;
            min-width: 100px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
        }
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        .status-waiting {
            background: #fff3cd;
            color: #856404;
        }
        .status-expired {
            background: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .start-btn {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .start-btn:hover:not(:disabled) {
            background: #5568d3;
            transform: scale(1.02);
        }
        .start-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .empty-state {
            background: white;
            padding: 60px 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .empty-state h2 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .empty-state p {
            color: #666;
        }
        .change-password-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-block;
        }
        .change-password-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Phiên Thi Của Tôi</h1>
            <div class="user-info">
                <span>Xin chào, <strong>{{ Auth::user()->name }}</strong></span>
                <a href="{{ route('change-password.form') }}" class="change-password-btn">🔐 Đổi mật khẩu</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">Đăng xuất</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($examSessions->isEmpty())
            <div class="empty-state">
                <h2>Chưa có phiên thi nào</h2>
                <p>Hiện tại bạn chưa được gán vào phiên thi nào. Vui lòng liên hệ giáo viên để biết thêm chi tiết.</p>
            </div>
        @else
            <div class="exam-grid">
                @foreach($examSessions as $session)
                    <div class="exam-card">
                        <h2 class="exam-title">{{ $session->exam->exam_name }}</h2>
                        
                        @if($session->exam->description)
                            <p style="color: #666; margin-bottom: 15px;">{{ $session->exam->description }}</p>
                        @endif

                        <div class="exam-info">
                            <strong>⏱️ Thời lượng:</strong>
                            <span>{{ $session->exam->duration_minutes }} phút</span>
                        </div>

                        <div class="exam-info">
                            <strong>📅 Bắt đầu:</strong>
                            <span>{{ $session->exam->start_time->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="exam-info">
                            <strong>🏁 Kết thúc:</strong>
                            <span>{{ $session->exam->end_time->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="exam-info">
                            <strong>🎯 Số lần thi:</strong>
                            <span>{{ $session->max_attempts }}</span>
                        </div>

                        @if(isset($session->attempts_left))
                            <div class="exam-info">
                                <strong>✅ Lượt còn lại:</strong>
                                <span>{{ $session->attempts_left }}</span>
                            </div>
                        @endif

                        @if($session->can_start)
                            <span class="status-badge status-available">✅ {{ $session->status_message }}</span>
                            <form action="{{ route('student.start-exam', $session->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="start-btn">Bắt đầu làm bài</button>
                            </form>
                        @else
                            @if($session->status_message == 'Chưa đến giờ thi')
                                <span class="status-badge status-waiting">⏳ {{ $session->status_message }}</span>
                                @if(isset($session->time_to_start))
                                    <p style="color: #856404; margin-top: 10px; font-size: 13px;">
                                        Bắt đầu {{ $session->time_to_start }}
                                    </p>
                                @endif
                            @elseif($session->status_message == 'Đã hết lượt thi')
                                <span class="status-badge status-completed">🎓 {{ $session->status_message }}</span>
                            @else
                                <span class="status-badge status-expired">❌ {{ $session->status_message }}</span>
                            @endif
                            <button type="button" class="start-btn" disabled>Không thể thi</button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>