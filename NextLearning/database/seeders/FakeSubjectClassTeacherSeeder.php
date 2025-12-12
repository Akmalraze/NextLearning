<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\SubjectClassTeacher;
use App\Models\Classes;
use App\Models\Subjects;
use App\Models\User;

class FakeSubjectClassTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates random subject-class-teacher assignments.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all teachers, classes, and subjects
        $teachers = User::role('Teacher')->pluck('id')->toArray();
        $classes = Classes::pluck('id')->toArray();
        $subjects = Subjects::where('is_active', true)->pluck('id')->toArray();

        $assignmentCount = 0;

        // For each class, assign 5-10 subjects with teachers
        foreach ($classes as $classId) {
            // Randomly select subjects for this class
            $numSubjects = min($faker->numberBetween(5, 10), count($subjects));
            $selectedSubjects = $faker->randomElements($subjects, $numSubjects);

            foreach ($selectedSubjects as $subjectId) {
                // Select a random teacher for this subject-class combination
                $teacherId = $faker->randomElement($teachers);

                // Use firstOrCreate to prevent duplicates (checks database)
                $assignment = SubjectClassTeacher::firstOrCreate(
                    [
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'teacher_id' => $teacherId,
                        'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                        'updated_at' => now(),
                    ]
                );

                if ($assignment->wasRecentlyCreated) {
                    $assignmentCount++;
                }
            }
        }
    }
}
