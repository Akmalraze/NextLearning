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
        // Create Materials Table
        Schema::create('materials', function (Blueprint $table) {
            $table->id('materials_id');
            $table->foreignId('modules_id')->constrained('modules')->onDelete('cascade');
            $table->string('materials_format');
            $table->date('materials_uploadDate');
            $table->text('materials_notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
