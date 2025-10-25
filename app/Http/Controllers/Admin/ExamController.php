<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\User;
use App\Models\QuestionType;
use App\Http\Requests\StoreExamRequest;
use App\Services\ExamService;
use App\Http\Controllers\Controller;
use App\Models\ExamSession;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $exams = Exam::with(['questions', 'sessions'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $questionTypes = QuestionType::all();
        $students = User::where('role_id', 3)->get(); // role_id = 3 là học sinh

        return view('admin.exams.create', compact('questionTypes', 'students'));
    }

    public function store(StoreExamRequest $request)
    {
        try {
            $exam = $this->examService->createExamWithQuestionsAndSessions(
                $request->validated()
            );

            return redirect()
                ->route('admin.exams.show', $exam)
                ->with('success', 'Tạo bài thi thành công!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show(Exam $exam)
    {
        $exam->load([
            'questions.answerOptions',
            'questions.type',
            'sessions.student',
            'sessions.results'
        ]);

        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        // Load relationships 
        $exam->load([
            'questions' => function ($query) {
                $query->orderBy('exam_question.order');
            },
            'questions.answerOptions' => function ($query) {
                $query->orderBy('order');
            },
            'questions.type',
            'sessions'
        ]);

        $questionTypes = QuestionType::all();
        $students = User::where('role_id', 3)->get(); // role_id = 3 là học sinh

        return view('admin.exams.edit', compact('exam', 'questionTypes', 'students'));
    }

    public function update(StoreExamRequest $request, Exam $exam)
    {
        try {
            $exam = $this->examService->updateExam($exam, $request->validated());

            return redirect()
                ->route('admin.exams.show', $exam)
                ->with('success', 'Cập nhật bài thi thành công!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Exam $exam)
    {
        try {
            $this->examService->deleteExam($exam);

            return redirect()
                ->route('admin.exams.index')
                ->with('success', 'Xóa bài thi thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function preview(Exam $exam)
    {
        $exam->load(['questions.answerOptions', 'questions.type']);

        return view('admin.exams.preview', compact('exam'));
    }

    public function assignForm(Exam $exam)
    {
        $exam->load('sessions.student');

        // Lấy danh sách học sinh chưa được gán
        $assignedStudentIds = $exam->sessions()->pluck('student_id')->filter();
        $availableStudents = User::where('role_id', 3)
            ->whereNotIn('id', $assignedStudentIds)
            ->orderBy('name')
            ->get();

        // Lấy danh sách học sinh đã được gán
        $assignedStudents = $exam->sessions()
            ->with('student')
            ->get()
            ->map(function ($session) {
                return $session->student;
            })
            ->filter();

        return view('admin.exams.assign', compact('exam', 'availableStudents', 'assignedStudents'));
    }

    public function assignStudents(Request $request, Exam $exam)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        try {
            $assignedCount = 0;

            foreach ($request->student_ids as $studentId) {
                // Kiểm tra xem học sinh đã được gán chưa
                $exists = ExamSession::where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->exists();

                if ($exists) {
                    $student = User::find($studentId);
                    return back()
                        ->withInput()
                        ->with('error', "Học sinh '{$student->name}' đã được gán vào bài thi này rồi!");
                }

                // Nếu chưa tồn tại -> tạo mới session
                ExamSession::create([
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                    'status' => 'not_started',
                ]);
                $assignedCount++;
            }

            return redirect()
                ->route('admin.exams.assign', $exam)
                ->with('success', "Đã gán bài thi cho {$assignedCount} học sinh!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // API để lấy danh sách học sinh chưa được gán vào bài thi
    public function getAvailableStudents(Exam $exam)
    {
        $assignedStudentIds = $exam->sessions()->pluck('student_id');

        $students = User::where('role_id', 3)
            ->whereNotIn('id', $assignedStudentIds)
            ->get(['id', 'name', 'email']);

        return response()->json($students);
    }
}