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
            transition: all 0.3s;
        }

        .auto-save-indicator.saving {
            color: #f59e0b;
        }

        .auto-save-indicator.saved {
            color: #10b981;
        }

        .auto-save-indicator i {
            font-size: 10px;
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
            transition: all 0.3s;
        }

        .question-card.answered {
            border-left: 4px solid #10b981;
        }

        .question-number {
            color: #667eea;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
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
            position: relative;
        }

        .answer-option:hover {
            border-color: #667eea;
            background: #f9fafb;
            transform: translateX(4px);
        }

        .answer-option.selected {
            border-color: #667eea;
            background: #eff6ff;
        }

        /* Checkbox styling cho multiple answer */
        .answer-option input[type="checkbox"] {
            display: none;
        }

        .answer-option input[type="radio"] {
            display: none;
        }

        .checkbox-indicator {
            width: 24px;
            height: 24px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .answer-option.selected .checkbox-indicator {
            background: #667eea;
            border-color: #667eea;
        }

        .checkbox-indicator i {
            color: white;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .answer-option.selected .checkbox-indicator i {
            opacity: 1;
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
            transition: all 0.3s;
        }

        .answer-option.selected .answer-icon {
            background: #667eea;
            color: white;
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
            position: sticky;
            bottom: 20px;
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
            gap: 15px;
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

        .question-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
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

        .fill-blank-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .fill-blank-input:focus {
            outline: none;
            border-color: #667eea;
            background: #f9fafb;
        }

        .fill-blank-input.has-value {
            border-color: #10b981;
            background: #f0fdf4;
        }

        /* Multiple answer instruction */
        .instruction-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }

        /* Question answered indicator */
        .answered-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
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
                    $questionType = $question->type->name;
                @endphp
                <div class="question-card" id="question-{{ $question->id }}" data-question-id="{{ $question->id }}">
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

                    <div class="question-text">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>

                    @if($question->image)
                        <img src="{{ asset('storage/' . $question->image) }}" 
                             alt="Question image" 
                             style="max-width: 100%; border-radius: 8px; margin-bottom: 20px;">
                    @endif

                    {{-- Multiple Choice --}}
                    @if($questionType === 'multiple_choice')
                        <div class="answers-list">
                            @foreach($question->answerOptions as $optionIndex => $option)
                                @php
                                    $isSelected = isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id;
                                @endphp
                                <label class="answer-option {{ $isSelected ? 'selected' : '' }}" 
                                       data-question-id="{{ $question->id }}" 
                                       data-answer-id="{{ $option->id }}">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $option->id }}"
                                           {{ $isSelected ? 'checked' : '' }}
                                           onchange="saveAnswer({{ $question->id }}, {{ $option->id }}, 'multiple_choice')">
                                    <span class="answer-icon">{{ $letters[$optionIndex] }}</span>
                                    <span class="answer-content">{!! nl2br(e($option->answer_text)) !!}</span>
                                </label>
                            @endforeach
                        </div>

                    {{-- Multiple Answer --}}
                    @elseif($questionType === 'multiple_answer')
                        <div class="instruction-badge">
                            <i class="fas fa-info-circle"></i>
                            Chọn tất cả các đáp án đúng
                        </div>
                        <div class="answers-list">
                            @foreach($question->answerOptions as $optionIndex => $option)
                                @php
                                    $isChecked = isset($savedAnswers[$question->id]) && 
                                                is_array($savedAnswers[$question->id]) && 
                                                in_array($option->id, $savedAnswers[$question->id]);
                                @endphp
                                <label class="answer-option {{ $isChecked ? 'selected' : '' }}" 
                                       data-question-id="{{ $question->id }}" 
                                       data-answer-id="{{ $option->id }}">
                                    <input type="checkbox" 
                                           name="answers[{{ $question->id }}][]" 
                                           value="{{ $option->id }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           onchange="saveMultipleAnswer({{ $question->id }})">
                                    <span class="checkbox-indicator">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span class="answer-icon">{{ $letters[$optionIndex] }}</span>
                                    <span class="answer-content">{!! nl2br(e($option->answer_text)) !!}</span>
                                </label>
                            @endforeach
                        </div>

                    {{-- Fill Blank --}}
                    @elseif($questionType === 'fill_blank')
                        <input type="text" 
                               name="answers[{{ $question->id }}]" 
                               class="fill-blank-input {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] !== '' ? 'has-value' : '' }}"
                               value="{{ $savedAnswers[$question->id] ?? '' }}"
                               placeholder="Nhập câu trả lời của bạn..."
                               oninput="saveFillBlankAnswer({{ $question->id }}, this.value)">
                    @endif
                </div>
            @endforeach

            <div class="submit-section">
                <div class="progress-indicator">
                    <span class="progress-text">
                        Đã trả lời: <strong id="answeredCount">0</strong>/{{ $questions->count() }}
                    </span>
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
        let timeLimit = {{ $exam->duration_minutes }} * 60;
        let timeRemaining = timeLimit;
        
        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const timerElement = document.getElementById('timeRemaining');
            const timerDiv = document.getElementById('timer');
            
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeRemaining <= 300) {
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

        // Auto-save functions
        let saveTimeout;

        function showSaveIndicator(status) {
            const indicator = document.getElementById('autoSaveIndicator');
            const text = document.getElementById('autoSaveText');

            indicator.classList.remove('saved', 'saving');
            
            if (status === 'saving') {
                indicator.classList.add('saving');
                text.textContent = 'Đang lưu...';
            } else if (status === 'saved') {
                indicator.classList.add('saved');
                text.textContent = 'Đã lưu';
                setTimeout(() => {
                    indicator.classList.remove('saved');
                    text.textContent = 'Tự động lưu';
                }, 2000);
            } else if (status === 'error') {
                text.textContent = 'Lỗi lưu';
            }
        }

        function markQuestionAnswered(questionId, answered) {
            const questionCard = document.getElementById('question-' + questionId);
            if (answered) {
                questionCard.classList.add('answered');
            } else {
                questionCard.classList.remove('answered');
            }
        }

        // Save Multiple Choice
        function saveAnswer(questionId, answerId, type) {
            clearTimeout(saveTimeout);
            showSaveIndicator('saving');

            saveTimeout = setTimeout(() => {
                fetch('{{ route("student.save-answer", $examSession->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer_id: answerId,
                        question_type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showSaveIndicator('saved');
                    markQuestionAnswered(questionId, true);
                    updateProgress();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveIndicator('error');
                });
            }, 500);

            // Update UI
            document.querySelectorAll(`[data-question-id="${questionId}"]`).forEach(el => {
                el.classList.remove('selected');
            });
            document.querySelector(`[data-question-id="${questionId}"][data-answer-id="${answerId}"]`).classList.add('selected');
        }

        // Save Multiple Answer
        function saveMultipleAnswer(questionId) {
            clearTimeout(saveTimeout);
            showSaveIndicator('saving');

            const checkboxes = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`);
            const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

            saveTimeout = setTimeout(() => {
                fetch('{{ route("student.save-answer", $examSession->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer_ids: selectedIds,
                        question_type: 'multiple_answer'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showSaveIndicator('saved');
                    markQuestionAnswered(questionId, selectedIds.length > 0);
                    updateProgress();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveIndicator('error');
                });
            }, 500);

            // Update UI
            document.querySelectorAll(`[data-question-id="${questionId}"]`).forEach(el => {
                const checkbox = el.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    el.classList.add('selected');
                } else {
                    el.classList.remove('selected');
                }
            });
        }

        // Save Fill Blank
        function saveFillBlankAnswer(questionId, value) {
            clearTimeout(saveTimeout);
            showSaveIndicator('saving');

            const input = event.target;
            const trimmedValue = value.trim();

            // Update input styling
            if (trimmedValue !== '') {
                input.classList.add('has-value');
            } else {
                input.classList.remove('has-value');
            }

            saveTimeout = setTimeout(() => {
                fetch('{{ route("student.save-answer", $examSession->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        text_answer: trimmedValue,
                        question_type: 'fill_blank'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showSaveIndicator('saved');
                    markQuestionAnswered(questionId, trimmedValue !== '');
                    updateProgress();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveIndicator('error');
                });
            }, 800); // Longer delay for typing
        }

        // Update Progress
        function updateProgress() {
            const totalQuestions = {{ $questions->count() }};
            let answeredCount = 0;

            // Count radio buttons
            answeredCount += document.querySelectorAll('input[type="radio"]:checked').length;

            // Count checkboxes (multiple answer) - only questions with at least 1 selection
            const multipleAnswerQuestions = new Set();
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                const match = cb.name.match(/\[(\d+)\]/);
                if (match) {
                    multipleAnswerQuestions.add(match[1]);
                }
            });
            answeredCount += multipleAnswerQuestions.size;

            // Count fill blank inputs with value
            document.querySelectorAll('.fill-blank-input').forEach(input => {
                if (input.value.trim() !== '') {
                    answeredCount++;
                }
            });

            const percentage = (answeredCount / totalQuestions) * 100;

            document.getElementById('answeredCount').textContent = answeredCount;
            document.getElementById('progressFill').style.width = percentage + '%';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Mark answered questions from saved data
            document.querySelectorAll('.question-card').forEach(card => {
                const questionId = card.dataset.questionId;
                
                // Check if question has any answer
                const hasRadio = card.querySelector('input[type="radio"]:checked');
                const hasCheckbox = card.querySelector('input[type="checkbox"]:checked');
                const hasFillBlank = card.querySelector('.fill-blank-input.has-value');
                
                if (hasRadio || hasCheckbox || hasFillBlank) {
                    card.classList.add('answered');
                }
            });

            updateProgress();
        });

        // Confirm before submit
        function confirmSubmit() {
            const totalQuestions = {{ $questions->count() }};
            let answeredCount = 0;

            answeredCount += document.querySelectorAll('input[type="radio"]:checked').length;

            const multipleAnswerQuestions = new Set();
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                const match = cb.name.match(/\[(\d+)\]/);
                if (match) multipleAnswerQuestions.add(match[1]);
            });
            answeredCount += multipleAnswerQuestions.size;

            document.querySelectorAll('.fill-blank-input').forEach(input => {
                if (input.value.trim() !== '') answeredCount++;
            });

            if (answeredCount < totalQuestions) {
                const unanswered = totalQuestions - answeredCount;
                return confirm(`Bạn còn ${unanswered} câu chưa trả lời. Bạn có chắc muốn nộp bài?`);
            }

            return confirm('Bạn có chắc muốn nộp bài? Bạn sẽ không thể thay đổi câu trả lời sau khi nộp.');
        }
    </script>
</body>
</html>