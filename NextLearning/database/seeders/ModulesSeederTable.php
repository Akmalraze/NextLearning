<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Seed Classes
        DB::table('classes')->insert([
            [
                'class_name' => 'Class A',
                'subjects_id' => 1, // Relating to Mathematics
            ],
            [
                'class_name' => 'Class B',
                'subjects_id' => 2, // Relating to Science
            ],
            [
                'class_name' => 'Class C',
                'subjects_id' => 3, // Relating to History
            ],
        ]);
    }
}
