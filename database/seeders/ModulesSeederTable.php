<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // Math (subject_id = 1)
            [
                'modules_name' => 'Numbers & Algebra',
                'modules_description' => 'Understanding integers, fractions, decimals, percentages, and basic algebraic expressions.',
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Geometry',
                'modules_description' => 'Basic shapes, perimeter, area, volume, and angles.',
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Measurement & Data',
                'modules_description' => 'Units of measurement, data collection, graphs, and statistics.',
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Probability & Patterns',
                'modules_description' => 'Simple probability, sequences, and identifying patterns.',
                'subject_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Science (subject_id = 4)
            [
                'modules_name' => 'Matter & Materials',
                'modules_description' => 'Properties of solids, liquids, gases, and changes in states of matter.',
                'subject_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Force & Motion',
                'modules_description' => 'Understanding forces, speed, motion, and simple machines.',
                'subject_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Energy',
                'modules_description' => 'Forms of energy, energy transfer, and conservation of energy.',
                'subject_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Living Things',
                'modules_description' => 'Basic biology: plants, animals, human body, and ecosystems.',
                'subject_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // History (subject_id = 8)
            [
                'modules_name' => 'Early Civilizations',
                'modules_description' => 'Introduction to ancient civilizations, culture, and society.',
                'subject_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'Malaysian History',
                'modules_description' => 'Early kingdoms, Malacca Sultanate, and colonial history.',
                'subject_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'World Wars',
                'modules_description' => 'Overview of World War I and II and their impact on Southeast Asia.',
                'subject_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'modules_name' => 'National Heroes & Events',
                'modules_description' => 'Key figures and events in Malaysian history leading to independence.',
                'subject_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modules')->insert($modules);
    }
}
