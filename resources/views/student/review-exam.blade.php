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
        }

        .header-bar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: #eff6ff;
            gap: 12px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-print {
            padding: 10px 20px;
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-print:hover {
            background: #667eea;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .result-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .result-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .result-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .score-display {
            font-size: 64px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 20px 0;
            display: inline-block;
        }

        .result-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .stat-value.correct { color: #10b981; }
        .stat-value.incorrect { color: #ef4444; }
        .stat-value.unanswered { color: #f59e0b; }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
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
            flex-wrap: wrap;
            gap: 15px;
        }

        .questions-header h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            color: #1f2937;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 18px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover {
            border-color: #667eea;
            background: #f9fafb;
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

        .filter-btn.unanswered-filter.active {
            background: #f59e0b;
            border-color: #f59e0b;
        }

        .question-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
            border-left: 5px solid #e5e7eb;
            transition: all 0.3s;
        }

        .question-card.correct { border-left-color: #10b981; }
        .question-card.incorrect { border-left-color: #ef4444; }
        .question-card.unanswered { border-left-color: #f59e0b; }

        .question-card.hidden { display: none; }

        .question-header {
            padding: 20px 30px;
            background: linear-gradient(to right, #f9fafb, white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e5e7eb;
        }

        .question-number {
            font-weight: 700;
            font-size: 18px;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .question-type-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-multiple-choice {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-multiple-answer {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-fill-blank {
            background: #d1fae5;
            color: #065f46;
        }

        .question-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 14px;
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
            font-size: 17px;
            line-height: 1.7;
            margin-bottom: 25px;
            font-weight: 500;
            color: #1f2937;
        }

        .question-image {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .answers-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .answer-option {
            padding: 18px 24px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            background: white;
        }

        .answer-option.student-selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .answer-option.correct-answer {
            border-color: #10b981;
            background: #d1fae5;
        }

        .answer-option.wrong-answer {
            border-color: #ef4444;
            background: #fee2e2;
        }

        .checkbox-indicator {
            width: 28px;
            height: 28px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .answer-option.student-selected .checkbox-indicator {
            background: #3b82f6;
            border-color: #3b82f6;
        }

        .answer-option.correct-answer .checkbox-indicator {
            background: #10b981;
            border-color: #10b981;
        }

        .answer-option.wrong-answer .checkbox-indicator {
            background: #ef4444;
            border-color: #ef4444;
        }

        .checkbox-indicator i {
            color: white;
            font-size: 14px;
        }

        .answer-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 15px;
        }

        .answer-option.student-selected .answer-icon {
            background: #3b82f6;
            color: white;
        }

        .answer-option.correct-answer .answer-icon {
            background: #10b981;
            color: white;
        }

        .answer-option.wrong-answer .answer-icon {
            background: #ef4444;
            color: white;
        }

        .answer-text {
            flex: 1;
            font-size: 15px;
            line-height: 1.6;
        }

        .answer-labels {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-end;
        }

        .answer-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .label-correct {
            background: #10b981;
            color: white;
        }

        .label-wrong {
            background: #ef4444;
            color: white;
        }

        .label-selected {
            background: #3b82f6;
            color: white;
        }

        /* Fill blank styles */
        .fill-blank-answer {
            margin-bottom: 20px;
        }

        .answer-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .answer-box {
            padding: 16px 20px;
            border-radius: 12px;
            border: 2px solid;
        }

        .answer-box.student-answer {
            background: #eff6ff;
            border-color: #3b82f6;
        }

        .answer-box.student-answer.correct {
            background: #d1fae5;
            border-color: #10b981;
        }

        .answer-box.student-answer.incorrect {
            background: #fee2e2;
            border-color: #ef4444;
        }

        .answer-box.correct-answer {
            background: #d1fae5;
            border-color: #10b981;
        }

        .answer-box-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .answer-box-text {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
        }

        .no-answer {
            color: #9ca3af;
            font-style: italic;
        }

        .explanation-box {
            margin-top: 25px;
            padding: 20px 24px;
            background: linear-gradient(to right, #fef3c7, #fef9e7);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
        }

        .explanation-box h4 {
            color: #92400e;
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .explanation-box p {
            color: #78350f;
            font-size: 14px;
            line-height: 1.7;
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
            transform: scale(1.1) rotate(360deg);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        @media (max-width: 768px) {
            .answer-comparison {
                grid-template-columns: 1fr;
            }

            .result-stats {
                grid-template-columns: 1fr;
            }

            .questions-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .header-bar,
            .back-button,
            .header-actions,
            .questions-header,
            .scroll-to-top {
                display: none;
            }
            .question-card {
                page-break-inside: avoid;
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
        }
    </style>
</head>
<body>
    <div class="header-bar">
        <div class="header-content">
            <a href="{{ route('student.exam-result', $examSession->id) }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Quay lại kết quả
            </a>
            <div class="header-actions">
                <button class="btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> In bài làm
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Kết quả tổng quan -->
        <div class="result-card">
            <div class="result-header">
                <h1>{{ $examSession->exam->exam_name }}</h1>
                @if($latestResult)
                    <div class="score-display">{{ number_format($latestResult->score, 1) }}/10</div>
                    <p style="color: #6b7280; font-size: 16px;">
                        <i class="far fa-calendar-check"></i>
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
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $latestResult->correct_answers }}</span>
                        </div>
                        <div class="stat-label">Câu trả lời đúng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value incorrect">
                            <i class="fas fa-times-circle"></i>
                            <span>{{ $latestResult->wrong_answers }}</span>
                        </div>
                        <div class="stat-label">Câu trả lời sai</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value unanswered">
                            <i class="fas fa-question-circle"></i>
                            <span>{{ $unanswered }}</span>
                        </div>
                        <div class="stat-label">Chưa trả lời</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #667eea;">
                            <i class="fas fa-percentage"></i>
                            <span>{{ $totalQuestions > 0 ? number_format(($latestResult->correct_answers / $totalQuestions) * 100, 1) : 0 }}%</span>
                        </div>
                        <div class="stat-label">Tỷ lệ đúng</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bộ lọc -->
        <div class="questions-header">
            <h2>
                <i class="fas fa-list-ol"></i> 
                Chi tiết {{ $examSession->exam->questions->count() }} câu hỏi
            </h2>
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
                <button class="filter-btn unanswered-filter" onclick="filterQuestions('unanswered')">
                    <i class="fas fa-question"></i> Chưa trả lời
                </button>
            </div>
        </div>

        <!-- Danh sách câu hỏi -->
        @foreach($examSession->exam->questions as $index => $question)
            @php
                $studentAnswer = $answersMap->get($question->id);
                $questionType = $question->type->name;
                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                
                // Xác định trạng thái câu hỏi
                $isAnswered = false;
                $isCorrect = false;
                
                if ($studentAnswer) {
                    if ($questionType === 'multiple_choice') {
                        $isAnswered = $studentAnswer->selected_answer_id !== null;
                    } elseif ($questionType === 'multiple_answer') {
                        $isAnswered = !empty($studentAnswer->selected_answer_ids);
                    } elseif ($questionType === 'fill_blank') {
                        $isAnswered = !empty(trim($studentAnswer->text_answer ?? ''));
                    }
                    $isCorrect = $studentAnswer->is_correct ?? false;
                }
                
                $questionClass = !$isAnswered ? 'unanswered' : ($isCorrect ? 'correct' : 'incorrect');
            @endphp

            <div class="question-card {{ $questionClass }}" data-status="{{ $questionClass }}">
                <div class="question-header">
                    <div class="question-number">
                        <i class="fas fa-question-circle"></i> 
                        <span>Câu {{ $index + 1 }}</span>
                        
                        @if($questionType === 'multiple_choice')
                            <span class="question-type-badge badge-multiple-choice">Một đáp án</span>
                        @elseif($questionType === 'multiple_answer')
                            <span class="question-type-badge badge-multiple-answer">Nhiều đáp án</span>
                        @elseif($questionType === 'fill_blank')
                            <span class="question-type-badge badge-fill-blank">Điền vào chỗ trống</span>
                        @endif
                    </div>
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

                    {{-- Multiple Choice & Multiple Answer --}}
                    @if($questionType === 'multiple_choice' || $questionType === 'multiple_answer')
                        @php
                            $selectedIds = [];
                            if ($studentAnswer) {
                                if ($questionType === 'multiple_choice') {
                                    $selectedIds = $studentAnswer->selected_answer_id ? [$studentAnswer->selected_answer_id] : [];
                                } else {
                                    $selectedIds = $studentAnswer->selected_answer_ids ?? [];
                                }
                            }
                        @endphp

                        <div class="answers-list">
                            @foreach($question->answerOptions as $optionIndex => $option)
                                @php
                                    $isSelectedByStudent = in_array($option->id, $selectedIds);
                                    $isCorrectAnswer = $option->is_correct;
                                    
                                    $optionClass = '';
                                    if ($isCorrectAnswer && $isSelectedByStudent) {
                                        $optionClass = 'correct-answer';
                                    } elseif ($isCorrectAnswer && !$isSelectedByStudent) {
                                        $optionClass = 'correct-answer';
                                    } elseif ($isSelectedByStudent && !$isCorrectAnswer) {
                                        $optionClass = 'wrong-answer';
                                    } elseif ($isSelectedByStudent) {
                                        $optionClass = 'student-selected';
                                    }
                                @endphp

                                <div class="answer-option {{ $optionClass }}">
                                    @if($questionType === 'multiple_answer')
                                        <span class="checkbox-indicator">
                                            @if($isSelectedByStudent || $isCorrectAnswer)
                                                <i class="fas fa-check"></i>
                                            @endif
                                        </span>
                                    @endif
                                    
                                    <div class="answer-icon">{{ $letters[$optionIndex] }}</div>
                                    
                                    <div class="answer-text">
                                        {!! nl2br(e($option->answer_text)) !!}
                                    </div>
                                    
                                    <div class="answer-labels">
                                        @if($isCorrectAnswer)
                                            <span class="answer-label label-correct">
                                                <i class="fas fa-check"></i> Đáp án đúng
                                            </span>
                                        @endif
                                        @if($isSelectedByStudent && !$isCorrectAnswer)
                                            <span class="answer-label label-wrong">
                                                <i class="fas fa-times"></i> Bạn đã chọn
                                            </span>
                                        @elseif($isSelectedByStudent && $isCorrectAnswer)
                                            <span class="answer-label label-correct">
                                                <i class="fas fa-check-double"></i> Bạn chọn đúng
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    {{-- Fill Blank --}}
                    @elseif($questionType === 'fill_blank')
                        @php
                            $studentAnswerText = $studentAnswer ? trim($studentAnswer->text_answer ?? '') : '';
                            $correctAnswers = $question->answerOptions()->where('is_correct', 1)->get();
                        @endphp

                        <div class="fill-blank-answer">
                            <div class="answer-comparison">
                                <div class="answer-box student-answer {{ $isCorrect ? 'correct' : ($isAnswered ? 'incorrect' : '') }}">
                                    <div class="answer-box-label" style="color: {{ $isCorrect ? '#065f46' : ($isAnswered ? '#991b1b' : '#3b82f6') }}">
                                        <i class="fas fa-user"></i> Câu trả lời của bạn
                                    </div>
                                    <div class="answer-box-text">
                                        @if($studentAnswerText !== '')
                                            {{ $studentAnswerText }}
                                        @else
                                            <span class="no-answer">Chưa trả lời</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="answer-box correct-answer">
                                    <div class="answer-box-label" style="color: #065f46;">
                                        <i class="fas fa-check-circle"></i> Đáp án đúng
                                    </div>
                                    <div class="answer-box-text">
                                        @foreach($correctAnswers as $idx => $correctAns)
                                            {{ $correctAns->answer_text }}
                                            @if(!$loop->last) <span style="color: #6b7280;"> hoặc </span> @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
            
            buttons.forEach(btn => btn.classList.remove('active'));
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

            // Scroll to first visible question
            setTimeout(() => {
                const firstVisible = document.querySelector('.question-card:not(.hidden)');
                if (firstVisible && filter !== 'all') {
                    firstVisible.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
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

        // Auto scroll to first incorrect answer on load
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight first incorrect answer after a delay
            setTimeout(() => {
                const firstIncorrect = document.querySelector('.question-card.incorrect');
                if (firstIncorrect && !window.location.hash) {
                    firstIncorrect.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    // Add a temporary highlight effect
                    firstIncorrect.style.transform = 'scale(1.02)';
                    firstIncorrect.style.transition = 'transform 0.3s';
                    setTimeout(() => {
                        firstIncorrect.style.transform = 'scale(1)';
                    }, 600);
                }
            }, 800);
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const questions = Array.from(document.querySelectorAll('.question-card:not(.hidden)'));
            const currentIndex = questions.findIndex(q => {
                const rect = q.getBoundingClientRect();
                return rect.top >= 0 && rect.top <= window.innerHeight / 2;
            });

            if (e.key === 'ArrowDown' && currentIndex < questions.length - 1) {
                questions[currentIndex + 1].scrollIntoView({ behavior: 'smooth', block: 'start' });
                e.preventDefault();
            } else if (e.key === 'ArrowUp' && currentIndex > 0) {
                questions[currentIndex - 1].scrollIntoView({ behavior: 'smooth', block: 'start' });
                e.preventDefault();
            }
        });
    </script>
</body>
</html>