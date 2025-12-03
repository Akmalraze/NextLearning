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
         // Seed Subjects
        DB::table('subjects')->insert([
            [
                'subjects_name' => 'Mathematics',
                'subjects_totalStudent' => 100,
            ],
            [
                'subjects_name' => 'Science',
                'subjects_totalStudent' => 80,
            ],
            [
                'subjects_name' => 'History',
                'subjects_totalStudent' => 50,
            ],
        ]);
    }
}
