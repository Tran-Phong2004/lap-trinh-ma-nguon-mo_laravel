<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('student_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_session_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('selected_answer_id')->nullable();
            $table->text('text_answer')->nullable(); // Cho câu hỏi tự luận
            $table->boolean('is_correct')->default(0);
            $table->timestamps();

            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->cascadeOnDelete();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
            $table->foreign('selected_answer_id')->references('id')->on('answer_options')->nullOnDelete();
            
            // Đảm bảo mỗi câu hỏi chỉ được trả lời 1 lần trong 1 session
            $table->unique(['exam_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
