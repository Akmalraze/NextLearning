<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make class_name nullable since we're using form_level + name now.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Make class_name nullable (legacy column)
            $table->string('class_name')->nullable()->change();

            // Also make user_id and subjects_id nullable if they exist
            if (Schema::hasColumn('classes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            if (Schema::hasColumn('classes', 'subjects_id')) {
                $table->unsignedBigInteger('subjects_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('class_name')->nullable(false)->change();
        });
    }
};
