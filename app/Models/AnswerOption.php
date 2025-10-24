<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    public $timestamps = false;
    protected $fillable = ['question_id', 'answer_text', 'is_correct', 'order'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
