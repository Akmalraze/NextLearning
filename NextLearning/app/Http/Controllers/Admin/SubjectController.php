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

        $query = Subjects::query();

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

        return view('admin.subjects.index', compact('subjects', 'search', 'isActive'));
    }

    public function create()
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        // Also set legacy fields
        $validated['subjects_name'] = $validated['name'];
        $validated['subjects_code'] = $validated['code'];
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Subjects::create($validated);

        flash()->addSuccess('Subject created successfully.');
        return redirect()->route('admin.subjects.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('view users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);
        $classes = Classes::all();
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teacher');
        })->get();

        return view('admin.subjects.show', compact('subject', 'classes', 'teachers'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);

        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit users'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subject = Subjects::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        // Update legacy fields
        $validated['subjects_name'] = $validated['name'];
        $validated['subjects_code'] = $validated['code'];
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $subject->update($validated);

        flash()->addSuccess('Subject updated successfully.');
        return redirect()->route('admin.subjects.index');
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
        return redirect()->route('admin.subjects.index');
    }
}
