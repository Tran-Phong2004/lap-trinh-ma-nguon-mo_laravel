<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Question extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'question_type_id',
        'difficulty_level_id',
        'question_text',
        'explanation',
        'image',
        'is_active'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

    public function answerOptions()
    {
        return $this->hasMany(AnswerOption::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_question')->withPivot('order');
    }
}
