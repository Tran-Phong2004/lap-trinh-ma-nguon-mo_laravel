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
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('exam_name', 255);
            $table->text('description')->nullable();
            $table->integer('duration_minutes'); // thời gian thi (phút)
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_active')->default(1);
        });

        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('session_name', 255)->nullable();
            $table->integer('max_attempts')->default(1); // số lần được thi
            $table->string('status', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
        });

        Schema::create('exam_access_control', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('exam_session_id')->nullable();
            $table->string('fingerprint_hash', 150)->nullable();
            $table->string('ip_address', 65)->nullable();
            $table->boolean('is_accepted')->default(1);

            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_session_id');
            $table->float('score')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->dateTime('submitted_at')->nullable();
            
            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_access_control');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exams');
    }
};
