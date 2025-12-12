<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('classes', 'form_level')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('form_level', 50)->after('id')->nullable();
            });
        }

        if (!Schema::hasColumn('classes', 'name')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('name')->nullable()->after('form_level');
            });
        }

        if (!Schema::hasColumn('classes', 'academic_session')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('academic_session')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('classes', 'homeroom_teacher_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->unsignedBigInteger('homeroom_teacher_id')->nullable()->after('academic_session');
            });
        }
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'form_level')) {
                $table->dropColumn('form_level');
            }
            if (Schema::hasColumn('classes', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('classes', 'academic_session')) {
                $table->dropColumn('academic_session');
            }
            if (Schema::hasColumn('classes', 'homeroom_teacher_id')) {
                $table->dropColumn('homeroom_teacher_id');
            }
        });
    }
};
