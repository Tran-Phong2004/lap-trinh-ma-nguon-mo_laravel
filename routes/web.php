<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Exam routes (require authentication)
Route::middleware(['auth'])->prefix('exam')->name('exam.')->group(function () {
    
    // Show exam taking page
    Route::get('/{examSessionId}/{hash}', [ExamController::class, 'show'])
        ->name('show');
    
    // Submit exam (Form POST instead of AJAX)
    Route::post('/submit', [ExamController::class, 'submit'])
        ->name('submit');
    
    // Save exam progress (không logout)
    Route::post('/save', [ExamController::class, 'save'])
        ->name('save');
    
    // Logout from exam
    Route::post('/logout', [ExamController::class, 'logout'])
        ->name('logout');
});