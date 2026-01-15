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
        Schema::table('subjects', function (Blueprint $table) {
            // Educator who owns/publishes the course
            $table->foreignId('educator_id')
                ->nullable()
                ->after('description')
                ->constrained('users')
                ->nullOnDelete();

            // Simple published flag (separate from is_active for flexibility)
            $table->boolean('is_published')
                ->default(false)
                ->after('is_active');
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('learner_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('active'); // active, cancelled, completed etc.
            $table->timestamps();

            $table->unique(['subject_id', 'learner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'is_published')) {
                $table->dropColumn('is_published');
            }
            if (Schema::hasColumn('subjects', 'educator_id')) {
                $table->dropConstrainedForeignId('educator_id');
            }
        });

        Schema::dropIfExists('course_enrollments');
    }
};




