<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $questionTypes = [
            ['name' => 'fill_blank'],
            ['name' => 'multiple_answer'],
            ['name' => 'multiple_choice'],
        ];

        DB::table('question_types')->insert($questionTypes);
    }
}