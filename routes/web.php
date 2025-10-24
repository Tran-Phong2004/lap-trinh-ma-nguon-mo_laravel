<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Exam routes (require authentication)
// Route::middleware(['auth'])->prefix('exam')->name('exam.')->group(function () {

//     // Show exam taking page
//     Route::get('/{examSessionId}/{hash}', [ExamController::class, 'show'])
//         ->name('show');

//     // Submit exam (Form POST instead of AJAX)
//     Route::post('/submit', [ExamController::class, 'submit'])
//         ->name('submit');

//     // Save exam progress (không logout)
//     Route::post('/save', [ExamController::class, 'save'])
//         ->name('save');

//     // Logout from exam
//     // Route::post('/logout', [ExamController::class, 'logout'])
//     //     ->name('logout');
// });


Route::middleware(['auth', 'role:admin'])->group(function () {
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
    });
});


