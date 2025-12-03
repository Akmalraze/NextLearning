<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Materials
        DB::table('materials_info')->insert([
            [
                'modules_id' => 1, // Relating to Basic Algebra
                'materials_format' => 'PDF',
                'materials_uploadDate' => '2025-12-01',
                'materials_notes' => 'Introduction to Algebra concepts.',
            ],
            [
                'modules_id' => 2, // Relating to Physics and Chemistry
                'materials_format' => 'Video',
                'materials_uploadDate' => '2025-12-02',
                'materials_notes' => 'Overview of chemical reactions and physics principles.',
            ],
            [
                'modules_id' => 3, // Relating to World War II
                'materials_format' => 'PDF',
                'materials_uploadDate' => '2025-12-03',
                'materials_notes' => 'Detailed timeline and key events of World War II.',
            ],
        ]);
    }
}
