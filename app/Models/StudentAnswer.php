<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    protected $fillable = [
        'exam_session_id',
        'question_id',
        'selected_answer_id',
        'text_answer',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Relationship với ExamSession
     */
    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    /**
     * Relationship với Question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship với AnswerOption (câu trả lời đã chọn)
     */
    public function selectedAnswer(): BelongsTo
    {
        return $this->belongsTo(AnswerOption::class, 'selected_answer_id');
    }
}