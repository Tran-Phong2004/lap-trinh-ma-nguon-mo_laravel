<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem lại bài làm - {{ $examSession->exam->exam_name }}</title>
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

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .back-button:hover {
            gap: 12px;
        }

        .result-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .result-header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 2px solid #e5e7eb;
        }

        .score-display {
            font-size: 72px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 20px 0;
        }

        .result-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 25px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 12px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-value.correct {
            color: #10b981;
        }

        .stat-value.incorrect {
            color: #ef4444;
        }

        .stat-value.unanswered {
            color: #f59e0b;
        }

        .stat-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        .questions-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .filter-btn.correct-filter.active {
            background: #10b981;
            border-color: #10b981;
        }

        .filter-btn.incorrect-filter.active {
            background: #ef4444;
            border-color: #ef4444;
        }

        .question-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
            border-left: 4px solid #e5e7eb;
            transition: all 0.3s;
        }

        .question-card.correct {
            border-left-color: #10b981;
        }

        .question-card.incorrect {
            border-left-color: #ef4444;
        }

        .question-card.unanswered {
            border-left-color: #f59e0b;
        }

        .question-card.hidden {
            display: none;
        }

        .question-header {
            padding: 20px 30px;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .question-number {
            font-weight: 700;
            font-size: 16px;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .question-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-correct {
            background: #d1fae5;
            color: #065f46;
        }

        .status-incorrect {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-unanswered {
            background: #fef3c7;
            color: #92400e;
        }

        .question-content {
            padding: 30px;
        }

        .question-text {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
            font-weight: 500;
            color: #1f2937;
        }

        .question-image {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .answers-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .answer-option {
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            background: white;
        }

        .answer-option.selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .answer-option.correct {
            border-color: #10b981;
            background: #d1fae5;
        }

        .answer-option.incorrect {
            border-color: #ef4444;
            background: #fee2e2;
        }

        .answer-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            background: #f3f4f6;
            color: #6b7280;
        }

        .answer-option.selected .answer-icon {
            background: #3b82f6;
            color: white;
        }

        .answer-option.correct .answer-icon {
            background: #10b981;
            color: white;
        }

        .answer-option.incorrect .answer-icon {
            background: #ef4444;
            color: white;
        }

        .answer-text {
            flex: 1;
            font-size: 15px;
            line-height: 1.5;
        }

        .answer-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .label-correct {
            color: #10b981;
        }

        .label-incorrect {
            color: #ef4444;
        }

        .label-selected {
            color: #3b82f6;
        }

        .explanation-box {
            margin-top: 20px;
            padding: 20px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
        }

        .explanation-box h4 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .explanation-box p {
            color: #78350f;
            font-size: 14px;
            line-height: 1.6;
        }

        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
            opacity: 0;
            visibility: hidden;
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .back-button,
            .questions-header,
            .scroll-to-top {
                display: none;
            }
            .question-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('student.exam-result', $examSession->id) }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Quay lại kết quả
        </a>

        <!-- Kết quả tổng quan -->
        <div class="result-card">
            <div class="result-header">
                <h1>{{ $examSession->exam->exam_name }}</h1>
                @if($latestResult)
                    <div class="score-display">{{ number_format($latestResult->score, 1) }}/10</div>
                    <p style="color: #6b7280; font-size: 16px;">
                        Hoàn thành lúc: {{ \Carbon\Carbon::parse($latestResult->submitted_at)->format('d/m/Y H:i:s') }}
                    </p>
                @endif
            </div>

            @if($latestResult)
                @php
                    $totalQuestions = $examSession->exam->questions->count();
                    $unanswered = $totalQuestions - $latestResult->correct_answers - $latestResult->wrong_answers;
                @endphp
                <div class="result-stats">
                    <div class="stat-item">
                        <div class="stat-value correct">
                            <i class="fas fa-check-circle"></i> {{ $latestResult->correct_answers }}
                        </div>
                        <div class="stat-label">Câu trả lời đúng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value incorrect">
                            <i class="fas fa-times-circle"></i> {{ $latestResult->wrong_answers }}
                        </div>
                        <div class="stat-label">Câu trả lời sai</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value unanswered">
                            <i class="fas fa-question-circle"></i> {{ $unanswered }}
                        </div>
                        <div class="stat-label">Chưa trả lời</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bộ lọc -->
        <div class="questions-header">
            <h2><i class="fas fa-list-ol"></i> Chi tiết câu hỏi</h2>
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="filterQuestions('all')">
                    <i class="fas fa-th"></i> Tất cả
                </button>
                <button class="filter-btn correct-filter" onclick="filterQuestions('correct')">
                    <i class="fas fa-check"></i> Câu đúng
                </button>
                <button class="filter-btn incorrect-filter" onclick="filterQuestions('incorrect')">
                    <i class="fas fa-times"></i> Câu sai
                </button>
                <button class="filter-btn" onclick="filterQuestions('unanswered')">
                    <i class="fas fa-question"></i> Chưa trả lời
                </button>
            </div>
        </div>

        <!-- Danh sách câu hỏi -->
        @foreach($examSession->exam->questions as $index => $question)
            @php
                $studentAnswer = $answersMap->get($question->id);
                $isAnswered = $studentAnswer !== null && $studentAnswer->selected_answer_id !== null;
                $isCorrect = $isAnswered && $studentAnswer->is_correct;
                $questionClass = !$isAnswered ? 'unanswered' : ($isCorrect ? 'correct' : 'incorrect');
                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
            @endphp

            <div class="question-card {{ $questionClass }}" data-status="{{ $questionClass }}">
                <div class="question-header">
                    <span class="question-number">
                        <i class="fas fa-question-circle"></i> Câu {{ $index + 1 }}
                    </span>
                    <span class="question-status status-{{ $questionClass }}">
                        @if(!$isAnswered)
                            <i class="fas fa-minus-circle"></i> Chưa trả lời
                        @elseif($isCorrect)
                            <i class="fas fa-check-circle"></i> Đúng
                        @else
                            <i class="fas fa-times-circle"></i> Sai
                        @endif
                    </span>
                </div>

                <div class="question-content">
                    <div class="question-text">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>

                    @if($question->image)
                        <img src="{{ asset('storage/' . $question->image) }}" 
                             alt="Question image" 
                             class="question-image">
                    @endif

                    <div class="answers-list">
                        @foreach($question->answerOptions as $optionIndex => $option)
                            @php
                                $isSelected = $isAnswered && $studentAnswer->selected_answer_id == $option->id;
                                $isCorrectAnswer = $option->is_correct;
                                
                                $optionClass = '';
                                if ($isCorrectAnswer) {
                                    $optionClass = 'correct';
                                } elseif ($isSelected && !$isCorrectAnswer) {
                                    $optionClass = 'incorrect';
                                }
                            @endphp

                            <div class="answer-option {{ $optionClass }}">
                                <div class="answer-icon">
                                    {{ $letters[$optionIndex] }}
                                </div>
                                <div class="answer-text">
                                    {!! nl2br(e($option->answer_text)) !!}
                                </div>
                                <div>
                                    @if($isCorrectAnswer)
                                        <span class="answer-label label-correct">
                                            <i class="fas fa-check"></i> Đáp án đúng
                                        </span>
                                    @endif
                                    @if($isSelected && !$isCorrectAnswer)
                                        <span class="answer-label label-incorrect">
                                            <i class="fas fa-times"></i> Bạn đã chọn
                                        </span>
                                    @elseif($isSelected && $isCorrectAnswer)
                                        <span class="answer-label label-correct">
                                            <i class="fas fa-check-double"></i> Bạn đã chọn đúng
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($question->explanation)
                        <div class="explanation-box">
                            <h4>
                                <i class="fas fa-lightbulb"></i> Giải thích
                            </h4>
                            <p>{!! nl2br(e($question->explanation)) !!}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <button class="scroll-to-top" id="scrollToTop" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>

    <script>
        // Filter questions
        function filterQuestions(filter) {
            const questions = document.querySelectorAll('.question-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            event.target.closest('.filter-btn').classList.add('active');
            
            questions.forEach(question => {
                const status = question.getAttribute('data-status');
                
                if (filter === 'all') {
                    question.classList.remove('hidden');
                } else if (filter === status) {
                    question.classList.remove('hidden');
                } else {
                    question.classList.add('hidden');
                }
            });
        }

        // Scroll to top button
        const scrollButton = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        });
        
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Auto scroll to first incorrect answer
        document.addEventListener('DOMContentLoaded', function() {
            const firstIncorrect = document.querySelector('.question-card.incorrect');
            if (firstIncorrect && !window.location.hash) {
                setTimeout(() => {
                    firstIncorrect.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }, 500);
            }
        });
    </script>
</body>
</html>