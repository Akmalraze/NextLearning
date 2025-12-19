<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{
    /**
     * Display a listing of all modules.
     */
  public function index(Request $request)
{
    // Get the selected subject_id (if any)
    $subjectId = $request->get('subject_id');

    // Get all active subjects and their modules
    $subjects = Subjects::with('modules')->where('is_active', true)->get();

    // Filter modules based on subject_id if provided
    $query = Modules::with('subject')->latest();

    if ($subjectId) {
        $query->where('subject_id', $subjectId);
    }

    $modules = $query->paginate(15)->withQueryString();

    return view('pages.ManageModule.index', compact('modules', 'subjects', 'subjectId'));
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

    // Redirect back with a success message
    return redirect()->route('modules-index')->with('success', 'Module created successfully.');
}

    // In the MaterialController
public function show($moduleId)
{
    $module = Modules::with('materials')->findOrFail($moduleId);  // Load materials along with the module

    return view('pages.ManageModule.view', compact('module'));
}


    /**
     * Show the form to edit an existing module.
     */
    public function edit($id)
    {
        $modules = Modules::findOrFail($id);
        $subjects = Subjects::where('is_active', true)->get();
        return view('pages.ManageModule.edit', compact('modules', 'subjects'));
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

        return redirect()->route('modules-index')->with('success', 'Module deleted successfully.');
    }
}
