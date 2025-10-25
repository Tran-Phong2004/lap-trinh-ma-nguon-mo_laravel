<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

}
