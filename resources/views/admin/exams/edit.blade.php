<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chỉnh sửa bài thi - Admin</title>
    
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
        .current-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Chỉnh Sửa Bài Thi</h1>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Đã xảy ra lỗi khi cập nhật bài thi:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST" enctype="multipart/form-data" id="examForm">
            @csrf
            @method('PUT')

            <!-- Thông tin bài thi -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Thông Tin Bài Thi</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên bài thi <span class="text-danger">*</span></label>
                        <input type="text" name="exam_name" value="{{ old('exam_name', $exam->exam_name) }}" 
                            class="form-control @error('exam_name') is-invalid @enderror" required>
                        @error('exam_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" rows="3" 
                            class="form-control">{{ old('description', $exam->description) }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian (phút) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                                class="form-control" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_time" 
                                value="{{ old('start_time', $exam->start_time ? date('Y-m-d\TH:i', strtotime($exam->start_time)) : '') }}" 
                                class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_time" 
                                value="{{ old('end_time', $exam->end_time ? date('Y-m-d\TH:i', strtotime($exam->end_time)) : '') }}" 
                                class="form-control">
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" 
                            {{ old('is_active', $exam->is_active) ? 'checked' : '' }} 
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

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Lưu ý:</strong> Khi cập nhật, tất cả câu hỏi cũ sẽ bị xóa và thay thế bởi các câu hỏi mới bạn nhập.
                    </div>

                    <div id="questionsContainer">
                        @if(!old('questions'))
                            {{-- Hiển thị câu hỏi hiện có từ database --}}
                            @foreach($exam->questions as $qIndex => $question)
                                <div class="question-card p-3 mb-3" id="question_existing_{{ $qIndex }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-question-circle text-primary"></i> Câu hỏi {{ $qIndex + 1 }}
                                            </h6>
                                            <span class="badge bg-info">{{ $question->type->name }}</span>
                                        </div>
                                        <button type="button" onclick="removeExistingQuestion({{ $qIndex }})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Loại câu hỏi <span class="text-danger">*</span></label>
                                        <select name="questions[{{ $qIndex }}][question_type_id]" 
                                                id="questionType_existing_{{ $qIndex }}"
                                                onchange="handleQuestionTypeChangeExisting({{ $qIndex }})" 
                                                required class="form-select">
                                            @foreach($questionTypes as $type)
                                                <option value="{{ $type->id }}" {{ $question->question_type_id == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                        <textarea name="questions[{{ $qIndex }}][question_text]" rows="3" required
                                            class="form-control">{{ $question->question_text }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Giải thích</label>
                                        <textarea name="questions[{{ $qIndex }}][explanation]" rows="2"
                                            class="form-control">{{ $question->explanation }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hình ảnh</label>
                                        @if($question->image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $question->image) }}" 
                                                     alt="Current question image" 
                                                     class="current-image">
                                                <p class="text-muted small mt-2">
                                                    <i class="fas fa-info-circle"></i> Ảnh hiện tại (chọn ảnh mới để thay thế)
                                                </p>
                                            </div>
                                        @endif
                                        <input type="file" name="questions[{{ $qIndex }}][image]" accept="image/*"
                                            class="form-control">
                                        <div class="form-text">
                                            <i class="fas fa-image"></i> Chọn hình ảnh mới để thay thế (nếu có)
                                        </div>
                                    </div>

                                    <div class="mb-3" id="answersSection_existing_{{ $qIndex }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-bold mb-0">Đáp án <span class="text-danger">*</span></label>
                                            <button type="button" onclick="addAnswerExisting({{ $qIndex }})" 
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-plus"></i> Thêm Đáp Án
                                            </button>
                                        </div>
                                        <div id="answersContainer_existing_{{ $qIndex }}">
                                            @foreach($question->answerOptions as $aIndex => $answer)
                                                <div class="answer-row" id="answer_existing_{{ $qIndex }}_{{ $aIndex }}">
                                                    
                                                    @if($question->type->name === 'multiple_choice')
                                                        {{-- Multiple Choice: Badge + Input + Radio --}}
                                                        <span class="badge bg-secondary" style="min-width: 30px;">{{ chr(65 + $aIndex) }}</span>
                                                        
                                                        <input type="text" 
                                                            name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]" 
                                                            value="{{ $answer->answer_text }}"
                                                            placeholder="Nhập nội dung đáp án {{ chr(65 + $aIndex) }}" 
                                                            required class="form-control">
                                                        
                                                        <div class="form-check" style="min-width: 80px;">
                                                            <input type="radio" 
                                                                name="questions[{{ $qIndex }}][correct_answer]" 
                                                                value="{{ $aIndex }}" 
                                                                class="form-check-input" 
                                                                id="correct_existing_{{ $qIndex }}_{{ $aIndex }}"
                                                                {{ $answer->is_correct ? 'checked' : '' }} required>
                                                            <label class="form-check-label" for="correct_existing_{{ $qIndex }}_{{ $aIndex }}">Đúng</label>
                                                        </div>
                                                        
                                                    @elseif($question->type->name === 'fill_blank')
                                                        {{-- Fill Blank: Chỉ Input + Hidden --}}
                                                        <input type="text" 
                                                            name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]" 
                                                            value="{{ $answer->answer_text }}"
                                                            placeholder="{{ $aIndex === 0 ? 'Đáp án đúng' : 'Đáp án thay thế ' . $aIndex }}" 
                                                            required class="form-control">
                                                        
                                                        <input type="hidden" 
                                                            name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" 
                                                            value="1">
                                                            
                                                    @else
                                                        {{-- Multiple Answer: Badge + Input + Checkbox --}}
                                                        <span class="badge bg-secondary" style="min-width: 30px;">{{ chr(65 + $aIndex) }}</span>
                                                        
                                                        <input type="text" 
                                                            name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][answer_text]" 
                                                            value="{{ $answer->answer_text }}"
                                                            placeholder="Nhập nội dung đáp án {{ chr(65 + $aIndex) }}" 
                                                            required class="form-control">
                                                        
                                                        <div class="form-check" style="min-width: 80px;">
                                                            <input type="hidden" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" 
                                                                value="0">
                                                            <input type="checkbox" 
                                                                name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][is_correct]" 
                                                                value="1" 
                                                                class="form-check-input" 
                                                                id="correct_existing_{{ $qIndex }}_{{ $aIndex }}"
                                                                {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="correct_existing_{{ $qIndex }}_{{ $aIndex }}">Đúng</label>
                                                        </div>
                                                    @endif
                                            
                                                    <button type="button" onclick="removeAnswerExisting({{ $qIndex }}, {{ $aIndex }})" 
                                                        class="btn btn-sm btn-outline-danger" title="Xóa đáp án">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Hiển thị old() data nếu validation fail --}}
                            @foreach(old('questions') as $qIndex => $question)
                                <div class="question-card p-3 mb-3" id="question_{{ $qIndex }}">
                                    <!-- Code tương tự như trong create.blade.php -->
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Nút submit -->
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Cập Nhật Bài Thi
                </button>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let questionIndex = {{ $exam->questions->count() }};
        let answerIndexes = {};
        let answerIndexesExisting = {};
        const questionTypes = @json($questionTypes);

        // Khởi tạo answer indexes cho các câu hỏi hiện có
        @foreach($exam->questions as $qIndex => $question)
            answerIndexesExisting[{{ $qIndex }}] = {{ $question->answerOptions->count() }};
        @endforeach

        // Hàm xóa câu hỏi hiện có
        function removeExistingQuestion(index) {
            const questionDiv = document.getElementById(`question_existing_${index}`);
            if (questionDiv) {
                if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                    questionDiv.remove();
                }
            }
        }

        // Hàm xử lý thay đổi loại câu hỏi cho câu hỏi hiện có
        function handleQuestionTypeChangeExisting(questionIndex) {
            const selectElement = document.getElementById(`questionType_existing_${questionIndex}`);
            if (!selectElement) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text;

            const answersContainer = document.getElementById(`answersContainer_existing_${questionIndex}`);
            if (!answersContainer) return;

            answersContainer.innerHTML = '';
            answerIndexesExisting[questionIndex] = 0;

            // Render lại đáp án theo loại
            if (typeName === 'multiple_choice') {
                for (let i = 0; i < 4; i++) {
                    addAnswerWithTypeExisting(questionIndex, 'radio');
                }
            } else if (typeName === 'fill_blank') {
                addAnswerWithTypeExisting(questionIndex, 'fill');
            } else {
                for (let i = 0; i < 4; i++) {
                    addAnswerWithTypeExisting(questionIndex, 'checkbox');
                }
            }
        }

        // Thêm đáp án cho câu hỏi hiện có
        function addAnswerExisting(questionIndex) {
            const selectElement = document.getElementById(`questionType_existing_${questionIndex}`);
            if (!selectElement) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text;

            if (typeName === 'multiple_choice') {
                addAnswerWithTypeExisting(questionIndex, 'radio');
            } else if (typeName === 'fill_blank') {
                addAnswerWithTypeExisting(questionIndex, 'fill');
            } else {
                addAnswerWithTypeExisting(questionIndex, 'checkbox');
            }
        }

        function addAnswerWithTypeExisting(questionIndex, inputType = 'checkbox') {
            if (!answerIndexesExisting[questionIndex]) {
                answerIndexesExisting[questionIndex] = 0;
            }

            const container = document.getElementById(`answersContainer_existing_${questionIndex}`);
            const answerIndex = answerIndexesExisting[questionIndex];
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer-row';
            answerDiv.id = `answer_existing_${questionIndex}_${answerIndex}`;

            const letter = String.fromCharCode(65 + answerIndex);

            let correctInputHTML = '';
            let placeholderText = '';
            
            if (inputType === 'radio') {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                            value="${answerIndex}" class="form-check-input" id="correct_existing_${questionIndex}_${answerIndex}" required>
                        <label class="form-check-label" for="correct_existing_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
                placeholderText = `Nhập nội dung đáp án ${letter}`;
            } else if (inputType === 'fill') {
                // Với fill_blank, tất cả đáp án đều đúng
                correctInputHTML = '<input type="hidden" name="questions[' + questionIndex + '][answers][' + answerIndex + '][is_correct]" value="1">';
                placeholderText = answerIndex === 0 ? 'Nhập đáp án đúng' : `Đáp án thay thế ${answerIndex}`;
            } else {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" 
                            value="1" class="form-check-input" id="correct_existing_${questionIndex}_${answerIndex}">
                        <label class="form-check-label" for="correct_existing_${questionIndex}_${answerIndex}">Đúng</label>
                    </div>
                `;
                placeholderText = `Nhập nội dung đáp án ${letter}`;
            }

            answerDiv.innerHTML = `
                ${inputType !== 'fill' ? `<span class="badge bg-secondary" style="min-width: 30px;">${letter}</span>` : ''}
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                    placeholder="${placeholderText}" required class="form-control">
                ${correctInputHTML}
                <button type="button" onclick="removeAnswerExisting(${questionIndex}, ${answerIndex})" 
                    class="btn btn-sm btn-outline-danger" title="Xóa đáp án">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(answerDiv);
            answerIndexesExisting[questionIndex]++;
        }

        function removeAnswerExisting(questionIndex, answerIndex) {
            const answerDiv = document.getElementById(`answer_existing_${questionIndex}_${answerIndex}`);
            if (answerDiv) {
                const container = document.getElementById(`answersContainer_existing_${questionIndex}`);
                const answerCount = container.querySelectorAll('.answer-row').length;
                
                if (answerCount <= 2) {
                    alert('Câu hỏi phải có ít nhất 2 đáp án!');
                    return;
                }
                
                answerDiv.remove();
            }
        }

        // === CÁC HÀM CHO THÊM CÂU HỎI MỚI (giống create.blade.php) ===
        function handleQuestionTypeChange(questionIndex) {
            const selectElement = document.getElementById(`questionType_${questionIndex}`);
            if (!selectElement) return;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const typeName = selectedOption.text;

            const badge = document.getElementById(`typeBadge_${questionIndex}`);
            if (badge) badge.textContent = typeName;

            const answersContainer = document.getElementById(`answersContainer_${questionIndex}`);
            if (!answersContainer) return;

            answersContainer.innerHTML = '';
            answerIndexes[questionIndex] = 0;

            if (typeName === 'multiple_choice') {
                for (let i = 0; i < 4; i++) addAnswerWithType(questionIndex, 'radio');
            } else if (typeName === 'fill_blank') {
                addAnswerWithType(questionIndex, 'fill');
            } else {
                for (let i = 0; i < 4; i++) addAnswerWithType(questionIndex, 'checkbox');
            }
        }

        function addAnswerWithType(questionIndex, inputType = 'checkbox') {
            if (!answerIndexes[questionIndex]) answerIndexes[questionIndex] = 0;

            const container = document.getElementById(`answersContainer_${questionIndex}`);
            const answerIndex = answerIndexes[questionIndex];
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer-row';
            answerDiv.id = `answer_${questionIndex}_${answerIndex}`;

            const letter = String.fromCharCode(65 + answerIndex);

            let correctInputHTML = '';
            let placeholderText = '';
            
            if (inputType === 'radio') {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                            value="${answerIndex}" class="form-check-input" id="correct_${questionIndex}_${answerIndex}" required>
                        <label class="form-check-label">Đúng</label>
                    </div>
                `;
                placeholderText = `Nhập nội dung đáp án ${letter}`;
            } else if (inputType === 'fill') {
                // Với fill_blank, tất cả đáp án đều đúng, dùng hidden input
                correctInputHTML = '<input type="hidden" name="questions[' + questionIndex + '][answers][' + answerIndex + '][is_correct]" value="1">';
                placeholderText = answerIndex === 0 ? 'Nhập đáp án đúng' : `Đáp án thay thế ${answerIndex}`;
            } else {
                correctInputHTML = `
                    <div class="form-check" style="min-width: 80px;">
                        <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" 
                            value="1" class="form-check-input" id="correct_${questionIndex}_${answerIndex}">
                        <label class="form-check-label">Đúng</label>
                    </div>
                `;
                placeholderText = `Nhập nội dung đáp án ${letter}`;
            }

            answerDiv.innerHTML = `
                ${inputType !== 'fill' ? `<span class="badge bg-secondary" style="min-width: 30px;">${letter}</span>` : ''}
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][answer_text]" 
                    placeholder="${placeholderText}" required class="form-control">
                ${correctInputHTML}
                <button type="button" onclick="removeAnswer(${questionIndex}, ${answerIndex})" 
                    class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(answerDiv);
            answerIndexes[questionIndex]++;
        }

        function addQuestion() {
            const container = document.getElementById('questionsContainer');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-card p-3 mb-3';
            questionDiv.id = `question_${questionIndex}`;

            questionDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-question-circle text-primary"></i> Câu hỏi mới ${questionIndex + 1}
                        </h6>
                        <span class="badge bg-success" id="typeBadge_${questionIndex}">
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
                        ${questionTypes.map(type => `<option value="${type.id}">${type.name}</option>`).join('')}
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <textarea name="questions[${questionIndex}][question_text]" rows="3" required
                        class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Giải thích</label>
                    <textarea name="questions[${questionIndex}][explanation]" rows="2"
                        class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="questions[${questionIndex}][image]" accept="image/*"
                        class="form-control">
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
            if (!selectElement) return;

            const typeName = selectElement.options[selectElement.selectedIndex].text;

            if (typeName === 'multiple_choice') {
                addAnswerWithType(questionIndex, 'radio');
            } else if (typeName === 'fill_blank') {
                addAnswerWithType(questionIndex, 'fill');
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

        // Validation
        document.getElementById('examForm').addEventListener('submit', function(e) {
            const questionsContainer = document.getElementById('questionsContainer');
            const questions = questionsContainer.querySelectorAll('.question-card');

            if (questions.length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất 1 câu hỏi!');
                return false;
            }

            // Kiểm tra mỗi câu hỏi phải có ít nhất 1 đáp án đúng (trừ fill_blank)
            let hasError = false;
            questions.forEach((question, index) => {
                // Lấy loại câu hỏi
                const selectElement = question.querySelector('select[name*="question_type_id"]');
                if (!selectElement) return;
                
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const typeName = selectedOption.text.trim();
                
                // Nếu là fill_blank thì bỏ qua validation đáp án đúng
                if (typeName === 'fill_blank') {
                    return;
                }
                
                // Kiểm tra có đáp án đúng không
                const checkboxes = question.querySelectorAll('input[type="checkbox"]:checked');
                const radios = question.querySelectorAll('input[type="radio"]:checked');

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
    </script>
</body>
</html>