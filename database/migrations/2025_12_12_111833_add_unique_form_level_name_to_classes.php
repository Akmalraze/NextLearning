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
        Schema::table('classes', function (Blueprint $table) {
            // Change form_level to integer (1-5)
            $table->unsignedTinyInteger('form_level')->nullable()->change();
        });

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
