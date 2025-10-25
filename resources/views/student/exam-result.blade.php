<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Thi - {{ $examSession->exam->exam_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #667eea;
            font-size: 24px;
        }

        .back-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .back-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Result Card */
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-bottom: 30px;
        }

        /* Score Circle */
        .score-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 30px;
        }

        .score-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(#28a745 0deg,
                    #28a745 calc(var(--score) * 3.6deg),
                    #e9ecef calc(var(--score) * 3.6deg),
                    #e9ecef 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: fillCircle 1.5s ease-out;
        }

        @keyframes fillCircle {
            from {
                transform: rotate(0deg);
                opacity: 0;
            }

            to {
                transform: rotate(360deg);
                opacity: 1;
            }
        }

        .score-inner {
            width: 85%;
            height: 85%;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-value {
            font-size: 48px;
            font-weight: bold;
            color: #28a745;
            line-height: 1;
        }

        .score-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Score variations */
        .score-circle.excellent {
            background: conic-gradient(#28a745 0deg,
                    #28a745 calc(var(--score) * 3.6deg),
                    #e9ecef calc(var(--score) * 3.6deg),
                    #e9ecef 360deg);
        }

        .score-circle.good {
            background: conic-gradient(#17a2b8 0deg,
                    #17a2b8 calc(var(--score) * 3.6deg),
                    #e9ecef calc(var(--score) * 3.6deg),
                    #e9ecef 360deg);
        }

        .score-circle.average {
            background: conic-gradient(#ffc107 0deg,
                    #ffc107 calc(var(--score) * 3.6deg),
                    #e9ecef calc(var(--score) * 3.6deg),
                    #e9ecef 360deg);
        }

        .score-circle.poor {
            background: conic-gradient(#dc3545 0deg,
                    #dc3545 calc(var(--score) * 3.6deg),
                    #e9ecef calc(var(--score) * 3.6deg),
                    #e9ecef 360deg);
        }

        .score-circle.excellent .score-value {
            color: #28a745;
        }

        .score-circle.good .score-value {
            color: #17a2b8;
        }

        .score-circle.average .score-value {
            color: #ffc107;
        }

        .score-circle.poor .score-value {
            color: #dc3545;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .status-badge.excellent {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.average {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.poor {
            background: #f8d7da;
            color: #721c24;
        }

        .result-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .stat-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .stat-box.correct {
            border-left-color: #28a745;
        }

        .stat-box.wrong {
            border-left-color: #dc3545;
        }

        .stat-box.total {
            border-left-color: #667eea;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .stat-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        /* Exam Info */
        .exam-info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .exam-info-card h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        /* History Table */
        .history-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .history-card h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .history-table tr:last-child td {
            border-bottom: none;
        }

        .history-table tr:hover {
            background: #f8f9fa;
        }

        .attempt-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .attempt-badge.current {
            background: #667eea;
            color: white;
        }

        .attempt-badge.previous {
            background: #e9ecef;
            color: #666;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Kết Quả Thi</h1>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('student.review-exam', $examSession->id) }}" class="back-btn"
                    style="background: #17a2b8;">
                    👁️ Xem lại bài làm
                </a>
                <a href="{{ route('student.exam-sessions') }}" class="back-btn">
                    ← Về danh sách
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($latestResult)
            <!-- Result Card -->
            <div class="result-card">
                @php
                    $score = round($latestResult->score, 1);
                    $scoreClass = 'poor';
                    $statusText = 'Cần cố gắng hơn';
                    $statusEmoji = '😢';

                        if ($score >= 8) {
                            $scoreClass = 'excellent';
                            $statusText = 'Xuất sắc';
                            $statusEmoji = '🎉';
                        } elseif ($score >= 6.5) {
                            $scoreClass = 'good';
                            $statusText = 'Tốt';
                            $statusEmoji = '😊';
                        } elseif ($score >= 5) {
                            $scoreClass = 'average';
                            $statusText = 'Trung bình';
                            $statusEmoji = '😐';
                        }
                        // Tính phần trăm cho hiển thị circle (điểm/10 * 100)
                        $scorePercentage = ($score / 10) * 100;
                @endphp

                <div class="status-badge {{ $scoreClass }}">
                    {{ $statusEmoji }} {{ $statusText }}
                </div>

                <h2 class="result-title">{{ $examSession->exam->exam_name }}</h2>

                <div class="score-container">
                    <div class="score-circle {{ $scoreClass }}"  style="--score: {{ $scorePercentage }}">
                        <div class="score-inner">
                            <div class="score-value">{{ $score }}</div>
                            <div class="score-label">điểm</div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-box correct">
                        <div class="stat-icon">✅</div>
                        <div class="stat-label">Đúng</div>
                        <div class="stat-value">{{ $latestResult->correct_answers }}</div>
                    </div>
                    <div class="stat-box wrong">
                        <div class="stat-icon">❌</div>
                        <div class="stat-label">Sai</div>
                        <div class="stat-value">{{ $latestResult->wrong_answers }}</div>
                    </div>
                    <div class="stat-box total">
                        <div class="stat-icon">📝</div>
                        <div class="stat-label">Tổng số</div>
                        <div class="stat-value">{{ $latestResult->correct_answers + $latestResult->wrong_answers }}</div>
                    </div>
                </div>
            </div>

            <!-- Exam Info -->
            <div class="exam-info-card">
                <h2>📋 Thông Tin Chi Tiết</h2>
                <div class="info-row">
                    <span class="info-label">⏰ Thời gian nộp bài:</span>
                    <span class="info-value">{{ $latestResult->submitted_at->format('H:i - d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🎯 Số lần thi:</span>
                    <span class="info-value">{{ $examSession->results()->count() }} /
                        {{ $examSession->max_attempts }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📊 Điểm số:</span>
                    <span class="info-value">{{ $score }}/10</span>
                </div>
                <div class="info-row">
                    <span class="info-label">✅ Số câu đúng:</span>
                    <span class="info-value">{{ $latestResult->correct_answers }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">❌ Số câu sai:</span>
                    <span class="info-value">{{ $latestResult->wrong_answers }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🔄 Lượt thi còn lại:</span>
                    <span class="info-value">
                        {{ max(0, $examSession->max_attempts - $examSession->results()->count()) }}
                    </span>
                </div>
            </div>

            <!-- History -->
            @if($examSession->results()->count() > 1)
                <div class="history-card">
                    <h2>📜 Lịch Sử Làm Bài</h2>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Lần thi</th>
                                <th>Điểm số</th>
                                <th>Đúng/Sai</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examSession->results()->orderByDesc('submitted_at')->get() as $index => $result)
                                <tr>
                                    <td>
                                        <span class="attempt-badge {{ $index === 0 ? 'current' : 'previous' }}">
                                            Lần {{ $examSession->results()->count() - $index }}
                                            @if($index === 0) (Mới nhất) @endif
                                        </span>
                                    </td>
                                    <td>
                                        <strong style="color: {{ $result->score >= 8 ? '#28a745' : ($result->score >= 5 ? '#ffc107' : '#dc3545') }}">
                                            {{ round($result->score, 1) }}/10
                                        </strong>
                                    </td>
                                    <td>
                                        <span style="color: #28a745">{{ $result->correct_answers }}</span> /
                                        <span style="color: #dc3545">{{ $result->wrong_answers }}</span>
                                    </td>
                                    <td>{{ $result->submitted_at->format('H:i d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($examSession->results()->count() < $examSession->max_attempts)
                <div style="margin-top: 20px; text-align: center;">
                    <form action="{{ route('student.start-exam', $examSession->id) }}" method="POST"
                        style="display: inline-block;">
                        @csrf
                        <button type="submit" class="back-btn"
                            style="background: #28a745; padding: 15px 40px; font-size: 16px;">
                            🔄 Thi lại (Còn {{ $examSession->max_attempts - $examSession->results()->count() }} lượt)
                        </button>
                    </form>
                </div>
            @endif

        @else
            <div class="result-card">
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h2>Chưa có kết quả</h2>
                    <p>Bạn chưa có kết quả thi nào cho phiên thi này.</p>
                </div>
            </div>
        @endif
    </div>
</body>

</html>