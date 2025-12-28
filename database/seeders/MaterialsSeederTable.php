<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MaterialsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // Math Materials (subject_id = 1)
            [
                'materials_name' => 'Math Textbook',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Textbook for Numbers & Algebra, Geometry, and Measurement & Data.',
                'module_id' => 1, // Numbers & Algebra
                'subject_id' => 1, // Math
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'materials_name' => 'Math Practice Exercises',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Practice exercises for Probability & Patterns.',
                'module_id' => 4, // Probability & Patterns
                'subject_id' => 1, // Math
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Science Materials (subject_id = 4)
            [
                'materials_name' => 'Science Workbook',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Workbook covering Matter & Materials and Force & Motion.',
                'module_id' => 5, // Matter & Materials
                'subject_id' => 4, // Science
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'materials_name' => 'Energy & Living Things',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Educational video on Energy and Living Things.',
                'module_id' => 7, // Living Things
                'subject_id' => 4, // Science
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // History Materials (subject_id = 8)
            [
                'materials_name' => 'World Wars Overview',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Summary of World War I and II with a focus on Southeast Asia.',
                'module_id' => 9, // World Wars
                'subject_id' => 8, // History
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'materials_name' => 'Malaysian History Slides',
                'file_path' => $this->storeFile('topic.pptx'),
                'materials_uploadDate' => now(),
                'materials_notes' => 'Presentation slides covering Malaysian history from early kingdoms to independence.',
                'module_id' => 10, // National Heroes & Events
                'subject_id' => 8, // History
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert materials into the database
        DB::table('materials')->insert($materials);
    }

    /**
     * Store the file and return its path.
     */
    private function storeFile($filename)
    {
        // Define the file path in the public directory
        $filePath = public_path('materials/' . $filename);

        // Check if the file exists in the public folder
        if (file_exists($filePath)) {
            // Store the file in the public storage directory
            $storedFilePath = Storage::disk('public')->putFileAs('materials', new File($filePath), $filename);
            return 'materials/' . $storedFilePath;  // Store the path for database
        }

        // Return null if file doesn't exist
        return null;
    }
}
