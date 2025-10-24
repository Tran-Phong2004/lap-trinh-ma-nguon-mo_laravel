<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'exam_name',
        'description',
        'duration_minutes',
        'start_time',
        'end_time',
        'is_active'
    ];

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withPivot('order');
    }
}
