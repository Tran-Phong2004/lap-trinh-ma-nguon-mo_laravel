<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Làm Bài Thi - {{ $exam->exam_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        
        /* Header */
        .exam-header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 20px;
            z-index: 100;
        }
        .exam-info h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .exam-info p { color: #666; font-size: 14px; }
        
        /* Timer */
        .timer-container { text-align: right; }
        .timer-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .timer {
            font-size: 36px;
            font-weight: bold;
            color: #2ecc71;
            font-family: 'Courier New', monospace;
        }
        .timer.warning {
            color: #f39c12;
            animation: pulse 1s infinite;
        }
        .timer.danger {
            color: #e74c3c;
            animation: pulse 0.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        /* Question Navigation */
        .question-nav {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .question-nav h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
        }
        .nav-btn {
            padding: 10px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        .nav-btn.answered {
            background: #d4edda;
            border-color: #28a745;
        }
        .nav-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        /* Question Container */
        .question-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        .question-container.active {
            display: block;
        }
        .question-number {
            color: #667eea;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .question-text {
            color: #333;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .question-image {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        /* Answers */
        .answers-list { list-style: none; }
        .answer-option { margin-bottom: 15px; }
        .answer-option label {
            display: flex;
            align-items: flex-start;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .answer-option label:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        .answer-option input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            margin-top: 2px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .answer-option input[type="radio"]:checked + .answer-text {
            font-weight: 600;
        }
        .answer-option label:has(input:checked) {
            border-color: #667eea;
            background: #e8eeff;
        }
        .answer-text {
            flex: 1;
            color: #333;
            font-size: 16px;
            line-height: 1.5;
        }
        
        /* Navigation Buttons */
        .navigation-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-prev {
            background: #6c757d;
            color: white;
        }
        .btn-prev:hover:not(:disabled) {
            background: #5a6268;
        }
        .btn-next {
            background: #667eea;
            color: white;
            flex: 1;
        }
        .btn-next:hover {
            background: #5568d3;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Submit Section */
        .submit-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            display: none;
        }
        .submit-section.active {
            display: block;
        }
        .submit-section h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .submit-section p {
            color: #666;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 20px;
        }
        .stat-item { text-align: center; }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .btn-submit {
            background: #28a745;
            color: white;
            width: 100%;
            padding: 15px;
            font-size: 18px;
        }
        .btn-submit:hover {
            background: #218838;
        }
        
        /* Modal */
        .warning-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .warning-modal.show { display: flex; }
        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            max-width: 500px;
            text-align: center;
        }
        .modal-content h2 {
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .modal-content p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .modal-buttons {
            display: flex;
            gap: 15px;
        }
        .modal-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }
        .modal-btn-confirm {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header với Timer -->
        <div class="exam-header">
            <div class="exam-info">
                <h1>{{ $exam->exam_name }}</h1>
                <p>{{ $questions->count() }} câu hỏi • {{ $exam->duration_minutes }} phút</p>
            </div>
            <div class="timer-container">
                <div class="timer-label">⏱️ Thời gian còn lại</div>
                <div class="timer" id="timer">{{ str_pad($exam->duration_minutes, 2, '0', STR_PAD_LEFT) }}:00</div>
            </div>
        </div>

        <!-- Navigation câu hỏi -->
        <div class="question-nav">
            <h3>📋 Danh sách câu hỏi</h3>
            <div class="nav-grid" id="questionNav">
                @foreach($questions as $index => $question)
                    <button type="button" class="nav-btn" data-index="{{ $index }}" onclick="goToQuestion({{ $index }})">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Form làm bài -->
        <form id="examForm" action="{{ route('student.submit-exam', $examSession->id) }}" method="POST">
            @csrf
            
            <!-- Các câu hỏi -->
            @foreach($questions as $index => $question)
                <div class="question-container" data-index="{{ $index }}">
                    <div class="question-number">
                        Câu {{ $index + 1 }}/{{ $questions->count() }}
                    </div>
                    
                    @if($question->image)
                        <img src="{{ asset('storage/' . $question->image) }}" alt="Question Image" class="question-image">
                    @endif
                    
                    <div class="question-text">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>
                    
                    <ul class="answers-list">
                        @foreach($question->answerOptions->sortBy('order') as $optIndex => $option)
                            <li class="answer-option">
                                <label>
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $option->id }}" 
                                           id="answer_{{ $question->id }}_{{ $option->id }}"
                                           onchange="markAnswered({{ $index }})">
                                    <span class="answer-text">{{ chr(65 + $optIndex) }}. {{ $option->answer_text }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="navigation-buttons">
                        <button type="button" class="btn btn-prev" onclick="previousQuestion()" id="btnPrev{{ $index }}">
                            ← Câu trước
                        </button>
                        <button type="button" class="btn btn-next" onclick="nextQuestion()" id="btnNext{{ $index }}">
                            {{ $index === $questions->count() - 1 ? 'Xem lại bài làm →' : 'Câu tiếp →' }}
                        </button>
                    </div>
                </div>
            @endforeach

            <!-- Phần submit -->
            <div class="submit-section" id="submitSection">
                <h2>🎯 Hoàn thành bài thi</h2>
                <p>Kiểm tra lại bài làm của bạn trước khi nộp</p>
                
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number" id="answeredCount">0</div>
                        <div class="stat-label">Đã làm</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="unansweredCount">{{ $questions->count() }}</div>
                        <div class="stat-label">Chưa làm</div>
                    </div>
                </div>

                <button type="button" class="btn btn-submit" onclick="confirmSubmit()">
                    Nộp bài thi
                </button>
            </div>
        </form>
    </div>

    <!-- Modal xác nhận -->
    <div class="warning-modal" id="confirmModal">
        <div class="modal-content">
            <h2>⚠️ Xác nhận nộp bài</h2>
            <p>Bạn có chắc chắn muốn nộp bài? Bạn sẽ không thể chỉnh sửa sau khi nộp.</p>
            <div class="modal-buttons">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal()">
                    Hủy
                </button>
                <button type="button" class="modal-btn modal-btn-confirm" onclick="submitExam()">
                    Nộp bài
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentQuestion = 0;
        const totalQuestions = {{ $questions->count() }};
        const durationMinutes = {{ $exam->duration_minutes }};
        let timeLeft = durationMinutes * 60;
        let timerInterval;

        // Khởi tạo khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            initExam();
        });

        function initExam() {
            // Hiển thị câu đầu tiên
            showQuestion(0);
            
            // Bắt đầu timer
            startTimer();
        }

        // Timer functions
        function startTimer() {
            updateTimerDisplay();
            timerInterval = setInterval(function() {
                timeLeft--;
                updateTimerDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    autoSubmit();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            const timerElement = document.getElementById('timer');
            timerElement.textContent = display;
            
            if (timeLeft <= 60) {
                timerElement.className = 'timer danger';
            } else if (timeLeft <= 300) {
                timerElement.className = 'timer warning';
            }
        }

        function autoSubmit() {
            alert('⏰ Hết giờ làm bài! Hệ thống sẽ tự động nộp bài của bạn.');
            document.getElementById('examForm').submit();
        }

        // Question navigation
        function showQuestion(index) {
            // Ẩn tất cả
            document.querySelectorAll('.question-container').forEach(q => {
                q.classList.remove('active');
            });
            
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById('submitSection').classList.remove('active');
            
            // Hiển thị câu hỏi hoặc submit section
            if (index >= totalQuestions) {
                document.getElementById('submitSection').classList.add('active');
                updateStats();
            } else {
                const questionElements = document.querySelectorAll('.question-container');
                if (questionElements[index]) {
                    questionElements[index].classList.add('active');
                }
                
                const navButtons = document.querySelectorAll('.nav-btn');
                if (navButtons[index]) {
                    navButtons[index].classList.add('active');
                }
            }
            
            currentQuestion = index;
        }

        function goToQuestion(index) {
            showQuestion(index);
        }

        function nextQuestion() {
            if (currentQuestion < totalQuestions) {
                showQuestion(currentQuestion + 1);
            }
        }

        function previousQuestion() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        }

        // Mark answered
        function markAnswered(index) {
            const navButtons = document.querySelectorAll('.nav-btn');
            if (navButtons[index]) {
                navButtons[index].classList.add('answered');
            }
            updateStats();
        }

        // Update statistics
        function updateStats() {
            const answered = document.querySelectorAll('.nav-btn.answered').length;
            const unanswered = totalQuestions - answered;
            
            document.getElementById('answeredCount').textContent = answered;
            document.getElementById('unansweredCount').textContent = unanswered;
        }

        // Submit functions
        function confirmSubmit() {
            document.getElementById('confirmModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.remove('show');
        }

        function submitExam() {
            clearInterval(timerInterval);
            document.getElementById('examForm').submit();
        }
    </script>
</body>
</html>