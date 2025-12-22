<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds unique constraint on form_level + name combination.
     */
    public function up(): void
    {
        // Change form_level to unsigned tiny integer using a raw statement to avoid
        // Doctrine DBAL type issues with "tinyinteger" when using ->change().
        if (Schema::hasColumn('classes', 'form_level')) {
            // This is MySQL-specific, which matches the current project setup.
            \Illuminate\Support\Facades\DB::statement(
                'ALTER TABLE classes MODIFY form_level TINYINT UNSIGNED NULL'
            );
        }

        // Add unique constraint on form_level + name
        Schema::table('classes', function (Blueprint $table) {
            $table->unique(['form_level', 'name'], 'unique_form_level_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropUnique('unique_form_level_name');
        });
    }
};
