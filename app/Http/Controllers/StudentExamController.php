<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\StudentAnswer;
use App\Models\Question;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Hiển thị danh sách phiên thi của student
    public function examSessions()
    {
        $studentId = Auth::id();

        $examSessions = ExamSession::with(['exam', 'results'])
            ->where('student_id', $studentId)
            ->get()
            ->map(function ($session) {
                $now = Carbon::now('Asia/Ho_Chi_Minh');
                $exam = $session->exam;

                // Khởi tạo
                $session->can_start = false;
                $session->status_message = '';

                // Kiểm tra exam có active không
                if (!$exam->is_active) {
                    $session->status_message = 'Bài thi không khả dụng';
                    return $session;
                }

                $startTime = $exam->start_time;
                $endTime = $exam->end_time;

                // Kiểm tra thời gian
                if ($now->lt($startTime)) {
                    $session->status_message = 'Chưa đến giờ thi';
                    $session->time_to_start = $startTime->diffForHumans();
                } elseif ($now->gt($endTime)) {
                    $session->status_message = 'Đã hết hạn';
                } else {
                    // Trong thời gian thi - kiểm tra số lần thi
                    $attemptCount = $session->results()->count();

                    if ($attemptCount >= $session->max_attempts) {
                        $session->status_message = 'Đã hết lượt thi';
                    } else {
                        $session->can_start = true;
                        $session->status_message = 'Có thể bắt đầu';
                        $session->attempts_left = $session->max_attempts - $attemptCount;
                    }
                }

                return $session;
            });

        return view('student.exam-sessions', compact('examSessions'));
    }

    // Bắt đầu làm bài thi
    public function startExam($sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with('exam')
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $exam = $examSession->exam;
        $now = Carbon::now();

        // Validate các điều kiện
        if (!$exam->is_active) {
            return back()->with('error', 'Bài thi không khả dụng');
        }

        if ($now->lt(Carbon::parse($exam->start_time))) {
            return back()->with('error', 'Chưa đến giờ thi');
        }

        if ($now->gt(Carbon::parse($exam->end_time))) {
            return back()->with('error', 'Bài thi đã hết hạn');
        }

        // Kiểm tra số lần thi
        $attemptCount = $examSession->results()->count();
        if ($attemptCount >= $examSession->max_attempts) {
            return back()->with('error', 'Bạn đã hết lượt thi');
        }
        $examSession->update([
            'status' => 'in_progress',
        ]);
        // Xóa các câu trả lời cũ nếu có (trường hợp làm lại)
        StudentAnswer::where('exam_session_id', $sessionId)->delete();
        // Chuyển đến trang làm bài
        return redirect()->route('student.take-exam', ['sessionId' => $sessionId]);
    }

    // Trang làm bài thi
    public function takeExam($sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with(['exam.questions.answerOptions', 'exam.questions.type', 'studentAnswers'])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $exam = $examSession->exam;

        $questions = $exam->questions()
            ->with([
                'type', // Thêm type
                'answerOptions' => function ($query) {
                    $query->orderBy('order');
                }
            ])
            ->orderBy('exam_question.order')
            ->get();

        // Xử lý savedAnswers cho tất cả loại câu hỏi
        $savedAnswers = [];
        foreach ($examSession->studentAnswers as $answer) {
            $question = $questions->firstWhere('id', $answer->question_id);
            if (!$question)
                continue;

            $questionType = $question->type->name;

            if ($questionType === 'multiple_choice') {
                $savedAnswers[$answer->question_id] = $answer->selected_answer_id;
            } elseif ($questionType === 'multiple_answer') {
                $savedAnswers[$answer->question_id] = $answer->selected_answer_ids ?? [];
            } elseif ($questionType === 'fill_blank') {
                $savedAnswers[$answer->question_id] = $answer->text_answer ?? '';
            }
        }

        return view('student.take-exam', compact('examSession', 'exam', 'questions', 'savedAnswers'));
    }

    // Lưu câu trả lời tạm thời (auto-save)
    public function saveAnswer(Request $request, $sessionId)
    {
        $studentId = Auth::id();
        $examSession = ExamSession::where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $questionId = $request->input('question_id');
        $questionType = $request->input('question_type');

        // Lấy thông tin câu hỏi
        $question = Question::with('type', 'answerOptions')->findOrFail($questionId);

        $isCorrect = false;
        $dataToSave = [
            'exam_session_id' => $sessionId,
            'question_id' => $questionId,
        ];

        switch ($questionType) {
            case 'multiple_choice':
                $answerId = $request->input('answer_id');

                if ($answerId) {
                    $isCorrect = $question->answerOptions()
                        ->where('id', $answerId)
                        ->where('is_correct', 1)
                        ->exists();
                }

                $dataToSave['selected_answer_id'] = $answerId;
                $dataToSave['is_correct'] = $isCorrect;
                break;

            case 'multiple_answer':
                $answerIds = $request->input('answer_ids', []);

                if (!empty($answerIds)) {
                    $correctAnswerIds = $question->answerOptions()
                        ->where('is_correct', 1)
                        ->pluck('id')
                        ->toArray();

                    sort($answerIds);
                    sort($correctAnswerIds);
                    $isCorrect = ($answerIds === $correctAnswerIds);
                }

                $dataToSave['selected_answer_ids'] = $answerIds;
                $dataToSave['is_correct'] = $isCorrect;
                break;

            case 'fill_blank':
                $textAnswer = trim($request->input('text_answer', ''));

                if ($textAnswer !== '') {
                    $correctAnswers = $question->answerOptions()
                        ->where('is_correct', 1)
                        ->get();

                    // Kiểm tra với tất cả các đáp án đúng (có thể có nhiều cách trả lời)
                    foreach ($correctAnswers as $correctAnswer) {
                        $correctText = trim($correctAnswer->answer_text);

                        // So sánh không phân biệt hoa thường và loại bỏ khoảng trắng thừa
                        if (mb_strtolower($textAnswer) === mb_strtolower($correctText)) {
                            $isCorrect = true;
                            break;
                        }
                    }
                }
                $dataToSave['text_answer'] = $textAnswer;
                $dataToSave['is_correct'] = $isCorrect;
                break;
        }

        StudentAnswer::updateOrCreate(
            [
                'exam_session_id' => $sessionId,
                'question_id' => $questionId
            ],
            $dataToSave
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu câu trả lời'
        ]);
    }

    // Submit bài thi
    public function submitExam(Request $request, $sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with(['exam.questions.answerOptions', 'exam.questions.type'])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $answers = $request->input('answers', []);

        DB::beginTransaction();
        try {
            $correctCount = 0;
            $wrongCount = 0;
            $totalQuestions = $examSession->exam->questions->count();

            // Xóa các câu trả lời cũ
            StudentAnswer::where('exam_session_id', $sessionId)->delete();

            // Lưu câu trả lời và chấm điểm
            foreach ($examSession->exam->questions as $question) {
                $questionType = $question->type->name;
                $answer = $answers[$question->id] ?? null;
                $isCorrect = false;

                switch ($questionType) {
                    case 'multiple_choice':
                        $selectedAnswerId = $answer;

                        if ($selectedAnswerId) {
                            $isCorrect = $question->answerOptions()
                                ->where('id', $selectedAnswerId)
                                ->where('is_correct', 1)
                                ->exists();
                        }

                        StudentAnswer::create([
                            'exam_session_id' => $sessionId,
                            'question_id' => $question->id,
                            'selected_answer_id' => $selectedAnswerId,
                            'is_correct' => $isCorrect
                        ]);
                        break;

                    case 'multiple_answer':
                        $selectedAnswerIds = is_array($answer) ? $answer : [];

                        // Chuyển đổi string sang integer
                        $selectedAnswerIds = array_map('intval', $selectedAnswerIds);

                        if (!empty($selectedAnswerIds)) {
                            $correctAnswerIds = $question->answerOptions()
                                ->where('is_correct', 1)
                                ->pluck('id')
                                ->map(fn($id) => (int) $id) // Đảm bảo kiểu integer
                                ->toArray();

                            sort($selectedAnswerIds);
                            sort($correctAnswerIds);
                            $isCorrect = ($selectedAnswerIds === $correctAnswerIds);
                        }

                        StudentAnswer::create([
                            'exam_session_id' => $sessionId,
                            'question_id' => $question->id,
                            'selected_answer_ids' => $selectedAnswerIds,
                            'is_correct' => $isCorrect
                        ]);
                        break;

                    case 'fill_blank':
                        $textAnswer = is_string($answer) ? trim($answer) : '';

                        if ($textAnswer !== '') {
                            $correctAnswers = $question->answerOptions()
                                ->where('is_correct', 1)
                                ->get();

                            // Kiểm tra với tất cả các đáp án đúng
                            foreach ($correctAnswers as $correctAnswer) {
                                $correctText = trim($correctAnswer->answer_text);

                                if (mb_strtolower($textAnswer) === mb_strtolower($correctText)) {
                                    $isCorrect = true;
                                    break;
                                }
                            }
                        }

                        StudentAnswer::create([
                            'exam_session_id' => $sessionId,
                            'question_id' => $question->id,
                            'text_answer' => $textAnswer,
                            'is_correct' => $isCorrect
                        ]);
                        break;
                }

                if ($isCorrect) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            }

            // Tính điểm
            $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 10 : 0;

            // Cập nhật trạng thái session
            $examSession->update(['status' => 'completed']);

            // Lưu kết quả
            $examSession->results()->create([
                'score' => round($score, 2),
                'correct_answers' => $correctCount,
                'wrong_answers' => $wrongCount,
                'submitted_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('student.exam-result', ['sessionId' => $sessionId])
                ->with('success', 'Nộp bài thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xem kết quả
    public function examResult($sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with(['exam', 'results'])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $latestResult = $examSession->results()->orderByDesc('submitted_at')->first();
        return view('student.exam-result', compact('examSession', 'latestResult'));
    }

    // Xem lại bài làm
    public function reviewExam($sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with([
            'exam.questions' => function ($query) {
                $query->orderBy('exam_question.order');
            },
            'exam.questions.answerOptions' => function ($query) {
                $query->orderBy('order');
            },
            'exam.questions.type',
            'studentAnswers.selectedAnswer',
            'results'
        ])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        // Kiểm tra xem đã hoàn thành bài thi chưa
        if ($examSession->status !== 'completed') {
            return redirect()->route('student.exam-sessions')
                ->with('error', 'Bạn chưa hoàn thành bài thi này');
        }

        // Tạo map câu trả lời của học sinh theo question_id
        $answersMap = $examSession->studentAnswers->keyBy('question_id');
        $latestResult = $examSession->results()->orderByDesc('submitted_at')->first();

        return view('student.review-exam', compact('examSession', 'answersMap', 'latestResult'));
    }
}