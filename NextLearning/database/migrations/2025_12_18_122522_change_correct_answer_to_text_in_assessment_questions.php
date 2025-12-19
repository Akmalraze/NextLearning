<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to change enum to text since Laravel doesn't support enum->text directly
        DB::statement('ALTER TABLE assessment_questions MODIFY correct_answer TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot fully revert to enum without data loss, so we'll just change back to text
        DB::statement('ALTER TABLE assessment_questions MODIFY correct_answer TEXT NULL');
    }
};
