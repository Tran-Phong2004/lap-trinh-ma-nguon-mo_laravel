<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
      public $timestamps = false;
    protected $fillable = [
        'question_type_id', 'difficulty_level_id',
        'question_text', 'explanation', 'image_path', 'is_active'
    ];

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
