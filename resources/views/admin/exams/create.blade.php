<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tạo bài thi mới - Admin</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .question-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        .answer-row {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            align-items: center;
        }
        .answer-row input[type="text"] {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Tạo Bài Thi Mới</h1>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Đã xảy ra lỗi khi tạo bài thi:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.exams.store') }}" method="POST" enctype="multipart/form-data" id="examForm">
            @csrf

            <!-- Thông tin bài thi -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Thông Tin Bài Thi</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên bài thi <span class="text-danger">*</span></label>
                        <input type="text" name="exam_name" value="{{ old('exam_name') }}" 
                            class="form-control @error('exam_name') is-invalid @enderror" required>
                        @error('exam_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" rows="3" 
                            class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian (phút) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" 
                                class="form-control" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" 
                                class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" 
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" 
                            {{ old('is_active', true) ? 'checked' : '' }} 
                            class="form-check-input" id="isActive">
                        <label class="form-check-label" for="isActive">
                            Kích hoạt bài thi
                        </label>
                    </div>
                </div>
            </div>

            <!-- Câu hỏi -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Câu Hỏi</h5>
                        <button type="button" onclick="addQuestion()" 
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Câu Hỏi
                        </button>
                    </div>

                    <div id="questionsContainer">
    @if(old('questions'))
        @foreach(old('questions') as $qIndex => $question)
            @php
                // Lấy thông tin loại câu hỏi đã chọn
                $selectedTypeId = old("questions.$qIndex.question_type_id");
                $selectedType = $questionTypes->firstWhere('id', $selectedTypeId);
                $typeName = $selectedType ? $selectedType->name : 'multiple_choice';
            @endphp
            
            <div class="question-card p-3 mb-3" id="question_old_{{ $qIndex }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-question-circle text-primary"></i> Câu hỏi {{ $loop->iteration }}
                        </h6>
                        <span class="badge bg-info">{{ $typeName }}</span>
                    </div>
                    <button type="button" onclick="removeOldQuestion({{ $qIndex }})" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Loại câu hỏi <span class="text-danger">*</span></label>
                    <select name="questions[{{ $qIndex }}][question_type_id]" 
                            id="questionType_old_{{ $qIndex }}"
                            onchange="handleQuestionTypeChangeOld({{ $qIndex }})"
                            required class="form-select">
                        @foreach($questionTypes as $type)
                            <option value="{{ $type->id }}" 
                                {{ $selectedTypeId == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <textarea name="questions[{{ $qIndex }}][question_text]" rows="3" required 
                        class="form-control">{{ old("questions.$qIndex.question_text") }}</textarea>
                    @error("questions.$qIndex.question_text")
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Giải thích</label>
                    <textarea name="questions[{{ $qIndex }}][explanation]" rows="2"
                        class="form-control">{{ old("questions.$qIndex.explanation") }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="questions[{{ $qIndex }}][image]" accept="image/*"
                        class="form-control">
                    <div class="form-text">
                        <i class="fas fa-image"></i> Chọn hình ảnh minh họa cho câu hỏi (nếu có)
                    </div>
                </div>

                <div class="mb-3" id="answersSection_old_{{ $qIndex }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Đáp án <span class="text-danger">*</span></label>
                        <button type="button" onclick="addAnswerOld({{ $qIndex }})" 
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Thêm Đáp Án
                        </button>
                    </div>
                    
                    <div id="answersContainer_old_{{ $qIndex }}">
                        @if(isset($question['answers']))
                                                @foreach($question['answers'] as $aIndex => $answer)
                                                    <div class="answer-row mb-2" id="answer_old_{{ $qIndex }}_{{ $aIndex }}">

                                                        @if($typeName === 'multiple_choice')
                                                            {{-- Multiple Choice: Badge + Input + Radio --}}
                                                            <span class="badge bg-secondary" style="min-width: 30px;">{{ chr(65 + $aIndex) }}</span>

                                                            <input type="text" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]"
                                                                value="{{ old("questions.$qIndex.answers.$aIndex.answer_text") }}"
                                                                class="form-control" required 
                                                                placeholder="Đáp án {{ chr(65 + $aIndex) }}">

                                                            <div class="form-check" style="min-width: 80px;">
                                                                <input type="radio" 
                                                                    name="questions[{{ $qIndex }}][correct_answer]"
                                                                    value="{{ $aIndex }}"
                                                                    class="form-check-input"
                                                                    id="correct_old_{{ $qIndex }}_{{ $aIndex }}"
                                                                    {{ old("questions.$qIndex.correct_answer") == $aIndex ? 'checked' : '' }} required>
                                                                <label class="form-check-label" for="correct_old_{{ $qIndex }}_{{ $aIndex }}">Đúng</label>
                                                            </div>

                                                        @elseif($typeName === 'fill_blank')
                                                            {{-- Fill Blank: Chỉ Input + Hidden --}}
                                                            <input type="text" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]"
                                                                value="{{ old("questions.$qIndex.answers.$aIndex.answer_text") }}"
                                                                class="form-control" required 
                                                                placeholder="{{ $aIndex === 0 ? 'Đáp án đúng' : 'Đáp án thay thế ' . ($aIndex + 1) }}">

                                                            <input type="hidden" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" 
                                                                value="1">

                                                        @else
                                                            {{-- Multiple Answer: Badge + Input + Checkbox --}}
                                                            <span class="badge bg-secondary" style="min-width: 30px;">{{ chr(65 + $aIndex) }}</span>

                                                            <input type="text" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]"
                                                                value="{{ old("questions.$qIndex.answers.$aIndex.answer_text") }}"
                                                                class="form-control" required 
                                                                placeholder="Đáp án {{ chr(65 + $aIndex) }}">

                                                            <div class="form-check" style="min-width: 80px;">
                                                                <input type="hidden" 
                                                                    name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" 
                                                                    value="0">
                                                                <input type="checkbox" 
                                                                    name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]"
                                                                    value="1"
                                                                    class="form-check-input"
                                                                    id="correct_old_{{ $qIndex }}_{{ $aIndex }}"
                                                                    {{ old("questions.$qIndex.answers.$aIndex.is_correct") == '1' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="correct_old_{{ $qIndex }}_{{ $aIndex }}">Đúng</label>
                                                            </div>
                                                        @endif

                                                        <button type="button" onclick="removeAnswerOld({{ $qIndex }}, {{ $aIndex }})" 
                                                            class="btn btn-sm btn-outline-danger" title="Xóa đáp án">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                        @endif
                    </div>

                                        @error("questions.$qIndex.answers")
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gán học sinh -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Gán Học Sinh Vào Phiên Thi</h5>

                    <!-- <div class="mb-3">
                        <label class="form-label fw-bold">Tên phiên thi</label>
                        <input type="text" name="session_name" value="{{ old('session_name') }}" 
                            class="form-control" placeholder="Ví dụ: Kiểm tra giữa kỳ - Lớp 10A1">
                    </div> -->

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Số lần thi tối đa</label>
                            <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" 
                                min="1" class="form-control">
                        </div>
                        <!-- <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian bắt đầu phiên</label>
                            <input type="datetime-local" name="session_start_time" 
                                value="{{ old('session_start_time') }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian kết thúc phiên</label>
                            <input type="datetime-local" name="session_end_time" 
                                value="{{ old('session_end_time') }}" class="form-control">
                        </div> -->
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn học sinh</label>
                        <select name="students[]" multiple size="10" class="form-select">
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> 
                            Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều học sinh
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút submit -->
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check"></i> Tạo Bài Thi
                </button>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        let questionIndex = 0;
        let answerIndexes = {};
        let answerIndexesOld = {};
        const questionTypes = @json($questionTypes);

        // Khởi tạo questionIndex
        @if(old('questions'))
            @php
                $oldQuestionsCount = count(old('questions'));
            @endphp
            questionIndex = {{ $oldQuestionsCount }};

            // Khởi tạo answer indexes cho old questions
            @foreach(old('questions') as $qIndex => $question)
                answerIndexesOld[{{ $qIndex }}] = {{ count($question['answers'] ?? []) }};
            @endforeach
        @else
            questionIndex = 0;
        @endif

        console.log('Initial questionIndex:', questionIndex);

        // ==================== FUNCTIONS CHO CÂU HỎI MỚI ====================

        function handleQuestionTypeChange(questionIndex) {
            const selectElement = document.getElementById(`questionType_${questionIndex}`);
            if (!selectElement) {
                console.error('Question type select not found');
                return;
            }

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text.trim();

            const badge = document.getElementById(`typeBadge_${questionIndex}`);
            if (badge) {
                badge.textContent = typeName;
            }

            const answersContainer = document.getElementById(`answersContainer_${questionIndex}`);
            if (!answersContainer) {
                console.error('Answers container not found');
                return;
            }

            answersContainer.innerHTML = '';
            answerIndexes[questionIndex] = 0;

            const addBtn = document.querySelector(`#answersSection_${questionIndex} button[onclick*="addAnswer"]`);
            if (addBtn) {
                addBtn.style.display = 'inline-block';
                addBtn.innerHTML = '<i class="fas fa-plus"></i> Thêm Đáp Án';
            }

            switch(typeName) {
                case 'multiple_choice':
                    for (let i = 0; i < 4; i++) {
                        addAnswerWithType(questionIndex, 'radio');
                    }
                    break;
                case 'multiple_answer':
                    for (let i = 0; i < 4; i++) {
                        addAnswerWithType(questionIndex, 'checkbox');
                    }
                    break;
                case 'fill_blank':
                    const container = document.getElementById(`answersContainer_${questionIndex}`);
                    const fillDiv = document.createElement('div');
                    fillDiv.className = 'mb-2';
                    fillDiv.innerHTML = `
                        <label class="form-label">Đáp án đúng:</label>
                        <input type="text" name="questions[${questionIndex}][answers][0][answer_text]" 
                            placeholder="Nhập đáp án đúng..." required class="form-control">
                        <input type="hidden" name="questions[${questionIndex}][answers][0][is_correct]" value="1">
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> Học sinh sẽ điền vào chỗ trống. Bạn có thể thêm nhiều đáp án được chấp nhận.
                        </div>
                    `;
                    container.appendChild(fillDiv);
                    answerIndexes[questionIndex] = 1;
                    if (addBtn) {
                        addBtn.innerHTML = '<i class="fas fa-plus"></i> Thêm Đáp Án Thay Thế';
                    }
                    break;
                default:
                    for (let i = 0; i < 4; i++) {
                        addAnswerWithType(questionIndex, 'radio');
                    }
            }
        }

        function addAnswerWithType(questionIndex, inputType = 'checkbox') {
            if (!answerIndexes[questionIndex]) {
                answerIndexes[questionIndex] = 0;
            }
        
            const container = document.getElementById(`answersContainer_${questionIndex}`);
            const answerIndex = answerIndexes[questionIndex];
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer-row';
            answerDiv.id = `answer_${questionIndex}_${answerIndex}`;
        
            const letter = String.fromCharCode(65 + answerIndex);
        
            let correctInputHTML = '';
            if (inputType === 'radio') {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                            value="${answerIndex}" class="form-check-input" id="correct_${questionIndex}_${answerIndex}" required>
                        <label class="form-check-label" for="correct_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
            } else if (inputType === 'fill') {
                correctInputHTML = `
                    <input type="hidden" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="1">
                `;
            } else {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="hidden" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="0">
                        <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" 
                            value="1" class="form-check-input" id="correct_${questionIndex}_${answerIndex}">
                        <label class="form-check-label" for="correct_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
            }
        
            answerDiv.innerHTML = `
                ${inputType !== 'fill' ? `<span class="badge bg-secondary" style="min-width: 30px;">${letter}</span>` : ''}
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                    placeholder="${inputType === 'fill' ? (answerIndex === 0 ? 'Nhập đáp án đúng' : 'Đáp án thay thế') : 'Nhập nội dung đáp án ' + letter}" 
                    required class="form-control">
                ${correctInputHTML}
                <button type="button" onclick="removeAnswer(${questionIndex}, ${answerIndex})" 
                    class="btn btn-sm btn-outline-danger" title="Xóa đáp án">
                    <i class="fas fa-times"></i>
                </button>
            `;
        
            container.appendChild(answerDiv);
            answerIndexes[questionIndex]++;
        }

        function addQuestion() {
            console.log('Adding new question with index:', questionIndex);

            const container = document.getElementById('questionsContainer');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-card p-3 mb-3';
            questionDiv.id = `question_${questionIndex}`;

            questionDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-question-circle text-primary"></i> Câu hỏi ${questionIndex + 1}
                        </h6>
                        <span class="badge bg-info question-type-badge" id="typeBadge_${questionIndex}">
                            ${questionTypes[0].name}
                        </span>
                    </div>
                    <button type="button" onclick="removeQuestion(${questionIndex})" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Loại câu hỏi <span class="text-danger">*</span></label>
                    <select name="questions[${questionIndex}][question_type_id]" 
                            id="questionType_${questionIndex}"
                            onchange="handleQuestionTypeChange(${questionIndex})" 
                            required class="form-select">
                        ${questionTypes.map(type => `<option value="${type.id}" data-code="${type.code}">${type.name}</option>`).join('')}
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <textarea name="questions[${questionIndex}][question_text]" rows="3" required
                        class="form-control" placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Giải thích</label>
                    <textarea name="questions[${questionIndex}][explanation]" rows="2"
                        class="form-control" placeholder="Giải thích đáp án đúng (không bắt buộc)"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="questions[${questionIndex}][image]" accept="image/*"
                        class="form-control">
                    <div class="form-text">
                        <i class="fas fa-image"></i> Chọn hình ảnh minh họa cho câu hỏi (nếu có)
                    </div>
                </div>

                <div class="mb-3" id="answersSection_${questionIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Đáp án <span class="text-danger">*</span></label>
                        <button type="button" onclick="addAnswer(${questionIndex})" 
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Thêm Đáp Án
                        </button>
                    </div>
                    <div id="answersContainer_${questionIndex}"></div>
                </div>
            `;

            container.appendChild(questionDiv);
            answerIndexes[questionIndex] = 0;

            for (let i = 0; i < 4; i++) {
                addAnswerWithType(questionIndex, 'radio');
            }
        
            questionIndex++;
        }

        function removeQuestion(index) {
            const questionDiv = document.getElementById(`question_${index}`);
            if (questionDiv && confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                questionDiv.remove();
            }
        }

        function addAnswer(questionIndex) {
            const selectElement = document.getElementById(`questionType_${questionIndex}`);
            if (!selectElement) {
                console.error('Question type select not found');
                return;
            }

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text.trim();

            if (typeName === 'fill_blank') {
                const container = document.getElementById(`answersContainer_${questionIndex}`);
                const answerIndex = answerIndexes[questionIndex];
                const fillDiv = document.createElement('div');
                fillDiv.className = 'mb-2';
                fillDiv.id = `answer_${questionIndex}_${answerIndex}`;
                fillDiv.innerHTML = `
                    <div class="d-flex gap-2">
                        <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                            placeholder="Đáp án thay thế ${answerIndex + 1}..." required class="form-control">
                        <input type="hidden" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="1">
                        <button type="button" onclick="removeAnswer(${questionIndex}, ${answerIndex})" 
                            class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(fillDiv);
                answerIndexes[questionIndex]++;
            } else if (typeName === 'multiple_choice') {
                addAnswerWithType(questionIndex, 'radio');
            } else {
                addAnswerWithType(questionIndex, 'checkbox');
            }
        }

        function removeAnswer(questionIndex, answerIndex) {
            const answerDiv = document.getElementById(`answer_${questionIndex}_${answerIndex}`);
            if (answerDiv) {
                const container = document.getElementById(`answersContainer_${questionIndex}`);
                const answerCount = container.querySelectorAll('.answer-row').length;

                if (answerCount <= 2) {
                    alert('Câu hỏi phải có ít nhất 2 đáp án!');
                    return;
                }

                answerDiv.remove();
            }
        }

        // ==================== FUNCTIONS CHO CÂU HỎI CŨ (OLD) ====================

        function removeOldQuestion(index) {
            const questionDiv = document.getElementById(`question_old_${index}`);
            if (questionDiv && confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                questionDiv.remove();
            }
        }

        function handleQuestionTypeChangeOld(questionIndex) {
            const selectElement = document.getElementById(`questionType_old_${questionIndex}`);
            if (!selectElement) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text.trim();

            const answersContainer = document.getElementById(`answersContainer_old_${questionIndex}`);
            if (!answersContainer) return;

            answersContainer.innerHTML = '';
            answerIndexesOld[questionIndex] = 0;

            if (typeName === 'multiple_choice') {
                for (let i = 0; i < 4; i++) {
                    addAnswerWithTypeOld(questionIndex, 'radio');
                }
            } else if (typeName === 'fill_blank') {
                addAnswerWithTypeOld(questionIndex, 'fill');
            } else if (typeName === 'multiple_answer') {
                for (let i = 0; i < 4; i++) {
                    addAnswerWithTypeOld(questionIndex, 'checkbox');
                }
            }
        }

        function addAnswerOld(questionIndex) {
            const selectElement = document.getElementById(`questionType_old_${questionIndex}`);
            if (!selectElement) return;

            const typeName = selectElement.options[selectElement.selectedIndex].text.trim();

            if (typeName === 'multiple_choice') {
                addAnswerWithTypeOld(questionIndex, 'radio');
            } else if (typeName === 'fill_blank') {
                addAnswerWithTypeOld(questionIndex, 'fill');
            } else {
                addAnswerWithTypeOld(questionIndex, 'checkbox');
            }
        }

        function addAnswerWithTypeOld(questionIndex, inputType = 'checkbox') {
            if (!answerIndexesOld[questionIndex]) {
                answerIndexesOld[questionIndex] = 0;
            }

            const container = document.getElementById(`answersContainer_old_${questionIndex}`);
            const answerIndex = answerIndexesOld[questionIndex];
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer-row mb-2';
            answerDiv.id = `answer_old_${questionIndex}_${answerIndex}`;

            const letter = String.fromCharCode(65 + answerIndex);

            let correctInputHTML = '';
            let placeholderText = '';

            if (inputType === 'radio') {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                            value="${answerIndex}" class="form-check-input" 
                            id="correct_old_${questionIndex}_${answerIndex}" required>
                        <label class="form-check-label" for="correct_old_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
                placeholderText = `Đáp án ${letter}`;
            } else if (inputType === 'fill') {
                correctInputHTML = `
                    <input type="hidden" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="1">
                `;
                placeholderText = answerIndex === 0 ? 'Đáp án đúng' : `Đáp án thay thế ${answerIndex + 1}`;
            } else {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="hidden" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" value="0">
                        <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" 
                            value="1" class="form-check-input" id="correct_old_${questionIndex}_${answerIndex}">
                        <label class="form-check-label" for="correct_old_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
                placeholderText = `Đáp án ${letter}`;
            }

            answerDiv.innerHTML = `
                ${inputType !== 'fill' ? `<span class="badge bg-secondary" style="min-width: 30px;">${letter}</span>` : ''}
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                    placeholder="${placeholderText}" required class="form-control">
                ${correctInputHTML}
                <button type="button" onclick="removeAnswerOld(${questionIndex}, ${answerIndex})" 
                    class="btn btn-sm btn-outline-danger" title="Xóa đáp án">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(answerDiv);
            answerIndexesOld[questionIndex]++;
        }

        function removeAnswerOld(questionIndex, answerIndex) {
            const answerDiv = document.getElementById(`answer_old_${questionIndex}_${answerIndex}`);
            if (answerDiv) {
                const container = document.getElementById(`answersContainer_old_${questionIndex}`);
                const answerCount = container.querySelectorAll('.answer-row').length;

                if (answerCount <= 2) {
                    alert('Câu hỏi phải có ít nhất 2 đáp án!');
                    return;
                }

                answerDiv.remove();
            }
        }

        // ==================== VALIDATION ====================

        document.getElementById('examForm').addEventListener('submit', function(e) {
            const questionsContainer = document.getElementById('questionsContainer');
            const questions = questionsContainer.querySelectorAll('.question-card');

            if (questions.length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất 1 câu hỏi!');
                return false;
            }
        
            let hasError = false;
            questions.forEach((question, index) => {
                const selectElement = question.querySelector('select[name*="question_type_id"]');
                if (!selectElement) return;

                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const typeName = selectedOption.text.trim();

                if (typeName === 'fill_blank') {
                    return;
                }

                const checkboxes = question.querySelectorAll('input[type="checkbox"][name*="is_correct"]:checked');
                const radios = question.querySelectorAll('input[type="radio"][name*="correct_answer"]:checked');

                if (checkboxes.length === 0 && radios.length === 0) {
                    hasError = true;
                    alert(`Câu hỏi ${index + 1} chưa có đáp án đúng! Vui lòng chọn ít nhất 1 đáp án đúng.`);
                }
            });
        
            if (hasError) {
                e.preventDefault();
                return false;
            }
        });

        // ==================== KHỞI TẠO ====================

        document.addEventListener('DOMContentLoaded', function() {
            if (!@json(old('questions'))) {
                addQuestion();
            }
        });
    </script>
</body>
</html>