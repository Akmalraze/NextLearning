<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop redundant columns from classes table.
     * We now use form_level + name instead of class_name.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Drop redundant columns
            if (Schema::hasColumn('classes', 'class_name')) {
                $table->dropColumn('class_name');
            }
            if (Schema::hasColumn('classes', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('classes', 'subjects_id')) {
                $table->dropColumn('subjects_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('class_name')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('subjects_id')->nullable();
        });
    }
};
