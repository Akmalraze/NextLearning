<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Classes
        DB::table('class')->insert([
            [
                'class_name' => 'Form1 A',
                'subjects_id' => 1, // Relating to Mathematics
            ],
            [
                'class_name' => 'Form1 B',
                'subjects_id' => 1, // Relating to Science
            ],
            [
                'class_name' => 'Form1 C',
                'subjects_id' => 1, // Relating to History
            ],
        ]);
    }
}
