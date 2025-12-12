<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Classes;
use App\Models\User;

class FakeClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates random fake classes data.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all teachers to assign as homeroom teachers
        $teachers = User::role('Teacher')->pluck('id')->toArray();

        // Use form levels and class names from the model
        $formLevels = Classes::FORM_LEVELS;
        $classNames = Classes::CLASS_NAMES;
        $academicSessions = ['2023/2024', '2024/2025', '2025/2026'];

        $usedTeachers = [];
        $classCount = 0;

        foreach ($formLevels as $formLevel) {
            // Create 2-4 classes per form level
            $numClasses = $faker->numberBetween(2, 4);

            for ($i = 0; $i < $numClasses && $i < count($classNames); $i++) {
                $className = $classNames[$i];

                // Assign a homeroom teacher (optional, avoid duplicates if possible)
                $homeroomTeacherId = null;
                if (!empty($teachers)) {
                    $availableTeachers = array_diff($teachers, $usedTeachers);
                    if (!empty($availableTeachers)) {
                        $homeroomTeacherId = $faker->randomElement($availableTeachers);
                        $usedTeachers[] = $homeroomTeacherId;
                    } else {
                        // If all teachers are used, allow reuse
                        $homeroomTeacherId = $faker->randomElement($teachers);
                    }
                }

                $class = Classes::firstOrCreate(
                    [
                        'form_level' => $formLevel,
                        'name' => $className,
                    ],
                    [
                        'academic_session' => $faker->randomElement($academicSessions),
                        'homeroom_teacher_id' => $homeroomTeacherId,
                    ]
                );

                if ($class->wasRecentlyCreated) {
                    $classCount++;
                }
            }
        }
    }
}
