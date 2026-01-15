<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('view users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $search = $request->get('search');
        $isActive = $request->get('is_active');

        // Show all subjects, enriched with educator & learner counts
        $query = Subjects::withCount('learners')->with('educator');

        // If user is an Educator, only show their own subjects
        if (auth()->user()->hasRole('Educator')) {
            $query->where(function ($q) {
                $q->where('educator_id', auth()->id())
                  ->orWhereNull('educator_id'); // Show subjects without educator_id (legacy)
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        return view('teacher.subjects.index', compact('subjects', 'search', 'isActive'));
    }

    public function create()
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('teacher.subjects.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'is_published' => 'nullable',
        ]);

        // Also set legacy fields
        $validated['subjects_name'] = $validated['name'];
        $validated['subjects_code'] = $validated['code'];
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;

        // Assign current educator as owner of the course
        $validated['educator_id'] = auth()->id();

        Subjects::create($validated);

        flash()->addSuccess('Course created successfully.');
        return redirect()->route('teacher.subjects.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('view users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::with(['modules.materials', 'sectionTitles.materials'])->findOrFail($id);

        // Load assessments for this subject
        $assessments = \App\Models\Assessments::where('subject_id', $subject->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('teacher.subjects.show', compact('subject', 'assessments'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);

        return view('teacher.subjects.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);

        // Ensure educator can only update their own courses
        if (auth()->user()->hasRole('Educator') && $subject->educator_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'is_published' => 'nullable',
        ]);

        // Update legacy fields
        $validated['subjects_name'] = $validated['name'];
        $validated['subjects_code'] = $validated['code'];
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_published'] = $request->has('is_published') ? 1 : 0;

        $subject->update($validated);

        flash()->addSuccess('Course updated successfully.');
        return redirect()->route('teacher.subjects.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);

        // Check for dependencies
        $moduleCount = $subject->modules()->count();
        $classAssignmentCount = $subject->classAssignments()->count();

        if ($moduleCount > 0 || $classAssignmentCount > 0) {
            flash()->addError("Cannot delete subject with {$moduleCount} modules and {$classAssignmentCount} class assignments. Please remove them first.");
            return back();
        }

        $subject->delete();

        flash()->addSuccess('Subject deleted successfully.');
        return redirect()->route('teacher.subjects.index');
    }

    public function togglePublish($id)
    {
        // Allow educators to publish their own courses
        if (!auth()->user()->hasRole('Educator')) {
            abort(403, 'Only educators can publish courses.');
        }

        $subject = Subjects::findOrFail($id);
        $currentUserId = auth()->id();

        // If subject doesn't have educator_id set, assign it to current user
        if ($subject->educator_id === null) {
            $subject->educator_id = $currentUserId;
            $subject->save();
        }

        // Ensure educator can only publish their own courses
        if ($subject->educator_id !== $currentUserId) {
            abort(403, 'You can only publish your own courses.');
        }

        // If trying to publish, ensure course is active first
        if (!$subject->is_published && !$subject->is_active) {
            flash()->addWarning('Please activate the course first before publishing it.');
            return redirect()->route('teacher.subjects.index');
        }

        // Toggle publish status
        $subject->is_published = !$subject->is_published;
        $subject->save();

        $message = $subject->is_published 
            ? 'Course published successfully. It is now visible to learners in the browse courses page.' 
            : 'Course unpublished successfully. It is no longer visible to learners.';

        flash()->addSuccess($message);
        return redirect()->route('teacher.subjects.index');
    }
}
