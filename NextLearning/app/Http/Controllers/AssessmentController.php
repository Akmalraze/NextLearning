<?php

namespace App\Http\Controllers;

use App\Models\Assessments;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentMaterial;
use App\Models\AssessmentSubmission;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $search = $request->get('search');
        $type = $request->get('type');
        
        // Initialize variables
        $classSubjectCombos = collect([]);
        $selectedClass = null;
        $selectedSubject = null;
        $assessments = collect([]);

        // Get class-subject assignments for teacher
        if (auth()->user()->hasRole('Teacher')) {
            $teacher = auth()->user();
            $assignments = $teacher->teachingAssignments()->with(['class', 'subject'])->get();
            
            // Group assignments by class-subject combination
            $classSubjectCombos = [];
            foreach ($assignments as $assignment) {
                $key = $assignment->class_id . '_' . $assignment->subject_id;
                if (!isset($classSubjectCombos[$key])) {
                    $classSubjectCombos[$key] = [
                        'class_id' => $assignment->class_id,
                        'subject_id' => $assignment->subject_id,
                        'class' => $assignment->class,
                        'subject' => $assignment->subject,
                    ];
                }
            }
            $classSubjectCombos = collect($classSubjectCombos)->values();

            // If class and subject are selected, show assessments
            if ($classId && $subjectId) {
                // Verify teacher is assigned to this class-subject
                $assignment = $assignments->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->first();
                
                if (!$assignment) {
                    return redirect()->route('assessments.index')
                        ->with('error', 'You are not assigned to teach this subject in this class.');
                }

                $selectedClass = Classes::find($classId);
                $selectedSubject = Subjects::find($subjectId);

                $query = Assessments::where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->where('teacher_id', auth()->id())
                    ->with(['class', 'subject', 'teacher']);

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                if ($type) {
                    $query->where('type', $type);
                }

                // Sort by due_date (earliest first), then by created_at for those without due_date
                $assessments = $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_date', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15)->withQueryString();
                
                return view('pages.ManageAssessment.index', compact(
                    'assessments', 
                    'classSubjectCombos', 
                    'classId', 
                    'subjectId', 
                    'selectedClass', 
                    'selectedSubject',
                    'search',
                    'type'
                ));
            }

            // Show class-subject cards - initialize empty variables
            $assessments = collect([]);
            $selectedClass = null;
            $selectedSubject = null;
            
            return view('pages.ManageAssessment.index', compact(
                'assessments', 
                'classSubjectCombos', 
                'classId', 
                'subjectId', 
                'selectedClass', 
                'selectedSubject',
                'search',
                'type'
            ));
        } elseif (auth()->user()->hasRole('Student')) {
            // Students see only published assessments for their active class
            $student = auth()->user();
            $studentClass = $student->activeClass()->first();
            
            if (!$studentClass) {
                // Initialize all variables for student view
                $assessments = collect([]);
                $classSubjectCombos = collect([]);
                $classId = null;
                $subjectId = null;
                $selectedClass = null;
                $selectedSubject = null;
                $message = 'You are not enrolled in any class.';
                
                return view('pages.ManageAssessment.index', compact(
                    'assessments', 
                    'classSubjectCombos', 
                    'classId', 
                    'subjectId', 
                    'selectedClass', 
                    'selectedSubject',
                    'search',
                    'type',
                    'message'
                ));
            }

            $now = now();
            
            // Get all published assessments for the student's class
            $query = Assessments::where('class_id', $studentClass->id)
                ->where('is_published', true)
                ->with(['class', 'subject', 'teacher']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($type) {
                $query->where('type', $type);
            }

            // Get all assessments (without pagination for separation)
            $allAssessments = $query->get();
            
            // Load submission status for each assessment
            $submissions = collect([]);
            if ($allAssessments->count() > 0) {
                $assessmentIds = $allAssessments->pluck('id');
                $submissions = AssessmentSubmission::whereIn('assessment_id', $assessmentIds)
                    ->where('student_id', $student->id)
                    ->get()
                    ->keyBy('assessment_id');
            }
            
            // Separate into: active (not submitted), submitted, and expired (not submitted)
            $expiredAssessments = collect([]);
            $activeAssessments = collect([]);
            $submittedAssessments = collect([]);
            
            foreach ($allAssessments as $assessment) {
                $hasEnded = $assessment->end_date !== null && $now->gt($assessment->end_date);
                $submission = $submissions[$assessment->id] ?? null;
                $isSubmitted = $submission && $submission->submitted_at;
                
                // First check if submitted - submitted assessments go to their own table
                if ($isSubmitted) {
                    $submittedAssessments->push($assessment);
                } elseif ($hasEnded) {
                    // Expired and not submitted
                    $expiredAssessments->push($assessment);
                } else {
                    // Active and not submitted
                    $activeAssessments->push($assessment);
                }
            }
            
            // Sort active assessments by end_date (earliest expiry first) - priority order
            $activeAssessments = $activeAssessments->sortBy(function ($assessment) {
                // If no end_date, put at the end
                return $assessment->end_date ? $assessment->end_date->timestamp : PHP_INT_MAX;
            })->values();
            
            // Sort submitted assessments by submitted_at (most recently submitted first)
            $submittedAssessments = $submittedAssessments->sortByDesc(function ($assessment) use ($submissions) {
                $submission = $submissions[$assessment->id] ?? null;
                return $submission && $submission->submitted_at ? $submission->submitted_at->timestamp : 0;
            })->values();
            
            // Sort expired assessments by end_date (most recently expired first)
            $expiredAssessments = $expiredAssessments->sortByDesc(function ($assessment) {
                return $assessment->end_date ? $assessment->end_date->timestamp : 0;
            })->values();
            
            // Initialize variables for student view
            $classSubjectCombos = collect([]);
            $classId = null;
            $subjectId = null;
            $selectedClass = null;
            $selectedSubject = null;
            
            return view('pages.ManageAssessment.index', compact(
                'activeAssessments',
                'submittedAssessments',
                'expiredAssessments',
                'classSubjectCombos', 
                'classId', 
                'subjectId', 
                'selectedClass', 
                'selectedSubject',
                'search',
                'type',
                'submissions'
            ));
        } else {
            abort(403, 'Access denied. Only teachers and students can view assessments.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Only teachers can create assessments
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can create assessments.');
        }

        $teacher = auth()->user();
        $preSelectedClassId = $request->get('class_id');
        $preSelectedSubjectId = $request->get('subject_id');
        
        // Get classes and subjects from teacher's assignments
        $assignments = $teacher->teachingAssignments()->with(['class', 'subject'])->get();
        
        // Get unique classes and subjects
        $classes = $assignments->pluck('class')->unique('id')->filter()->values();
        $subjects = $assignments->pluck('subject')->unique('id')->filter()->values();

        // If class and subject are pre-selected, verify teacher is assigned
        if ($preSelectedClassId && $preSelectedSubjectId) {
            $assignment = $assignments->where('class_id', $preSelectedClassId)
                ->where('subject_id', $preSelectedSubjectId)
                ->first();
            
            if (!$assignment) {
                return redirect()->route('assessments.index')
                    ->with('error', 'You are not assigned to teach this subject in this class.');
            }
        }

        return view('pages.ManageAssessment.create', compact('classes', 'subjects', 'preSelectedClassId', 'preSelectedSubjectId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only teachers can create assessments
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can create assessments.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,test,homework',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|numeric|min:0|max:1000',
            'time_limit' => 'nullable|integer|min:1|required_if:type,quiz',
            'max_attempts' => 'nullable|integer|min:1',
            'show_marks' => 'boolean',
            'is_published' => 'boolean',
            // Questions for quiz
            'questions' => 'required_if:type,quiz|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.question_type' => 'required_with:questions|in:multiple_choice,checkboxes,short_answer',
            'questions.*.options' => 'nullable|array|min:2',
            'questions.*.options.*' => 'required_with:questions.*.options|string',
            'questions.*.option_a' => 'nullable|string',
            'questions.*.option_b' => 'nullable|string',
            'questions.*.option_c' => 'nullable|string',
            'questions.*.option_d' => 'nullable|string',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.correct_answers' => 'nullable|array',
            'questions.*.shuffle_options' => 'boolean',
            'questions.*.marks' => 'required_with:questions|numeric|min:0',
            // Materials for homework/test - only required if type is homework or test
            'materials' => 'required_if:type,homework,test|array|min:1',
            'materials.*.file' => 'required_if:type,homework,test|file|max:10240',
            'materials.*.description' => 'nullable|string',
        ], [
            'title.required' => 'The assessment title field is required. Please fill in the title.',
            'type.required' => 'The assessment type field is required. Please select a type.',
            'type.in' => 'Please select a valid assessment type (Quiz, Test, or Homework).',
            'class_id.required' => 'The class field is required. Please select a class.',
            'class_id.exists' => 'The selected class is invalid. Please select a valid class.',
            'subject_id.required' => 'The subject field is required. Please select a subject.',
            'subject_id.exists' => 'The selected subject is invalid. Please select a valid subject.',
            'start_date.required' => 'The start date field is required. Please select a start date and time.',
            'start_date.date' => 'The start date must be a valid date and time.',
            'end_date.required' => 'The end date field is required. Please select an end date and time.',
            'end_date.date' => 'The end date must be a valid date and time.',
            'total_marks.required' => 'The total marks field is required. Please enter the total marks.',
            'total_marks.numeric' => 'The total marks must be a number.',
            'total_marks.min' => 'The total marks must be at least 0.',
            'total_marks.max' => 'The total marks cannot exceed 1000.',
            'time_limit.required_if' => 'The time limit field is required for quiz assessments. Please enter the time limit in minutes.',
            'time_limit.integer' => 'The time limit must be a whole number.',
            'time_limit.min' => 'The time limit must be at least 1 minute.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'questions.required_if' => 'At least one question is required for quiz assessments. Please add questions.',
            'questions.min' => 'At least one question is required for quiz assessments. Please add questions.',
            'questions.*.question.required_with' => 'The question text is required. Please fill in the question.',
            'questions.*.question_type.required_with' => 'The question type is required. Please select a question type.',
            'questions.*.marks.required_with' => 'The marks field is required for each question. Please enter marks.',
            'questions.*.marks.numeric' => 'The marks must be a number.',
            'questions.*.marks.min' => 'The marks must be at least 0.',
            'materials.required_if' => 'At least one material file is required for homework/test assessments. Please upload materials.',
            'materials.min' => 'At least one material file is required for homework/test assessments. Please upload materials.',
            'materials.*.file.required_if' => 'The material file is required. Please upload a file.',
            'materials.*.file.file' => 'The uploaded file must be a valid file.',
            'materials.*.file.max' => 'The file size cannot exceed 10MB.',
        ]);

        // Verify teacher is assigned to this class and subject
        $teacher = auth()->user();
        $assignment = $teacher->teachingAssignments()
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if (!$assignment) {
            return back()->withErrors(['class_id' => 'You are not assigned to teach this subject in this class.'])->withInput();
        }

        $validated['teacher_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        // Convert datetime-local format (Y-m-d\TH:i) to datetime format (Y-m-d H:i:s)
        $validated['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $validated['start_date'])));
        $validated['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $validated['end_date'])));

        // Create the assessment
        $assessment = Assessments::create($validated);

        // Handle questions for quiz
        if ($validated['type'] === 'quiz' && $request->has('questions')) {
            $order = 1;
            foreach ($request->input('questions') as $questionData) {
                $questionType = $questionData['question_type'] ?? 'multiple_choice';
                
                // Handle options - use new options array if available, otherwise fallback to old format
                $options = [];
                if (isset($questionData['options']) && is_array($questionData['options'])) {
                    // Filter out empty options
                    $options = array_values(array_filter($questionData['options'], function($opt) {
                        return !empty(trim($opt));
                    }));
                } else {
                    // Fallback to old format for backward compatibility
                    $options = array_filter([
                        $questionData['option_a'] ?? null,
                        $questionData['option_b'] ?? null,
                        $questionData['option_c'] ?? null,
                        $questionData['option_d'] ?? null,
                    ]);
                    $options = array_values(array_filter($options));
                }
                
                // Handle correct answer based on question type
                $correctAnswer = null;
                if ($questionType === 'checkboxes' && isset($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    // For checkboxes, store as comma-separated indices
                    $correctAnswer = implode(',', $questionData['correct_answers']);
                } else {
                    // For multiple choice or short answer - store as index for multiple choice
                    $correctAnswer = $questionData['correct_answer'] ?? null;
                }
                
                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'question' => $questionData['question'],
                    'question_type' => $questionType,
                    'options' => $options,
                    'option_a' => $options[0] ?? null,
                    'option_b' => $options[1] ?? null,
                    'option_c' => $options[2] ?? null,
                    'option_d' => $options[3] ?? null,
                    'correct_answer' => $correctAnswer,
                    'shuffle_options' => isset($questionData['shuffle_options']) && $questionData['shuffle_options'] == '1',
                    'marks' => $questionData['marks'],
                    'order' => $order++,
                ]);
            }
        }

        // Handle materials for homework/test
        if (in_array($validated['type'], ['homework', 'test']) && $request->has('materials')) {
            $materials = $request->input('materials');
            $materialFiles = $request->file('materials');
            
            if ($materialFiles) {
                foreach ($materialFiles as $index => $materialData) {
                    if (isset($materialData['file']) && $materialData['file']->isValid()) {
                        $file = $materialData['file'];
                        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('assessments/' . $assessment->id, $fileName, 'public');

                        AssessmentMaterial::create([
                            'assessment_id' => $assessment->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'description' => isset($materials[$index]['description']) ? $materials[$index]['description'] : null,
                        ]);
                    }
                }
            }
        }

        flash()->addSuccess('Assessment created successfully.');
        return redirect()->route('assessments.index', ['class_id' => $validated['class_id'], 'subject_id' => $validated['subject_id']]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $assessment = Assessments::with(['class', 'subject', 'teacher', 'questions', 'materials'])->findOrFail($id);
        $submission = null; // Initialize for teachers
        $timeCheck = ['within' => true, 'reason' => null, 'message' => null]; // Initialize for teachers
        $allSubmissions = collect([]);
        $highestScore = null;
        $canAttemptAgain = false;

        // If user is a teacher, ensure they own this assessment
        if (auth()->user()->hasRole('Teacher')) {
            if ($assessment->teacher_id !== auth()->id()) {
                abort(403, 'Unauthorized access.');
            }
        } elseif (auth()->user()->hasRole('Student')) {
            // Students can only view published assessments for their class
            $student = auth()->user();
            $studentClass = $student->activeClass()->first();
            
            if (!$studentClass || $assessment->class_id !== $studentClass->id || !$assessment->is_published) {
                abort(403, 'You can only view published assessments for your class.');
            }
            
            // Get all student's submissions (for multiple attempts)
            $allSubmissions = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->where('type', $assessment->type)
                ->orderBy('attempt_number')
                ->get();
            
            // Get current/latest submission (in-progress or latest submitted)
            $submission = $allSubmissions->whereNull('submitted_at')->first() 
                       ?? $allSubmissions->whereNotNull('submitted_at')->sortByDesc('attempt_number')->first();
            
            // Calculate highest score for quizzes
            $highestScore = null;
            if ($assessment->type === 'quiz' && $allSubmissions->isNotEmpty()) {
                $highestScore = $allSubmissions->whereNotNull('score')->max('score');
            }
            
            // Check time window availability
            $timeCheck = $this->isWithinTimeWindow($assessment);
            
            // Check if can attempt again
            $canAttemptAgain = false;
            if ($assessment->type === 'quiz') {
                $submittedCount = $allSubmissions->whereNotNull('submitted_at')->count();
                $canAttemptAgain = ($assessment->max_attempts === null || $submittedCount < $assessment->max_attempts);
            }
        } else {
            abort(403, 'Access denied.');
        }

        return view('pages.ManageAssessment.show', compact('assessment', 'submission', 'timeCheck', 'allSubmissions', 'highestScore', 'canAttemptAgain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Only teachers can edit assessments
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can edit assessments.');
        }

        $assessment = Assessments::findOrFail($id);
        
        // Ensure teacher owns this assessment
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $teacher = auth()->user();
        $assignments = $teacher->teachingAssignments()->with(['class', 'subject'])->get();
        
        $classes = $assignments->pluck('class')->unique('id')->filter()->values();
        $subjects = $assignments->pluck('subject')->unique('id')->filter()->values();

        // Load questions and materials for editing
        $assessment->load(['questions', 'materials']);

        return view('pages.ManageAssessment.edit', compact('assessment', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Only teachers can update assessments
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can update assessments.');
        }

        $assessment = Assessments::findOrFail($id);
        
        // Ensure teacher owns this assessment
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,test,homework',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_marks' => 'required|numeric|min:0|max:1000',
            'time_limit' => 'nullable|integer|min:1|required_if:type,quiz',
            'max_attempts' => 'nullable|integer|min:1',
            'show_marks' => 'boolean',
            'is_published' => 'boolean',
        ], [
            'title.required' => 'The assessment title field is required. Please fill in the title.',
            'type.required' => 'The assessment type field is required. Please select a type.',
            'type.in' => 'Please select a valid assessment type (Quiz, Test, or Homework).',
            'class_id.required' => 'The class field is required. Please select a class.',
            'class_id.exists' => 'The selected class is invalid. Please select a valid class.',
            'subject_id.required' => 'The subject field is required. Please select a subject.',
            'subject_id.exists' => 'The selected subject is invalid. Please select a valid subject.',
            'start_date.required' => 'The start date field is required. Please select a start date and time.',
            'start_date.date' => 'The start date must be a valid date and time.',
            'end_date.required' => 'The end date field is required. Please select an end date and time.',
            'end_date.date' => 'The end date must be a valid date and time.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'total_marks.required' => 'The total marks field is required. Please enter the total marks.',
            'total_marks.numeric' => 'The total marks must be a number.',
            'total_marks.min' => 'The total marks must be at least 0.',
            'total_marks.max' => 'The total marks cannot exceed 1000.',
            'time_limit.required_if' => 'The time limit field is required for quiz assessments. Please enter the time limit in minutes.',
            'time_limit.integer' => 'The time limit must be a whole number.',
            'time_limit.min' => 'The time limit must be at least 1 minute.',
        ]);

        // Verify teacher is assigned to this class and subject
        $teacher = auth()->user();
        $assignment = $teacher->teachingAssignments()
            ->where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if (!$assignment) {
            return back()->withErrors(['class_id' => 'You are not assigned to teach this subject in this class.'])->withInput();
        }

        $validated['is_published'] = $request->has('is_published') ? 1 : 0;
        
        // Convert datetime-local format (Y-m-d\TH:i) to datetime format (Y-m-d H:i:s)
        $validated['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $validated['start_date'])));
        $validated['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $validated['end_date'])));

        $assessment->update($validated);

        flash()->addSuccess('Assessment updated successfully.');
        return redirect()->route('assessments.show', $assessment->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Only teachers can delete assessments
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can delete assessments.');
        }

        $assessment = Assessments::findOrFail($id);
        
        // Ensure teacher owns this assessment
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $classId = $assessment->class_id;
        $subjectId = $assessment->subject_id;

        $assessment->delete();

        flash()->addSuccess('Assessment deleted successfully.');
        return redirect()->route('assessments.index', ['class_id' => $classId, 'subject_id' => $subjectId]);
    }

    /**
     * Store a question for quiz assessment
     */
    public function storeQuestion(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can add questions.');
        }

        $assessment = Assessments::findOrFail($id);
        
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($assessment->type !== 'quiz') {
            return back()->withErrors(['type' => 'Questions can only be added to quiz assessments.']);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d',
            'marks' => 'required|numeric|min:0',
        ]);

        $validated['assessment_id'] = $id;
        $validated['order'] = $assessment->questions()->count() + 1;

        AssessmentQuestion::create($validated);

        flash()->addSuccess('Question added successfully.');
        return redirect()->route('assessments.show', $id);
    }

    /**
     * Delete a question
     */
    public function deleteQuestion($id, $questionId)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can delete questions.');
        }

        $assessment = Assessments::findOrFail($id);
        
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $question = AssessmentQuestion::where('assessment_id', $id)->findOrFail($questionId);
        $question->delete();

        flash()->addSuccess('Question deleted successfully.');
        return redirect()->route('assessments.show', $id);
    }

    /**
     * Check if assessment is within the allowed time window.
     */
    private function isWithinTimeWindow($assessment)
    {
        $now = now();
        
        if ($assessment->start_date && $now->lt($assessment->start_date)) {
            return ['within' => false, 'reason' => 'not_started', 'message' => 'This assessment has not started yet. It will be available from ' . $assessment->start_date->format('F d, Y h:i A') . '.'];
        }
        
        if ($assessment->end_date && $now->gt($assessment->end_date)) {
            return ['within' => false, 'reason' => 'ended', 'message' => 'This assessment has ended. The submission deadline was ' . $assessment->end_date->format('F d, Y h:i A') . '.'];
        }
        
        return ['within' => true, 'reason' => null, 'message' => null];
    }

    /**
     * Student starts the quiz (records start time).
     */
    public function startQuiz($id)
    {
        if (!auth()->user()->hasRole('Student')) {
            abort(403, 'Only students can start quizzes.');
        }

        $assessment = Assessments::findOrFail($id);

        if ($assessment->type !== 'quiz' || !$assessment->is_published) {
            abort(403, 'This quiz is not available.');
        }

        $student = auth()->user();
        $studentClass = $student->activeClass()->first();
        if (!$studentClass || $studentClass->id !== $assessment->class_id) {
            abort(403, 'You are not enrolled in the class for this assessment.');
        }

        // Check if within time window
        $timeCheck = $this->isWithinTimeWindow($assessment);
        if (!$timeCheck['within']) {
            flash()->addError($timeCheck['message']);
            return redirect()->route('assessments.show', $assessment->id);
        }

        // Check attempt limits
        $submittedAttempts = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->where('type', 'quiz')
            ->whereNotNull('submitted_at')
            ->count();
        
        // Check if max attempts reached
        if ($assessment->max_attempts !== null && $submittedAttempts >= $assessment->max_attempts) {
            flash()->addWarning('You have reached the maximum number of attempts (' . $assessment->max_attempts . ') for this quiz.');
            return redirect()->route('assessments.show', $assessment->id);
        }

        // Check if there's an in-progress attempt
        $inProgressSubmission = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->where('type', 'quiz')
            ->whereNotNull('started_at')
            ->whereNull('submitted_at')
            ->first();

        if ($inProgressSubmission) {
            // Continue with existing in-progress attempt
            return redirect()->route('assessments.show', $assessment->id);
        }

        // Calculate next attempt number
        $nextAttemptNumber = $submittedAttempts + 1;

        // Create new submission for this attempt
        AssessmentSubmission::create([
            'assessment_id' => $assessment->id,
            'student_id' => $student->id,
            'type' => 'quiz',
            'attempt_number' => $nextAttemptNumber,
            'started_at' => now(),
        ]);

        return redirect()->route('assessments.show', $assessment->id);
    }

    /**
     * Student submits quiz answers.
     */
    public function submitQuiz(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Student')) {
            abort(403, 'Only students can submit quiz answers.');
        }

        $assessment = Assessments::with('questions')->findOrFail($id);

        // Ensure assessment is a published quiz and belongs to student's active class
        if ($assessment->type !== 'quiz' || !$assessment->is_published) {
            abort(403, 'This quiz is not available.');
        }

        $student = auth()->user();
        $studentClass = $student->activeClass()->first();
        if (!$studentClass || $studentClass->id !== $assessment->class_id) {
            abort(403, 'You are not enrolled in the class for this assessment.');
        }

        // Check if within time window
        $timeCheck = $this->isWithinTimeWindow($assessment);
        if (!$timeCheck['within']) {
            flash()->addError($timeCheck['message']);
            return redirect()->route('assessments.show', $assessment->id);
        }

        // Get the submission to submit
        $submissionId = $request->input('submission_id');
        if ($submissionId) {
            $submission = AssessmentSubmission::where('id', $submissionId)
                ->where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->where('type', 'quiz')
                ->whereNotNull('started_at')
                ->whereNull('submitted_at')
                ->first();
        } else {
            // Fallback: get latest in-progress submission
            $submission = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->where('type', 'quiz')
                ->whereNotNull('started_at')
                ->whereNull('submitted_at')
                ->latest()
                ->first();
        }

        if (!$submission) {
            abort(403, 'Please start the quiz first.');
        }

        // Check time limit if set
        if ($assessment->time_limit) {
            $startedAt = $submission->started_at;
            $timeLimitSeconds = $assessment->time_limit * 60;
            $elapsedSeconds = now()->diffInSeconds($startedAt);
            
            if ($elapsedSeconds > $timeLimitSeconds) {
                flash()->addWarning('Time limit exceeded. Your quiz has been automatically submitted.');
                // Auto-submit with current answers if any
                if ($request->has('answers')) {
                    $data = $request->validate(['answers' => 'array']);
                    $answers = $data['answers'] ?? [];
                } else {
                    $answers = $submission->answers ?? [];
                }
                
                $score = $this->calculateQuizScore($assessment, $answers);
                $submission->update([
                    'answers' => $answers,
                    'score' => $score,
                    'submitted_at' => now(),
                ]);
                return redirect()->route('assessments.show', $assessment->id);
            }
        }

        $data = $request->validate([
            'answers' => 'required|array',
        ]);

        $answers = [];
        foreach ($assessment->questions as $question) {
            $qid = $question->id;
            if (!array_key_exists($qid, $data['answers'])) {
                continue;
            }
            $answer = $data['answers'][$qid];

            // Normalize answer based on question_type
            if ($question->question_type === 'checkboxes') {
                $answerValue = is_array($answer) ? implode(',', $answer) : (string) $answer;
            } else {
                $answerValue = is_array($answer) ? reset($answer) : (string) $answer;
            }

            $answers[$qid] = $answerValue;
        }

        $score = $this->calculateQuizScore($assessment, $answers);

        $submission->update([
            'answers' => $answers,
            'score' => $score,
            'submitted_at' => now(),
        ]);

        flash()->addSuccess('Quiz submitted successfully.');
        return redirect()->route('assessments.show', $assessment->id);
    }

    /**
     * Calculate quiz score from answers.
     */
    private function calculateQuizScore($assessment, $answers)
    {
        $score = 0;
        foreach ($assessment->questions as $question) {
            $qid = $question->id;
            if (!isset($answers[$qid])) {
                continue;
            }
            $answerValue = $answers[$qid];

            // Simple auto-grading for multiple_choice and checkboxes
            if (in_array($question->question_type, ['multiple_choice', 'checkboxes'])) {
                if ($question->correct_answer !== null && $question->correct_answer !== '') {
                    if ((string) $question->correct_answer === (string) $answerValue) {
                        $score += (float) $question->marks;
                    }
                }
            }
        }
        return $score;
    }

    /**
     * Student submits homework/test answer file.
     */
    public function submitHomework(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Student')) {
            abort(403, 'Only students can submit homework or test answers.');
        }

        $assessment = Assessments::findOrFail($id);

        if (!in_array($assessment->type, ['homework', 'test']) || !$assessment->is_published) {
            abort(403, 'This assessment is not available.');
        }

        $student = auth()->user();
        $studentClass = $student->activeClass()->first();
        if (!$studentClass || $studentClass->id !== $assessment->class_id) {
            abort(403, 'You are not enrolled in the class for this assessment.');
        }

        $data = $request->validate([
            'answer_file' => 'required|file|max:10240',
        ]);

        $file = $data['answer_file'];
        $path = $file->store('assessment_submissions', 'public');

        AssessmentSubmission::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'type' => $assessment->type,
            ],
            [
                'answer_file_path' => $path,
                'answer_original_name' => $file->getClientOriginalName(),
                'submitted_at' => now(),
            ]
        );

        flash()->addSuccess('Answer uploaded successfully.');
        return redirect()->route('assessments.show', $assessment->id);
    }

    /**
     * Remove student submission (for test/homework only)
     */
    public function removeSubmission($id)
    {
        if (!auth()->user()->hasRole('Student')) {
            abort(403, 'Only students can remove their submissions.');
        }

        $assessment = Assessments::findOrFail($id);

        if (!in_array($assessment->type, ['homework', 'test'])) {
            abort(403, 'Can only remove submissions for test or homework.');
        }

        $student = auth()->user();
        $studentClass = $student->activeClass()->first();
        if (!$studentClass || $studentClass->id !== $assessment->class_id) {
            abort(403, 'You are not enrolled in the class for this assessment.');
        }

        $submission = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$submission || !$submission->submitted_at) {
            return redirect()->route('assessments.show', $assessment->id)
                ->with('error', 'No submission found to remove.');
        }

        // Check if still within submission window
        $timeCheck = $this->isWithinTimeWindow($assessment);
        if ($timeCheck['reason'] === 'ended' || !$timeCheck['within']) {
            return redirect()->route('assessments.show', $assessment->id)
                ->with('error', 'Cannot remove submission after the deadline has passed.');
        }

        // Delete the uploaded file if it exists
        if ($submission->answer_file_path && Storage::disk('public')->exists($submission->answer_file_path)) {
            Storage::disk('public')->delete($submission->answer_file_path);
        }

        // Remove submission data (set submitted_at to null, clear file paths, but keep the record)
        $submission->update([
            'submitted_at' => null,
            'answer_file_path' => null,
            'answer_original_name' => null,
            'score' => null, // Reset score when resubmitting
        ]);

        flash()->addSuccess('Submission removed successfully. You can now resubmit your answer.');
        return redirect()->route('assessments.show', $assessment->id);
    }

    /**
     * Upload material for test/homework assessment
     */
    public function uploadMaterial(Request $request, $id)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can upload materials.');
        }

        $assessment = Assessments::findOrFail($id);
        
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($assessment->type, ['test', 'homework'])) {
            return back()->withErrors(['type' => 'Materials can only be uploaded for test or homework assessments.']);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('assessments/' . $id, $fileName, 'public');

        AssessmentMaterial::create([
            'assessment_id' => $id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
        ]);

        flash()->addSuccess('Material uploaded successfully.');
        return redirect()->route('assessments.show', $id);
    }

    /**
     * Delete a material
     */
    public function deleteMaterial($id, $materialId)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can delete materials.');
        }

        $assessment = Assessments::findOrFail($id);
        
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $material = AssessmentMaterial::where('assessment_id', $id)->findOrFail($materialId);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();

        flash()->addSuccess('Material deleted successfully.');
        return redirect()->route('assessments.show', $id);
    }

    /**
     * View student submissions for an assessment (Teacher only)
     */
    public function viewSubmissions($id)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can view student submissions.');
        }

        $assessment = Assessments::with(['class', 'subject'])->findOrFail($id);
        
        // Ensure teacher owns this assessment
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all active students in the class
        $students = $assessment->class->activeStudents()->orderBy('name')->get();
        
        // Get all submissions for this assessment
        $submissions = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('student_id');
        
        return view('pages.ManageAssessment.submissions', compact('assessment', 'students', 'submissions'));
    }

    /**
     * Update mark for a submission (Teacher only)
     */
    public function updateSubmissionMark(Request $request, $id, $submissionId)
    {
        if (!auth()->user()->hasRole('Teacher')) {
            abort(403, 'Only teachers can update marks.');
        }

        $assessment = Assessments::findOrFail($id);
        
        // Ensure teacher owns this assessment
        if ($assessment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $submission = AssessmentSubmission::where('assessment_id', $id)
            ->findOrFail($submissionId);

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assessment->total_marks,
        ]);

        $submission->update([
            'score' => $validated['score'],
        ]);

        flash()->addSuccess('Mark updated successfully.');
        return redirect()->route('assessments.submissions', $assessment->id);
    }
}
