<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ModuleController extends Controller
{
    /**
     * Display a listing of all modules.
     */public function index(Request $request)
{
    // Get subject_id from query
    $subjectId = $request->get('subject_id');

    // Load active subjects with modules
    $subjects = Subjects::with('modules')
        ->where('is_active', true)
        ->get();

    // 🚨 If subject_id sent, use it; else pick the first active subject
    if (!$subjectId && $subjects->count() > 0) {
        $subjectId = $subjects->first()->id;
    }

    // Filter modules by subject_id only
    $modules = Modules::with('subject')
        ->where('subject_id', $subjectId)
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $currentModuleId = null;

    return view(
        'pages.ManageModule.index',
        compact(
            'modules',
            'subjects',
            'subjectId',
            'currentModuleId'
        )
    );
}




    /**
     * Show the form to create a new module.
     */
    public function create($subjectId)
{
    $subject = Subjects::findOrFail($subjectId);
    if (!$subject->is_active) {
        return redirect()->route('modules.index')->with('error', 'This subject is not active.');
    }
    return view('pages.ManageModule.create', compact('subject'));
}

    /**
     * Store a newly created module in the database.
     */
  public function store(Request $request)
{
    // Validate the input data
    $validated = $request->validate([
        'modules_name' => 'required|string|max:255',
        'modules_description' => 'nullable|string|max:255',
        'subject_id' => 'required|exists:subjects,id',  // Ensure the subject exists in the database
    ]);

    // Create a new module under the selected subject
    Modules::create($validated);

    flash()->addSuccess('Module created successfully.');

    // Redirect back with a success message
    return redirect()->route('modules-index', ['subject_id' => $validated['subject_id']])
                     ->with('success', 'Module created successfully.');
}

    // In the MaterialController
public function show($moduleId)
{
    $module = Modules::with(['materials', 'subject'])->findOrFail($moduleId);

    $subjectId = $module->subject_id;
    $currentModuleId = $module->id;

    $subjects = Subjects::with('modules')
        ->where('is_active', true)
        ->get();

    return view(
        'pages.ManageModule.view',
        compact('module', 'subjects', 'subjectId', 'currentModuleId')
    );
}

public function list(Subjects $subject)
{
    $subjectId = $subject->id;

    // Load modules & materials
    $subject->load(['modules.materials']);

    $subjects = Subjects::with('modules')
        ->where('is_active', true)
        ->get();

    $currentModuleId = null;

    return view(
        'pages.ManageModule.list',
        compact('subject', 'subjects', 'subjectId', 'currentModuleId')
    );
}


    /**
     * Show the form to edit an existing module.
     */
    public function edit($id)
{
    $module = Modules::with('subject')->findOrFail($id);

    $subjectId = $module->subject_id;
    $currentModuleId = $module->id;

    $subjects = Subjects::with('modules')
        ->where('is_active', true)
        ->get();

       

    return view(
        'pages.ManageModule.edit',
        compact('module', 'subjects', 'subjectId', 'currentModuleId')
    );
}


    /**
     * Update an existing module in the database.
     */
   public function update(Request $request, $id)
{
    

    $module = Modules::findOrFail($id);

    // Validate input data
    $validated = $request->validate([
        'modules_name' => 'required|string|max:255',
        'modules_description' => 'nullable|string|max:255',
        'subject_id' => 'required|exists:subjects,id', // Ensure valid subject_id
    ]);

    // Update the module
    $module->update($validated);

    flash()->addSuccess('Module updated successfully.');

    // Redirect back with success message
    return redirect()->route('modules-index')->with('success', 'Module updated successfully.');
}


    /**
     * Delete a module from the database.
     */
    public function destroy($id)
    {
        

        $module = Modules::findOrFail($id);
        $module->delete();

        flash()->addSuccess('Module deleted successfully.');
        return redirect()->route('modules-index')->with('success', 'Module deleted successfully.');
    }
}
