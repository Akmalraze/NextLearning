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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');

            $table->text('question');

            // Question behavior
            $table->enum('question_type', ['multiple_choice', 'checkboxes', 'short_answer'])->default('multiple_choice');
            $table->boolean('shuffle_options')->default(false);
            $table->json('options')->nullable();

            // Legacy option columns (still used for some question types)
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();

            // Correct answer stored as free text (to support multiple types)
            $table->text('correct_answer')->nullable();

            $table->decimal('marks', 8, 2)->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
