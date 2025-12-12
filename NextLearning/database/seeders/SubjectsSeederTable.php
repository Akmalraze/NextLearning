<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Subjects (without form_level - it's now on classes table)
        DB::table('subjects')->insert([
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Mathematical concepts and problem solving',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Science',
                'code' => 'SCI',
                'description' => 'General science subjects',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'History',
                'code' => 'HIST',
                'description' => 'Historical studies',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
