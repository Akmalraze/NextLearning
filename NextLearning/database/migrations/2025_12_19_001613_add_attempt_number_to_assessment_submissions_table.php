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
            $table->integer('attempt_number')->default(1)->after('student_id')->comment('Attempt number for this submission (1, 2, 3, etc.)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_submissions', function (Blueprint $table) {
            $table->dropColumn('attempt_number');
        });
    }
};
