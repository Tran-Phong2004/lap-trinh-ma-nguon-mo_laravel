<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\StudentExamController;

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Group route cho admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.users.index');
        })->name('dashboard');

        // Quản lý người dùng
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        // Gửi thông tin đăng nhập
        Route::post('/users/{id}/send-credentials', [UserController::class, 'sendCredentials'])->name('users.sendCredentials');

        // Routes cho quản lý bài thi (Admin/Teacher)
        Route::resource('exams', ExamController::class);
        Route::get('exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
        Route::get('exams/{exam}/assign', [ExamController::class, 'assignForm'])->name('exams.assign');
        Route::post('exams/{exam}/assign', [ExamController::class, 'assignStudents'])->name('exams.assign.store');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::get('exams/{exam}/available-students', [ExamController::class, 'getAvailableStudents'])
            ->name('exams.available-students');

        // Routes cho báo cáo
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/session/{session}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/exam/{exam}', [ReportController::class, 'examReport'])->name('reports.exam');
        Route::get('/reports/student/{student}', [ReportController::class, 'studentReport'])->name('reports.student');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/chart-data', [ReportController::class, 'chartData'])->name('reports.chart');
    });
});

Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/exam-sessions', [StudentExamController::class, 'examSessions'])->name('exam-sessions');
    Route::post('/exam/{sessionId}/start', [StudentExamController::class, 'startExam'])->name('start-exam');
    Route::get('/exam/{sessionId}/take', [StudentExamController::class, 'takeExam'])->name('take-exam');
    Route::post('/exam/{sessionId}/submit', [StudentExamController::class, 'submitExam'])->name('submit-exam');
    Route::get('/exam/{sessionId}/result', [StudentExamController::class, 'examResult'])->name('exam-result');
});


