<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Subjects;

class FakeSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates random fake subjects data.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Realistic school subjects with codes
        $subjectData = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Study of numbers, quantities, and shapes'],
            ['name' => 'English Language', 'code' => 'ENG', 'description' => 'Study of English grammar, literature, and composition'],
            ['name' => 'Bahasa Melayu', 'code' => 'BM', 'description' => 'Study of Malay language and literature'],
            ['name' => 'Science', 'code' => 'SCI', 'description' => 'General science covering physics, chemistry, and biology'],
            ['name' => 'Physics', 'code' => 'PHY', 'description' => 'Study of matter, energy, and their interactions'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'description' => 'Study of substances and chemical reactions'],
            ['name' => 'Biology', 'code' => 'BIO', 'description' => 'Study of living organisms and life processes'],
            ['name' => 'History', 'code' => 'HIST', 'description' => 'Study of past events and civilizations'],
            ['name' => 'Geography', 'code' => 'GEO', 'description' => 'Study of places, landscapes, and environment'],
            ['name' => 'Additional Mathematics', 'code' => 'ADDM', 'description' => 'Advanced mathematical concepts and calculus'],
            ['name' => 'Accounting', 'code' => 'ACC', 'description' => 'Study of financial record keeping and reporting'],
            ['name' => 'Economics', 'code' => 'ECON', 'description' => 'Study of production, distribution, and consumption'],
            ['name' => 'Islamic Studies', 'code' => 'ISLA', 'description' => 'Study of Islamic principles and practices'],
            ['name' => 'Moral Education', 'code' => 'MORA', 'description' => 'Study of ethics and moral values'],
            ['name' => 'Physical Education', 'code' => 'PE', 'description' => 'Physical fitness and sports activities'],
            ['name' => 'Art Education', 'code' => 'ART', 'description' => 'Study of visual arts and creative expression'],
            ['name' => 'Music', 'code' => 'MUS', 'description' => 'Study of musical theory and practice'],
            ['name' => 'Computer Science', 'code' => 'CS', 'description' => 'Study of computing and programming'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Study of information systems and technology'],
            ['name' => 'Chinese Language', 'code' => 'CHI', 'description' => 'Study of Mandarin Chinese language'],
            ['name' => 'Tamil Language', 'code' => 'TAM', 'description' => 'Study of Tamil language and literature'],
            ['name' => 'Business Studies', 'code' => 'BUS', 'description' => 'Study of business operations and management'],
        ];

        foreach ($subjectData as $subject) {
            // Check if subject already exists
            if (!Subjects::where('code', $subject['code'])->exists()) {
                Subjects::create([
                    'name' => $subject['name'],
                    'code' => $subject['code'],
                    'description' => $subject['description'],
                    'is_active' => $faker->randomElement([true, true, true, false]), // 75% active
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }

        $count = Subjects::count();
    }
}
