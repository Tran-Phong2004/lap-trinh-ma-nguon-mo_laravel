<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique(); // Ví dụ: 'multiple_choice', 'essay'
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('question_type_id');
            $table->unsignedInteger('difficulty_level_id')->nullable();
            $table->longText('question_text');
            $table->text('explanation')->nullable(); // giải thích đáp án
            $table->string('image_path', 255)->nullable(); // nếu có ảnh đính kèm
            $table->boolean('is_active')->default(1);

            $table->foreign('question_type_id')->references('id')->on('question_types');
        });

        Schema::create('answer_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('question_id');
            $table->longText('answer_text');
            $table->boolean('is_correct')->default(0);
            $table->integer('order')->default(0);

            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
        });

        Schema::create('exam_question', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('order')->default(0);

            $table->primary(['exam_id', 'question_id']);

            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_types');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('answer_options');
        Schema::dropIfExists('exam_question');
    }
};
