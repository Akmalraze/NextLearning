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
        Schema::table('users', function (Blueprint $table) {
            // Add additional fields if they don't exist
            if (!Schema::hasColumn('users', 'id_number')) {
                $table->string('id_number')->unique()->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('id_number');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(1)->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_number', 'photo_path', 'status']);
        });
    }
};
