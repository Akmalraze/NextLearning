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
        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');

            // Attempt tracking
            $table->integer('attempt_number')->default(1)->comment('Attempt number for this submission (1, 2, 3, etc.)');
            $table->timestamp('started_at')->nullable()->comment('When student started the quiz');

            // Type and answers
            $table->enum('type', ['quiz', 'homework', 'test']);
            // For quizzes: store answers as JSON (question_id => answer)
            $table->json('answers')->nullable();

            // For homework/test: single uploaded answer file per submission
            $table->string('answer_file_path')->nullable();
            $table->string('answer_original_name')->nullable();

            // Grading and timestamps
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['assessment_id', 'student_id'], 'index_assessment_student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_submissions');
    }
};
