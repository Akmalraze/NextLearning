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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'test', 'homework']);
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');

            // Availability window
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            // Deadlines and grading
            $table->date('due_date')->nullable();
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->boolean('is_published')->default(false);

            // Quiz-specific settings
            $table->integer('time_limit')->nullable()->comment('Time limit in minutes for quiz');
            $table->integer('max_attempts')->nullable()->comment('Null = unlimited, 1 = one attempt only, >1 = specific number of attempts');
            $table->boolean('show_marks')->default(true)->comment('Whether to show marks to students');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
