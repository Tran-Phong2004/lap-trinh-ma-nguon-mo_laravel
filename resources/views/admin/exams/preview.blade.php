<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem trước bài thi - {{ $exam->exam_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            color: #333;
            padding: 30px;
        }

        .preview-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }

        .preview-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .exam-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item i {
            font-size: 18px;
        }

        .preview-body {
            padding: 30px;
        }

        .exam-description {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .question-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .question-item {
            background: #f9fafb;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }

        .question-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .question-number {
            background: #667eea;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        .question-type {
            background: #10b981;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .question-text {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .answer-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .answer-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .answer-option.correct {
            background: #d1fae5;
            border-color: #10b981;
        }

        .answer-option .option-label {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .answer-option.correct .option-label {
            background: #10b981;
        }

        .answer-option .option-text {
            flex: 1;
        }

        .correct-indicator {
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-bar {
            padding: 20px 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .no-questions {
            text-align: center;
            padding: 60px 30px;
            color: #6b7280;
        }

        .no-questions i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .action-bar {
                display: none;
            }
            .preview-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1>{{ $exam->exam_name }}</h1>
            <div class="exam-info">
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span>Thời gian: {{ $exam->duration_minutes }} phút</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Số câu hỏi: {{ $exam->questions->count() }}</span>
                </div>
                @if($exam->start_time)
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span>Bắt đầu: {{ \Carbon\Carbon::parse($exam->start_time)->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                <div class="info-item">
                    <i class="fas fa-circle"></i>
                    <span>{{ $exam->is_active ? 'Kích hoạt' : 'Tạm dừng' }}</span>
                </div>
            </div>
        </div>

        <div class="preview-body">
            @if($exam->description)
            <div class="exam-description">
                <h3 style="margin-bottom: 10px;">Mô tả bài thi:</h3>
                <p>{{ $exam->description }}</p>
            </div>
            @endif

            @if($exam->questions->count() > 0)
            <div class="question-list">
                @foreach($exam->questions as $index => $question)
                <div class="question-item">
                    <div class="question-header">
                        <div class="question-number">Câu {{ $index + 1 }}</div>
                        <div class="question-type">{{ $question->type->name ?? 'N/A' }}</div>
                    </div>

                    <div class="question-text">
                        {{ $question->question_text }}
                    </div>

                    @if($question->answerOptions->count() > 0)
                    <div class="answer-options">
                        @foreach($question->answerOptions as $optionIndex => $option)
                        <div class="answer-option {{ $option->is_correct ? 'correct' : '' }}">
                            <div class="option-label">{{ chr(65 + $optionIndex) }}</div>
                            <div class="option-text">{{ $option->answer_text  }}</div>
                            @if($option->is_correct)
                            <div class="correct-indicator">
                                <i class="fas fa-check-circle"></i>
                                Đáp án đúng
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="no-questions">
                <i class="fas fa-inbox"></i>
                <p>Bài thi chưa có câu hỏi nào</p>
            </div>
            @endif
        </div>

        <div class="action-bar">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Chỉnh sửa
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i>
                    In bài thi
                </button>
            </div>
        </div>
    </div>
</body>
</html>