<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamSession;
use App\Models\AnswerOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExamService
{
    // public function createExamWithQuestionsAndSessions(array $data): Exam
    // {
    //     return DB::transaction(function () use ($data) {
    //         // 1. Tạo bài thi
    //         $exam = Exam::create([
    //             'exam_name' => $data['exam_name'],
    //             'description' => $data['description'] ?? null,
    //             'duration_minutes' => $data['duration_minutes'],
    //             'start_time' => $data['start_time'],
    //             'end_time' => $data['end_time'],
    //             'is_active' => $data['is_active'] ?? true,
    //         ]);

    //         // 2. Tạo câu hỏi và đáp án
    //         $questionIds = [];
    //         foreach ($data['questions'] as $index => $questionData) {
    //             $question = $this->createQuestionWithAnswers($questionData);
    //             $questionIds[$question->id] = ['order' => $index + 1];
    //         }

    //         // 3. Gán câu hỏi vào bài thi
    //         $exam->questions()->attach($questionIds);

    //         // 4. Tạo exam sessions cho học sinh
    //         if (!empty($data['students'] ?? [])) {
    //             $this->createExamSessions($exam, $data);
    //         }

    //         ExamSession::firstOrCreate([
    //             'exam_id' => $exam->id,
    //         ], [
    //              'max_attempts' => $data['max_attempts'],
    //         ]);

    //         return $exam->load(['questions.answerOptions', 'sessions.student']);
    //     });
    // }
    public function createExamWithQuestionsAndSessions(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Tạo Exam
            $exam = Exam::create([
                'exam_name' => $data['exam_name'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'start_time' => $data['start_time'] ?? now(),
                'end_time' => $data['end_time'] ?? now()->addDays(7),
                'is_active' => !empty($data['is_active']),
            ]);

            // 2. Tạo Questions và gán vào exam thông qua bảng exam_question
            if (isset($data['questions']) && !empty($data['questions'])) {
                $questionIds = [];

                foreach ($data['questions'] as $questionIndex => $questionData) {
                    // Xử lý upload ảnh cho câu hỏi
                    $imagePath = null;
                    if (isset($questionData['image']) && $questionData['image']) {
                        $imagePath = $questionData['image']->store('questions', 'public');
                    }

                    // Tạo câu hỏi
                    $question = Question::create([
                        'question_type_id' => $questionData['question_type_id'],
                        'difficulty_level_id' => $questionData['difficulty_level_id'] ?? null,
                        'question_text' => $questionData['question_text'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'image' => $imagePath, // Lưu đường dẫn ảnh
                        'is_active' => true,
                        'order' => $questionIndex + 1,
                    ]);

                    // Tạo Answer Options
                    $this->createAnswerOptions($question, $questionData);

                    // Lưu question_id và order để gán vào bảng exam_question
                    $questionIds[$question->id] = ['order' => $questionIndex + 1];
                }

                // 3. Gán câu hỏi vào exam thông qua bảng trung gian exam_question
                $exam->questions()->attach($questionIds);
            }

            // 4. Tạo Exam Sessions cho học sinh
            if (isset($data['students']) && !empty($data['students'])) {
                foreach ($data['students'] as $studentId) {
                    ExamSession::create([
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                        'session_name' => $data['session_name'] ?? "Phiên thi - {$exam->exam_name}",
                        'status' => 'not_started',
                        'max_attempts' => $data['max_attempts'] ?? 1,
                    ]);
                }
            }

            return $exam->load(['questions.answerOptions', 'sessions.student']);
        });
    }


    protected function createQuestionWithAnswers(array $questionData): Question
    {
        // Xử lý upload ảnh
        $imagePath = null;
        if (!empty($questionData['image'])) {
            $imagePath = $questionData['image']->store('questions', 'public');
        }

        // Tạo câu hỏi
        $question = Question::create([
            'question_type_id' => $questionData['question_type_id'],
            'difficulty_level_id' => $questionData['difficulty_level_id'] ?? null,
            'question_text' => $questionData['question_text'],
            'explanation' => $questionData['explanation'] ?? null,
            'image_path' => $imagePath,
            'is_active' => $questionData['is_active'] ?? true,
        ]);

        // Tạo các đáp án
        foreach ($questionData['answers'] as $index => $answerData) {
            $question->answerOptions()->create([
                'answer_text' => $answerData['answer_text'],
                'is_correct' => $answerData['is_correct'] ?? false,
                'order' => $index + 1,
            ]);
        }

        return $question;
    }

    // protected function createExamSessions(Exam $exam, array $data): void
    // {
    //     $sessionData = [
    //         'session_name' => $data['session_name'] ?? "Phiên thi - {$exam->exam_name}",
    //         'start_time' => $data['session_start_time'] ?? $exam->start_time ?? now(),
    //         'end_time' => $data['session_end_time'] ?? $exam->end_time ?? now()->addDays(7),
    //         'max_attempts' => $data['max_attempts'] ?? 1,
    //     ];

    //     foreach ($data['students'] as $studentId) {
    //         ExamSession::create([
    //             'exam_id' => $exam->id,
    //             'student_id' => $studentId,
    //             'session_name' => $sessionData['session_name'],
    //             'start_time' => $sessionData['start_time'],
    //             'end_time' => $sessionData['end_time'],
    //             'max_attempts' => $sessionData['max_attempts'],
    //         ]);
    //     }
    // }

    // public function updateExam(Exam $exam, array $data): Exam
    // {
    //     return DB::transaction(function () use ($exam, $data) {
    //         // Cập nhật thông tin bài thi
    //         $exam->update([
    //             'exam_name' => $data['exam_name'],
    //             'description' => $data['description'] ?? null,
    //             'duration_minutes' => $data['duration_minutes'],
    //             'start_time' => $data['start_time'] ?? null,
    //             'end_time' => $data['end_time'] ?? null,
    //             'is_active' => $data['is_active'] ?? true,
    //         ]);

    //         // Nếu có cập nhật câu hỏi
    //         if (isset($data['questions'])) {
    //             // Xóa câu hỏi cũ
    //             $exam->questions()->detach();

    //             // Thêm câu hỏi mới
    //             $questionIds = [];
    //             foreach ($data['questions'] as $index => $questionData) {
    //                 if (isset($questionData['id'])) {
    //                     // Cập nhật câu hỏi hiện có
    //                     $question = Question::find($questionData['id']);
    //                     $this->updateQuestion($question, $questionData);
    //                     $questionIds[$question->id] = ['order' => $index + 1];
    //                 } else {
    //                     // Tạo câu hỏi mới
    //                     $question = $this->createQuestionWithAnswers($questionData);
    //                     $questionIds[$question->id] = ['order' => $index + 1];
    //                 }
    //             }

    //             $exam->questions()->attach($questionIds);
    //         }

    //         return $exam->load(['questions.answerOptions', 'sessions.student']);
    //     });
    // }
    public function updateExam(Exam $exam, array $data)
    {
        return DB::transaction(function () use ($exam, $data) {
            // 1. Cập nhật thông tin exam
            $exam->update([
                'exam_name' => $data['exam_name'],
                'description' => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'],
                'start_time' => $data['start_time'] ?? $exam->start_time,
                'end_time' => $data['end_time'] ?? $exam->end_time,
                'is_active' => !empty($data['is_active']),
            ]);

            // 2. Xử lý câu hỏi
            if (isset($data['questions'])) {
                // Lấy danh sách câu hỏi hiện tại của exam
                $currentQuestionIds = $exam->questions()->pluck('questions.id')->toArray();

                // Xóa ảnh của các câu hỏi cũ và xóa câu hỏi
                foreach ($currentQuestionIds as $questionId) {
                    $question = Question::find($questionId);
                    if ($question) {
                        // Xóa ảnh cũ nếu có
                        if ($question->image && Storage::disk('public')->exists($question->image)) {
                            Storage::disk('public')->delete($question->image);
                        }

                        // Xóa câu hỏi (cascade sẽ xóa answer_options)
                        $question->delete();
                    }
                }

                // Detach tất cả câu hỏi khỏi exam
                $exam->questions()->detach();

                // Tạo lại câu hỏi mới
                $questionIds = [];

                foreach ($data['questions'] as $questionIndex => $questionData) {
                    // Xử lý upload ảnh
                    $imagePath = null;
                    if (isset($questionData['image']) && $questionData['image']) {
                        $imagePath = $questionData['image']->store('questions', 'public');
                    }

                    // Tạo câu hỏi mới
                    $question = Question::create([
                        'question_type_id' => $questionData['question_type_id'],
                        'difficulty_level_id' => $questionData['difficulty_level_id'] ?? null,
                        'question_text' => $questionData['question_text'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'image' => $imagePath,
                        'is_active' => true,
                        'order' => $questionIndex + 1,
                    ]);

                    // Tạo đáp án
                    $this->createAnswerOptions($question, $questionData);

                    // Lưu để attach
                    $questionIds[$question->id] = ['order' => $questionIndex + 1];
                }

                // Attach câu hỏi mới vào exam
                $exam->questions()->attach($questionIds);
            }

            return $exam->fresh(['questions.answerOptions', 'sessions.student']);
        });
    }

    // protected function updateQuestion(Question $question, array $data): void
    // {
    //     // Xử lý upload ảnh mới
    //     if (!empty($data['image'])) {
    //         // Xóa ảnh cũ
    //         if ($question->image_path) {
    //             Storage::disk('public')->delete($question->image_path);
    //         }
    //         $imagePath = $data['image']->store('questions', 'public');
    //         $question->image_path = $imagePath;
    //     }

    //     $question->update([
    //         'question_type_id' => $data['question_type_id'],
    //         'question_text' => $data['question_text'],
    //         'explanation' => $data['explanation'] ?? null,
    //         'is_active' => $data['is_active'] ?? true,
    //     ]);

    //     // Cập nhật đáp án
    //     if (isset($data['answers'])) {
    //         $question->answerOptions()->delete();
    //         foreach ($data['answers'] as $index => $answerData) {
    //             $question->answerOptions()->create([
    //                 'answer_text' => $answerData['answer_text'],
    //                 'is_correct' => $answerData['is_correct'] ?? false,
    //                 'order' => $index + 1,
    //             ]);
    //         }
    //     }
    // }
    private function createAnswerOptions(Question $question, array $questionData): void
    {
        if (!isset($questionData['answers']) || empty($questionData['answers'])) {
            return;
        }
        // Lấy thông tin loại câu hỏi
        $questionType = $question->type;
        foreach ($questionData['answers'] as $answerIndex => $answerData) {
            // Xác định đáp án đúng
            $isCorrect = false;

            // Kiểm tra theo tên loại câu hỏi
            if ($questionType->name === 'multiple_choice') {
                // Trắc nghiệm 1 đáp án: kiểm tra radio button (correct_answer)
                $isCorrect = isset($questionData['correct_answer'])
                    && (string) $questionData['correct_answer'] === (string) $answerIndex;
            } elseif ($questionType->name === 'fill_blank') {
                // Điền khuyết: tất cả đáp án đều đúng
                $isCorrect = true;

            } elseif ($questionType->name === 'multiple_answer') {
                // Nhiều đáp án đúng: kiểm tra checkbox của từng đáp án
                $isCorrect = isset($answerData['is_correct']) && $answerData['is_correct'] == '1';

            } else {
                // Fallback: kiểm tra checkbox
                $isCorrect = isset($answerData['is_correct']) && $answerData['is_correct'] == '1';
            }

            AnswerOption::create([
                'question_id' => $question->id,
                'answer_text' => $answerData['answer_text'],
                'is_correct' => $isCorrect,
                'order' => $answerIndex + 1,
            ]);
        }
    }

    public function deleteQuestion(Question $question): bool
    {
        // Xóa ảnh nếu có
        if ($question->image && Storage::disk('public')->exists($question->image)) {
            Storage::disk('public')->delete($question->image);
        }

        // Xóa câu hỏi (cascade sẽ xóa answer_options và exam_question)
        return $question->delete();
    }

    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            // Xóa ảnh của tất cả câu hỏi trong exam
            foreach ($exam->questions as $question) {
                if ($question->image && Storage::disk('public')->exists($question->image)) {
                    Storage::disk('public')->delete($question->image);
                }
            }

            // Xóa exam (cascade sẽ xóa exam_sessions, exam_results, exam_question)
            return $exam->delete();
        });
    }
}