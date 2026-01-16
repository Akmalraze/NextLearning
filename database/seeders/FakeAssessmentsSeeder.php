<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Assessments;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentMaterial;
use App\Models\AssessmentSubmission;
use App\Models\SubjectClassTeacher;
use App\Models\Classes;
use App\Models\User;
use Carbon\Carbon;

class FakeAssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates random fake assessments with questions, materials, and submissions.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all subject-class-teacher assignments
        $assignments = SubjectClassTeacher::with(['class', 'subject', 'teacher'])->get();

        if ($assignments->isEmpty()) {
            $this->command->warn('No subject-class-teacher assignments found. Please run FakeSubjectClassTeacherSeeder first.');
            return;
        }

        $assessmentTypes = ['quiz', 'test', 'homework'];
        $assessmentCount = 0;
        $questionCount = 0;
        $materialCount = 0;
        $submissionCount = 0;

        foreach ($assignments as $assignment) {
            // Create 1-3 assessments per assignment
            $numAssessments = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $numAssessments; $i++) {
                $type = $faker->randomElement($assessmentTypes);
                
                // Set date ranges (some past, some current, some future)
                $dateRange = $faker->randomElement(['past', 'current', 'future']);
                $now = Carbon::now();
                
                if ($dateRange === 'past') {
                    $startDate = $faker->dateTimeBetween('-3 months', '-1 week');
                    $endDate = $faker->dateTimeBetween($startDate, '-1 day');
                    $dueDate = $faker->dateTimeBetween($startDate, $endDate);
                } elseif ($dateRange === 'current') {
                    $startDate = $faker->dateTimeBetween('-1 week', 'now');
                    $endDate = $faker->dateTimeBetween('now', '+2 weeks');
                    $dueDate = $faker->dateTimeBetween('now', $endDate);
                } else { // future
                    $startDate = $faker->dateTimeBetween('+1 week', '+1 month');
                    $endDate = $faker->dateTimeBetween($startDate, '+2 months');
                    $dueDate = $faker->dateTimeBetween($startDate, $endDate);
                }

                // Quiz-specific fields
                $timeLimit = null;
                $maxAttempts = null;
                $showMarks = true;
                
                if ($type === 'quiz') {
                    $timeLimit = $faker->randomElement([15, 30, 45, 60, 90, 120]); // minutes
                    $maxAttempts = $faker->randomElement([null, 1, 2, 3, 5]); // null = unlimited
                    $showMarks = $faker->boolean(80); // 80% chance to show marks
                }

                // Calculate total marks
                $totalMarks = $type === 'quiz' 
                    ? $faker->numberBetween(20, 100) 
                    : $faker->numberBetween(50, 100);

                $assessment = Assessments::create([
                    'title' => $this->generateAssessmentTitle($faker, $type, $assignment->subject->name),
                    'description' => $faker->optional(0.7)->paragraph(),
                    'type' => $type,
                    'class_id' => $assignment->class_id,
                    'subject_id' => $assignment->subject_id,
                    'teacher_id' => $assignment->teacher_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'total_marks' => $totalMarks,
                    'is_published' => $faker->boolean(90), // 90% published
                    'time_limit' => $timeLimit,
                    'max_attempts' => $maxAttempts,
                    'show_marks' => $showMarks,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now(),
                ]);

                $assessmentCount++;

                // Create questions for quizzes
                if ($type === 'quiz') {
                    $numQuestions = $faker->numberBetween(5, 15);
                    $questionMarks = $totalMarks / $numQuestions;
                    
                    for ($q = 1; $q <= $numQuestions; $q++) {
                        $questionType = $faker->randomElement(['multiple_choice', 'checkboxes', 'short_answer']);
                        
                        $question = AssessmentQuestion::create([
                            'assessment_id' => $assessment->id,
                            'question' => $this->generateQuestion($faker, $assignment->subject->name),
                            'question_type' => $questionType,
                            'option_a' => $faker->sentence(),
                            'option_b' => $faker->sentence(),
                            'option_c' => $faker->sentence(),
                            'option_d' => $faker->sentence(),
                            'correct_answer' => $this->generateCorrectAnswer($faker, $questionType),
                            'shuffle_options' => $faker->boolean(50),
                            'marks' => round($questionMarks, 2),
                            'order' => $q,
                        ]);
                        
                        $questionCount++;
                    }
                }

                // Create materials for tests and homework
                if (in_array($type, ['test', 'homework'])) {
                    $numMaterials = $faker->numberBetween(1, 3);
                    
                    for ($m = 1; $m <= $numMaterials; $m++) {
                        $fileTypes = ['pdf', 'docx', 'doc', 'xlsx', 'pptx'];
                        $fileType = $faker->randomElement($fileTypes);
                        $fileName = $faker->word() . '.' . $fileType;
                        
                        AssessmentMaterial::create([
                            'assessment_id' => $assessment->id,
                            'file_name' => $fileName,
                            'file_path' => 'materials/' . $fileName, // Fake path
                            'file_type' => $fileType,
                            'file_size' => $faker->numberBetween(100000, 5000000), // 100KB to 5MB
                            'description' => $faker->optional(0.6)->sentence(),
                            'created_at' => $faker->dateTimeBetween($assessment->created_at, 'now'),
                            'updated_at' => now(),
                        ]);
                        
                        $materialCount++;
                    }
                }

                // Create submissions from students in the class
                $class = Classes::find($assignment->class_id);
                $students = $class ? $class->activeStudents()->get() : collect([]);
                
                if ($students->isNotEmpty()) {
                    foreach ($students as $student) {
                        // 70% chance student has submitted, 20% in progress, 10% not started
                        $rand = $faker->numberBetween(1, 100);
                        if ($rand <= 70) {
                            $submissionStatus = 'submitted';
                        } elseif ($rand <= 90) {
                            $submissionStatus = 'in_progress';
                        } else {
                            $submissionStatus = 'not_started';
                        }

                        if ($type === 'quiz') {
                            // Handle quiz submissions with attempts
                            $this->createQuizSubmissions($faker, $assessment, $student, $submissionStatus);
                            $submissionCount++;
                        } else {
                            // Handle test/homework submissions
                            $this->createTestHomeworkSubmission($faker, $assessment, $student, $submissionStatus);
                            $submissionCount++;
                        }
                    }
                }
            }
        }

        $this->command->info("Created {$assessmentCount} assessments");
        $this->command->info("Created {$questionCount} questions");
        $this->command->info("Created {$materialCount} materials");
        $this->command->info("Created {$submissionCount} submissions");
    }

    /**
     * Generate assessment title based on type and subject
     */
    private function generateAssessmentTitle($faker, $type, $subjectName): string
    {
        $titles = [
            'quiz' => [
                "{$subjectName} Quiz - Chapter {chapter}",
                "{$subjectName} Quick Quiz",
                "{$subjectName} Practice Quiz",
                "{$subjectName} Weekly Quiz",
            ],
            'test' => [
                "{$subjectName} Test - {topic}",
                "{$subjectName} Mid-Term Test",
                "{$subjectName} Assessment Test",
                "{$subjectName} Unit Test",
            ],
            'homework' => [
                "{$subjectName} Homework - {topic}",
                "{$subjectName} Assignment",
                "{$subjectName} Exercise",
                "{$subjectName} Practice Work",
            ],
        ];

        $template = $faker->randomElement($titles[$type]);
        $chapter = $faker->numberBetween(1, 10);
        $topic = $faker->randomElement(['Algebra', 'Grammar', 'Reading', 'Writing', 'Calculus', 'Biology', 'Chemistry']);
        
        return str_replace(['{chapter}', '{topic}'], [$chapter, $topic], $template);
    }

    /**
     * Generate a question based on subject
     */
    private function generateQuestion($faker, $subjectName): string
    {
        $questionTemplates = [
            "What is the main concept of {topic}?",
            "Explain the difference between {term1} and {term2}.",
            "Which of the following best describes {concept}?",
            "Calculate the value of {expression}.",
            "What happens when {action}?",
            "Identify the correct statement about {topic}.",
        ];

        $template = $faker->randomElement($questionTemplates);
        $topic = $faker->word();
        $term1 = $faker->word();
        $term2 = $faker->word();
        $concept = $faker->word();
        $expression = $faker->numerify('x + #');
        $action = $faker->sentence(3);

        return str_replace(
            ['{topic}', '{term1}', '{term2}', '{concept}', '{expression}', '{action}'],
            [$topic, $term1, $term2, $concept, $expression, $action],
            $template
        );
    }

    /**
     * Generate correct answer based on question type
     */
    private function generateCorrectAnswer($faker, $questionType): string
    {
        if ($questionType === 'multiple_choice') {
            return $faker->randomElement(['a', 'b', 'c', 'd']);
        } elseif ($questionType === 'checkboxes') {
            $answers = $faker->randomElements(['0', '1', '2', '3'], $faker->numberBetween(1, 3));
            return implode(',', $answers);
        } else { // short_answer
            return $faker->word();
        }
    }

    /**
     * Create quiz submissions with multiple attempts if allowed
     */
    private function createQuizSubmissions($faker, $assessment, $student, $submissionStatus): void
    {
        if ($submissionStatus === 'not_started') {
            return; // No submission created
        }

        // Determine number of attempts
        $maxAttempts = $assessment->max_attempts;
        $numAttempts = 1;
        
        if ($maxAttempts === null) {
            // Unlimited attempts - create 1-3 attempts
            $numAttempts = $faker->numberBetween(1, 3);
        } elseif ($maxAttempts > 1) {
            // Limited attempts - create 1 to max_attempts
            $numAttempts = $faker->numberBetween(1, $maxAttempts);
        }

        $highestScore = 0;

        for ($attempt = 1; $attempt <= $numAttempts; $attempt++) {
            $startedAt = $faker->dateTimeBetween($assessment->start_date, 'now');
            
            if ($submissionStatus === 'in_progress' && $attempt === $numAttempts) {
                // Last attempt is in progress (not submitted)
                AssessmentSubmission::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $student->id,
                    'type' => 'quiz',
                    'attempt_number' => $attempt,
                    'answers' => $this->generateQuizAnswers($faker, $assessment),
                    'score' => null,
                    'started_at' => $startedAt,
                    'submitted_at' => null,
                ]);
            } else {
                // Submitted attempt
                $submittedAt = $faker->dateTimeBetween($startedAt, 'now');
                $score = $faker->randomFloat(2, 0, $assessment->total_marks);
                $highestScore = max($highestScore, $score);

                AssessmentSubmission::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $student->id,
                    'type' => 'quiz',
                    'attempt_number' => $attempt,
                    'answers' => $this->generateQuizAnswers($faker, $assessment),
                    'score' => $score,
                    'started_at' => $startedAt,
                    'submitted_at' => $submittedAt,
                ]);
            }
        }
    }

    /**
     * Create test/homework submission
     */
    private function createTestHomeworkSubmission($faker, $assessment, $student, $submissionStatus): void
    {
        if ($submissionStatus === 'not_started') {
            return; // No submission created
        }

        $startedAt = $faker->dateTimeBetween($assessment->start_date, 'now');

        if ($submissionStatus === 'in_progress') {
            // In progress - not submitted yet
            AssessmentSubmission::create([
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'type' => $assessment->type,
                'attempt_number' => 1,
                'answers' => null,
                'answer_file_path' => null,
                'answer_original_name' => null,
                'score' => null,
                'started_at' => $startedAt,
                'submitted_at' => null,
            ]);
        } else {
            // Submitted
            $submittedAt = $faker->dateTimeBetween($startedAt, min($assessment->end_date, Carbon::now()));
            $fileTypes = ['pdf', 'docx', 'doc'];
            $fileType = $faker->randomElement($fileTypes);
            $fileName = $faker->word() . '.' . $fileType;

            AssessmentSubmission::create([
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'type' => $assessment->type,
                'attempt_number' => 1,
                'answers' => null,
                'answer_file_path' => 'submissions/' . $fileName, // Fake path
                'answer_original_name' => $fileName,
                'score' => $faker->optional(0.6)->randomFloat(2, 0, $assessment->total_marks), // 60% chance teacher has graded
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
            ]);
        }
    }

    /**
     * Generate quiz answers based on assessment questions
     */
    private function generateQuizAnswers($faker, $assessment): array
    {
        $answers = [];
        $questions = $assessment->questions()->orderBy('order')->get();

        foreach ($questions as $question) {
            if ($question->question_type === 'multiple_choice') {
                $answers[$question->id] = $faker->randomElement(['a', 'b', 'c', 'd']);
            } elseif ($question->question_type === 'checkboxes') {
                $selected = $faker->randomElements(['0', '1', '2', '3'], $faker->numberBetween(0, 4));
                $answers[$question->id] = $selected;
            } else { // short_answer
                $answers[$question->id] = $faker->word();
            }
        }

        return $answers;
    }
}

