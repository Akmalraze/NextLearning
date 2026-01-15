<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $search = $request->get('search');
        $formLevel = $request->get('form_level');
        $academicSession = $request->get('academic_session');

        $query = Classes::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('form_level', $search);
            });
        }

        if ($formLevel) {
            $query->where('form_level', $formLevel);
        }

        if ($academicSession) {
            $query->where('academic_session', $academicSession);
        }

        $classes = $query->latest()->paginate(15)->withQueryString();
        $formLevels = Classes::select('form_level')->distinct()->pluck('form_level');
        $academicSessions = Classes::select('academic_session')->distinct()->pluck('academic_session');

        return view('teacher.classes.index', compact('classes', 'search', 'formLevel', 'academicSession', 'formLevels', 'academicSessions'));
    }

    public function create()
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get teachers not already assigned as homeroom teachers
        $assignedTeacherIds = Classes::whereNotNull('homeroom_teacher_id')
            ->pluck('homeroom_teacher_id')
            ->toArray();

        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Educator');
        })->whereNotIn('id', $assignedTeacherIds)->get();

        return view('teacher.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'form_level' => 'required|integer|in:' . implode(',', Classes::FORM_LEVELS),
            'name' => 'required|string|max:100',
            'academic_session' => 'required|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        // Check for unique form_level + name combination
        $exists = Classes::where('form_level', $validated['form_level'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'This class already exists.']);
        }

        Classes::create($validated);

        flash()->addSuccess('Class created successfully.');
        return redirect()->route('teacher.classes.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('view users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($id);
        $students = $class->activeStudents()->with('roles')->paginate(15);

        // Load subject-teacher assignments
        $assignments = $class->subjectAssignments()->with(['subject', 'teacher'])->get();

        // Get all active subjects for assignment
        $subjects = \App\Models\Subjects::where('is_active', true)->get();

        // Get all educators for assignment
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Educator');
        })->get();

        return view('teacher.classes.show', compact('class', 'students', 'assignments', 'subjects', 'teachers'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($id);

        // Get teachers not already assigned as homeroom teachers (except current class's teacher)
        $assignedTeacherIds = Classes::whereNotNull('homeroom_teacher_id')
            ->where('id', '!=', $id)
            ->pluck('homeroom_teacher_id')
            ->toArray();

        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Educator');
        })->whereNotIn('id', $assignedTeacherIds)->get();

        return view('teacher.classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($id);

        $validated = $request->validate([
            'form_level' => 'required|integer|in:' . implode(',', Classes::FORM_LEVELS),
            'name' => 'required|string|max:100',
            'academic_session' => 'required|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        // Check for unique form_level + name combination (excluding current class)
        $exists = Classes::where('form_level', $validated['form_level'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['name' => 'This class already exists.']);
        }

        $class->update($validated);

        flash()->addSuccess('Class updated successfully.');
        return redirect()->route('teacher.classes.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($id);

        // Check if class has students
        $studentCount = $class->activeStudents()->count();
        if ($studentCount > 0) {
            flash()->addError("Cannot delete class with {$studentCount} enrolled students. Please reassign them first.");
            return back();
        }

        $class->delete();

        flash()->addSuccess('Class deleted successfully.');
        return redirect()->route('teacher.classes.index');
    }

    public function enrollments($id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($id);
        $enrolledStudents = $class->activeStudents()->with('roles')->get();

        // Get learners not enrolled in ANY class (with active status)
        $availableStudents = User::whereHas('roles', function ($query) {
            $query->where('name', 'Learner');
        })->whereDoesntHave('classes', function ($query) {
            $query->where('class_students.status', 'active');
        })->get();

        return view('teacher.classes.enrollments', compact('class', 'enrolledStudents', 'availableStudents'));
    }

    public function enroll(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'student_id' => 'required|exists:users,id'
        ]);

        $class = Classes::findOrFail($id);
        $student = User::findOrFail($request->student_id);

        // Check if learner has Learner role
        if (!$student->hasRole('Learner')) {
            flash()->addError('Selected user is not a learner.');
            return back();
        }

        // Check if already enrolled
        if ($class->activeStudents()->where('student_id', $student->id)->exists()) {
            flash()->addError('Student is already enrolled in this class.');
            return back();
        }

        // Check if learner is enrolled in another active class
        $existingEnrollment = $student->activeClass()->first();
        if ($existingEnrollment) {
            flash()->addError("Student is already enrolled in {$existingEnrollment->form_level} {$existingEnrollment->name}.");
            return back();
        }

        // Enroll learner
        $class->activeStudents()->attach($student->id, [
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        flash()->addSuccess("{$student->name} has been enrolled in {$class->form_level} {$class->name}.");
        return back();
    }

    public function unenroll($classId, $studentId)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $class = Classes::findOrFail($classId);
        $student = User::findOrFail($studentId);

        // Check if learner is enrolled
        if (!$class->activeStudents()->where('student_id', $studentId)->exists()) {
            flash()->addError('Student is not enrolled in this class.');
            return back();
        }

        // Remove enrollment for learner
        $class->activeStudents()->detach($studentId);

        flash()->addSuccess("{$student->name} has been removed from {$class->form_level} {$class->name}.");
        return redirect()->route('teacher.classes.enrollments', $classId);
    }

    public function assignTeacher(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $class = Classes::findOrFail($id);
        $subject = \App\Models\Subjects::findOrFail($request->subject_id);
        $teacher = User::findOrFail($request->teacher_id);

        // Check if educator has Educator role
        if (!$teacher->hasRole('Educator')) {
            flash()->addError('Selected user is not an educator.');
            return back();
        }

        // Check if this subject is already assigned in this class
        $existingAssignment = \App\Models\SubjectClassTeacher::where('class_id', $id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if ($existingAssignment) {
            flash()->addError("{$subject->name} is already assigned to {$existingAssignment->teacher->name} in this class.");
            return back();
        }

        // Create assignment
        \App\Models\SubjectClassTeacher::create([
            'class_id' => $id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
        ]);

        flash()->addSuccess("{$teacher->name} has been assigned to teach {$subject->name} in {$class->form_level} {$class->name}.");
        return back();
    }

    public function unassignTeacher($assignmentId)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assignment = \App\Models\SubjectClassTeacher::findOrFail($assignmentId);
        $class = $assignment->class;
        $subject = $assignment->subject;
        $teacher = $assignment->teacher;

        $assignment->delete();

        flash()->addSuccess("{$teacher->name} has been unassigned from teaching {$subject->name} in {$class->form_level} {$class->name}.");
        return back();
    }
}
