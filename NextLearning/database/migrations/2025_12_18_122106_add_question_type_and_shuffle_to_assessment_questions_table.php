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
        Schema::table('assessment_questions', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('assessment_questions', 'question_type')) {
                $table->enum('question_type', ['multiple_choice', 'checkboxes', 'short_answer'])->default('multiple_choice')->after('question');
            }
            if (!Schema::hasColumn('assessment_questions', 'shuffle_options')) {
                $table->boolean('shuffle_options')->default(false)->after('question_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['question_type', 'shuffle_options']);
            // Revert correct_answer back to enum (might need to handle existing data first)
            $table->enum('correct_answer', ['a', 'b', 'c', 'd'])->nullable()->change();
        });
    }
};
