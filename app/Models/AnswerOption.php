<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    public $timestamps = false;
    protected $fillable = ['question_id', 'answer_text', 'is_correct', 'order'];
    protected $casts = [
        'is_correct' => 'boolean',
    ];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
