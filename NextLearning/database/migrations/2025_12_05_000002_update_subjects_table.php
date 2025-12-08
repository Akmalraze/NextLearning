<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if form_level column exists before dropping
        if (Schema::hasColumn('subjects', 'form_level')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('form_level');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('subjects', 'form_level')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('form_level', 50)->after('code');
            });
        }
    }
};
