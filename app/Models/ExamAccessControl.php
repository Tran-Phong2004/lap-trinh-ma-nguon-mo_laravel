<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAccessControl extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'student_id',
        'exam_session_id',
        'fingerprint_hash',
        'ip_address',
        'is_accepted'
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
