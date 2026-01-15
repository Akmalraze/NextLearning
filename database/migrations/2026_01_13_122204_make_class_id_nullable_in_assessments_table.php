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
        Schema::table('assessments', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['class_id']);
            
            // Make the column nullable
            $table->foreignId('class_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['class_id']);
            
            // Make the column not nullable
            $table->foreignId('class_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }
};
