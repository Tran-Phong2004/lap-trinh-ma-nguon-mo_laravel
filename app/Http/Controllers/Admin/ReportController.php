<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\User;
use App\Models\ExamSession;
use App\Models\ExamResult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Hiển thị trang báo cáo tổng quan
     */
    public function index(Request $request)
    {
        $query = ExamSession::with(['student', 'exam', 'results']);

        // Lọc theo bài thi
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        // Lọc theo học sinh
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo thời gian
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Lấy danh sách bài thi và học sinh để làm filter
        $exams = Exam::orderBy('exam_name')->get(['id', 'exam_name']);
        $students = User::where('role_id', 3)->orderBy('name')->get(['id', 'name']);

        // Thống kê tổng quan
        $statistics = [
            'total_sessions' => ExamSession::count(),
            'completed_sessions' => ExamSession::where('status', 'completed')->count(),
            'in_progress_sessions' => ExamSession::where('status', 'in_progress')->count(),
            'average_score' => ExamResult::avg('score') ?? 0,
        ];

        return view('admin.reports.index', compact('sessions', 'exams', 'students', 'statistics'));
    }

    /**
     * Xem chi tiết kết quả của một exam session
     */
    public function show(ExamSession $session)
    {
        $session->load([
            'student',
            'exam.questions.answerOptions',
            'exam.questions.type',
            'results'
        ]);

        return view('admin.reports.show', compact('session'));
    }

    /**
     * Báo cáo theo bài thi
     */
    public function examReport(Exam $exam)
    {
        $exam->load(['sessions.student', 'sessions.results']);

        // Thống kê theo bài thi
        $statistics = [
            'total_students' => $exam->sessions()->distinct('student_id')->count('student_id'),
            'completed' => $exam->sessions()->where('status', 'completed')->count(),
            'in_progress' => $exam->sessions()->where('status', 'in_progress')->count(),
            'not_started' => $exam->sessions()->where('status', 'not_started')->count(),
            'average_score' => $exam->sessions()
                ->join('exam_results', 'exam_sessions.id', '=', 'exam_results.exam_session_id')
                ->avg('exam_results.score') ?? 0,
            'highest_score' => $exam->sessions()
                ->join('exam_results', 'exam_sessions.id', '=', 'exam_results.exam_session_id')
                ->max('exam_results.score') ?? 0,
            'lowest_score' => $exam->sessions()
                ->join('exam_results', 'exam_sessions.id', '=', 'exam_results.exam_session_id')
                ->where('exam_results.score', '>', 0)
                ->min('exam_results.score') ?? 0,
        ];

        // Lấy top học sinh
        $topStudents = $exam->sessions()
            ->join('exam_results', 'exam_sessions.id', '=', 'exam_results.exam_session_id')
            ->join('users', 'exam_sessions.student_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'exam_results.score', 'exam_results.submitted_at')
            ->orderBy('exam_results.score', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.exam', compact('exam', 'statistics', 'topStudents'));
    }

    /**
     * Báo cáo theo học sinh
     */
    public function studentReport(User $student)
    {
        $student->load(['examSessions.exam', 'examSessions.results']);

        // Thống kê theo học sinh
        $statistics = [
            'total_exams' => $student->examSessions()->distinct('exam_id')->count('exam_id'),
            'completed' => $student->examSessions()->where('status', 'completed')->count(),
            'in_progress' => $student->examSessions()->where('status', 'in_progress')->count(),
            'average_score' => $student->examSessions()
                ->join('exam_results', 'exam_sessions.id', '=', 'exam_results.exam_session_id')
                ->avg('exam_results.score') ?? 0,
        ];

        return view('admin.reports.student', compact('student', 'statistics'));
    }

    /**
     * Export báo cáo ra Excel
     */
    public function export(Request $request)
    {

    }

    public function chartData(Request $request)
    {
        $type = $request->get('type', 'score_distribution');

        switch ($type) {
            case 'score_distribution':
                // Phân bố điểm số
                $data = ExamResult::select(
                    DB::raw('FLOOR(score/10)*10 as score_range'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('score_range')
                ->orderBy('score_range')
                ->get();
                break;

            case 'exam_completion':
                // Tỷ lệ hoàn thành theo bài thi
                $data = Exam::withCount([
                    'sessions',
                    'sessions as completed_count' => function($query) {
                        $query->where('status', 'completed');
                    }
                ])->get();
                break;

            case 'student_performance':
                // Thành tích học sinh theo thời gian
                $data = ExamResult::select(
                    DB::raw('DATE(submitted_at) as date'),
                    DB::raw('AVG(score) as avg_score'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereNotNull('submitted_at')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }
}