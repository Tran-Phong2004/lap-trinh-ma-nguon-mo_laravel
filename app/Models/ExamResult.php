<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'exam_session_id',
        'score',
        'correct_answers',
        'wrong_answers',
        'submitted_at'
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
