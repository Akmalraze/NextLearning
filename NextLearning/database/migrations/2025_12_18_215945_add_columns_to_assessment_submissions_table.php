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
        Schema::table('assessment_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_submissions', 'assessment_id')) {
                $table->unsignedBigInteger('assessment_id')->after('id');
                $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            }
            if (!Schema::hasColumn('assessment_submissions', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('assessment_id');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('assessment_submissions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('assessment_submissions', 'type')) {
                $table->enum('type', ['quiz', 'homework', 'test'])->after('started_at');
            }
            if (!Schema::hasColumn('assessment_submissions', 'answers')) {
                $table->json('answers')->nullable()->after('type');
            }
            if (!Schema::hasColumn('assessment_submissions', 'answer_file_path')) {
                $table->string('answer_file_path')->nullable()->after('answers');
            }
            if (!Schema::hasColumn('assessment_submissions', 'answer_original_name')) {
                $table->string('answer_original_name')->nullable()->after('answer_file_path');
            }
            if (!Schema::hasColumn('assessment_submissions', 'score')) {
                $table->decimal('score', 8, 2)->nullable()->after('answer_original_name');
            }
            if (!Schema::hasColumn('assessment_submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('score');
            }
            
            // Check if index exists before creating
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('assessment_submissions');
            if (!isset($indexesFound['index_assessment_student'])) {
                $table->index(['assessment_id', 'student_id'], 'index_assessment_student');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_submissions', 'assessment_id')) {
                $table->dropForeign(['assessment_id']);
            }
            if (Schema::hasColumn('assessment_submissions', 'student_id')) {
                $table->dropForeign(['student_id']);
            }
            $table->dropIndex('index_assessment_student');
            $columnsToDrop = [
                'assessment_id',
                'student_id',
                'started_at',
                'type',
                'answers',
                'answer_file_path',
                'answer_original_name',
                'score',
                'submitted_at'
            ];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('assessment_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
