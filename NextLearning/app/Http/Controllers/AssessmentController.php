<?php

namespace App\Http\Controllers;

use App\Models\Assessments;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentMaterial;
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

                $assessments = $query->latest()->paginate(15)->withQueryString();
                
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

            $assessments = $query->latest()->paginate(15)->withQueryString();
            
            // Initialize variables for student view
            $classSubjectCombos = collect([]);
            $classId = null;
            $subjectId = null;
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_date' => 'nullable|date|after_or_equal:end_date',
            'total_marks' => 'required|numeric|min:0|max:1000',
            'is_published' => 'boolean',
            // Questions for quiz
            'questions' => 'required_if:type,quiz|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.option_a' => 'required_with:questions|string',
            'questions.*.option_b' => 'required_with:questions|string',
            'questions.*.option_c' => 'required_with:questions|string',
            'questions.*.option_d' => 'required_with:questions|string',
            'questions.*.correct_answer' => 'required_with:questions|in:a,b,c,d',
            'questions.*.marks' => 'required_with:questions|numeric|min:0',
            // Materials for homework/test
            'materials' => 'required_if:type,homework,test|array|min:1',
            'materials.*.file' => 'required_with:materials|file|max:10240',
            'materials.*.description' => 'nullable|string',
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

        // Create the assessment
        $assessment = Assessments::create($validated);

        // Handle questions for quiz
        if ($validated['type'] === 'quiz' && $request->has('questions')) {
            $order = 1;
            foreach ($request->input('questions') as $questionData) {
                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'question' => $questionData['question'],
                    'option_a' => $questionData['option_a'],
                    'option_b' => $questionData['option_b'],
                    'option_c' => $questionData['option_c'],
                    'option_d' => $questionData['option_d'],
                    'correct_answer' => $questionData['correct_answer'],
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
        } else {
            abort(403, 'Access denied.');
        }

        return view('pages.ManageAssessment.show', compact('assessment'));
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_date' => 'nullable|date|after_or_equal:end_date',
            'total_marks' => 'required|numeric|min:0|max:1000',
            'is_published' => 'boolean',
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
}
