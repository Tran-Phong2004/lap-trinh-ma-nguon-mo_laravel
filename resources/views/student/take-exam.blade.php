<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->exam_name }}</title>
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

        .exam-header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .timer {
            font-size: 24px;
            font-weight: 700;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timer.warning {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .auto-save-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .auto-save-indicator.saving {
            color: #f59e0b;
        }

        .auto-save-indicator.saved {
            color: #10b981;
        }

        .exam-container {
            max-width: 1200px;
            margin: 100px auto 30px;
            padding: 0 30px;
        }

        .exam-info {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .question-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .question-number {
            color: #667eea;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
            color: #1f2937;
        }

        .answers-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .answer-option {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .answer-option:hover {
            border-color: #667eea;
            background: #f9fafb;
        }

        .answer-option input[type="radio"] {
            display: none;
        }

        .answer-option input[type="radio"]:checked + .answer-content {
            color: #667eea;
        }

        .answer-option input[type="radio"]:checked ~ .answer-icon {
            background: #667eea;
            color: white;
        }

        .answer-option.selected {
            border-color: #667eea;
            background: #eff6ff;
        }

        .answer-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .answer-content {
            flex: 1;
            font-size: 15px;
            line-height: 1.5;
        }

        .submit-section {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-text {
            font-size: 14px;
            color: #6b7280;
        }

        .progress-bar {
            width: 200px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="exam-header">
        <div class="header-content">
            <div>
                <h1 style="font-size: 20px; margin-bottom: 5px;">{{ $exam->exam_name }}</h1>
                <p style="color: #6b7280; font-size: 14px;">{{ $questions->count() }} câu hỏi</p>
            </div>
            <div style="display: flex; align-items: center; gap: 30px;">
                <div class="auto-save-indicator" id="autoSaveIndicator">
                    <i class="fas fa-circle"></i>
                    <span id="autoSaveText">Tự động lưu</span>
                </div>
                <div class="timer" id="timer">
                    <i class="fas fa-clock"></i>
                    <span id="timeRemaining">{{ $exam->duration_minutes }}:00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="exam-container">
        <div class="exam-info">
            <p><strong>Lưu ý:</strong> Câu trả lời của bạn sẽ được tự động lưu. Hãy kiểm tra kỹ trước khi nộp bài.</p>
        </div>

        <form id="examForm" method="POST" action="{{ route('student.submit-exam', $examSession->id) }}">
            @csrf
            
            @foreach($questions as $index => $question)
                @php
                    $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                @endphp
                <div class="question-card" id="question-{{ $question->id }}">
                    <div class="question-number">
                        <i class="fas fa-question-circle"></i> Câu {{ $index + 1 }}
                    </div>
                    <div class="question-text">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>

                    @if($question->image)
                        <img src="{{ asset('storage/' . $question->image) }}" 
                             alt="Question image" 
                             style="max-width: 100%; border-radius: 8px; margin-bottom: 20px;">
                    @endif

                    <div class="answers-list">
                        @foreach($question->answerOptions as $optionIndex => $option)
                            <label class="answer-option" data-question-id="{{ $question->id }}" data-answer-id="{{ $option->id }}">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $option->id }}"
                                       {{ (isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id) ? 'checked' : '' }}
                                       onchange="saveAnswer({{ $question->id }}, {{ $option->id }})">
                                <span class="answer-icon">{{ $letters[$optionIndex] }}</span>
                                <span class="answer-content">{!! nl2br(e($option->answer_text)) !!}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="submit-section">
                <div class="progress-indicator">
                    <span class="progress-text" id="progressText">Đã trả lời: <strong id="answeredCount">0</strong>/{{ $questions->count() }}</span>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" onclick="return confirmSubmit()">
                    <i class="fas fa-paper-plane"></i> Nộp bài
                </button>
            </div>
        </form>
    </div>

    <script>
        // Timer
        let timeLimit = {{ $exam->duration_minutes }} * 60; // Convert to seconds
        let timeRemaining = timeLimit;
        
        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const timerElement = document.getElementById('timeRemaining');
            const timerDiv = document.getElementById('timer');
            
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeRemaining <= 300) { // 5 minutes warning
                timerDiv.classList.add('warning');
            }
            
            if (timeRemaining <= 0) {
                alert('Hết thời gian! Bài thi sẽ được tự động nộp.');
                document.getElementById('examForm').submit();
            } else {
                timeRemaining--;
            }
        }
        
        setInterval(updateTimer, 1000);

        // Auto-save answer
        let saveTimeout;
        function saveAnswer(questionId, answerId) {
            clearTimeout(saveTimeout);
            
            const indicator = document.getElementById('autoSaveIndicator');
            const text = document.getElementById('autoSaveText');
            
            indicator.classList.remove('saved');
            indicator.classList.add('saving');
            text.textContent = 'Đang lưu...';
            
            saveTimeout = setTimeout(() => {
                fetch('{{ route("student.save-answer", $examSession->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer_id: answerId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    indicator.classList.remove('saving');
                    indicator.classList.add('saved');
                    text.textContent = 'Đã lưu';
                    
                    setTimeout(() => {
                        indicator.classList.remove('saved');
                        text.textContent = 'Tự động lưu';
                    }, 2000);
                    
                    updateProgress();
                })
                .catch(error => {
                    console.error('Error saving answer:', error);
                    text.textContent = 'Lỗi lưu';
                });
            }, 500);
            
            // Update selected style
            document.querySelectorAll(`[data-question-id="${questionId}"]`).forEach(el => {
                el.classList.remove('selected');
            });
            document.querySelector(`[data-question-id="${questionId}"][data-answer-id="${answerId}"]`).classList.add('selected');
        }

        // Update progress
        function updateProgress() {
            const totalQuestions = {{ $questions->count() }};
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            const percentage = (answeredCount / totalQuestions) * 100;
            
            document.getElementById('answeredCount').textContent = answeredCount;
            document.getElementById('progressFill').style.width = percentage + '%';
        }

        // Initialize progress on page load
        updateProgress();

        // Add change event to all radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', updateProgress);
        });

        // Confirm before submit
        function confirmSubmit() {
            const totalQuestions = {{ $questions->count() }};
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            
            if (answeredCount < totalQuestions) {
                return confirm(`Bạn chỉ trả lời ${answeredCount}/${totalQuestions} câu hỏi. Bạn có chắc muốn nộp bài?`);
            }
            
            return confirm('Bạn có chắc muốn nộp bài? Bạn sẽ không thể thay đổi câu trả lời sau khi nộp.');
        }

        // Prevent accidental page close
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });

        // Mark saved answers on load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                const label = radio.closest('.answer-option');
                label.classList.add('selected');
            });
        });
    </script>
</body>
</html>