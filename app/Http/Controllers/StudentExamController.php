<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
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
            'status' => 'in_progress'
         ]);

        // Chuyển đến trang làm bài
        return redirect()->route('student.take-exam', ['sessionId' => $sessionId]);
    }

    // Trang làm bài thi
    public function takeExam($sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with(['exam.questions.answerOptions'])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $exam = $examSession->exam;

        $questions = $exam->questions()
            ->with([
                'answerOptions' => function ($query) {
                    $query->orderBy('order');
                }
            ])
            ->orderBy('exam_question.order')
            ->get();
        return view('student.take-exam', compact('examSession', 'exam', 'questions'));
    }

    // Submit bài thi
    public function submitExam(Request $request, $sessionId)
    {
        $studentId = Auth::id();

        $examSession = ExamSession::with(['exam.questions.answerOptions'])
            ->where('id', $sessionId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $answers = $request->input('answers', []);

        $correctCount = 0;
        $wrongCount = 0;
        $totalQuestions = $examSession->exam->questions->count();

        // Chấm điểm
        foreach ($examSession->exam->questions as $question) {
            $selectedAnswerId = $answers[$question->id] ?? null;

            if ($selectedAnswerId) {
                $isCorrect = $question->answerOptions()
                    ->where('id', $selectedAnswerId)
                    ->where('is_correct', 1)
                    ->exists();

                if ($isCorrect) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            } else {
                $wrongCount++;
            }
        }

        $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        $examSession->update([
            'status' => 'completed'
        ]);
        // Lưu kết quả
        $examSession->results()->create([
            'score' => $score,
            'correct_answers' => $correctCount,
            'wrong_answers' => $wrongCount,
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->route('student.exam-result', ['sessionId' => $sessionId])
            ->with('success', 'Nộp bài thành công!');
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
}