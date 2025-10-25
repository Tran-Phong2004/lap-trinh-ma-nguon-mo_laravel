<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'session_name',
        'status',
        // 'start_time',
        // 'end_time',
        'max_attempts'
    ];

    // protected $casts = [
    //     'start_time' => 'datetime',
    //     'end_time' => 'datetime',
    // ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function accessControls()
    {
        return $this->hasMany(ExamAccessControl::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
