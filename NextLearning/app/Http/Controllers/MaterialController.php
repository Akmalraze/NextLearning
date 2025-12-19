<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class MaterialController extends Controller
{
    // Display the list of materials for a specific module
    /**
     * Show the form to create a new material for a module.
     */
    public function create($moduleId)
    {
        $module = Modules::findOrFail($moduleId);
        return view('pages.ManageMaterial.create', compact('module'));
    }

    /**
     * Store a newly created material in the database.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'materials_name' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf,docx,pptx,txt,mp4|max:10240', // Validate file type and size
        'materials_notes' => 'nullable|string|max:255',
        'module_id' => 'required|exists:modules,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    // Handle file upload
    $filePath = $request->file('file')->store('materials', 'public'); // Store the file in the 'materials' folder inside 'public'

    // Store material data along with the file path
    Material::create([
        'materials_name' => $validated['materials_name'],
        'file_path' => $filePath, // Save the file path
        'materials_uploadDate' => now(),
        'materials_notes' => $validated['materials_notes'],
        'module_id' => $validated['module_id'],
        'subject_id' => $validated['subject_id'],
    ]);

    return redirect()->route('materials-index')->with('success', 'Material uploaded successfully!');
}


    /**
     * Show the form to edit an existing material.
     */
    public function edit($materialId)
    {
        $material = Material::findOrFail($materialId);
        return view('pages.ManageMaterial.edit', compact('material'));
    }

    /**
     * Update an existing material in the database.
     */
    public function update(Request $request, $materialId)
    {
        abort_if(Gate::denies('edit materials'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $material = Material::findOrFail($materialId);

        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'materials_format' => 'required|string|max:50',
            'materials_uploadDate' => 'nullable|date',
            'materials_notes' => 'nullable|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $material->update($validated);

        return redirect()->route('modules-view', $validated['module_id'])->with('success', 'Material updated successfully.');
    }

    /**
     * Delete a material from the database.
     */
    public function destroy($materialId)
    {
        $material = Material::findOrFail($materialId);
        $material->delete();

        return redirect()->route('modules-view', $material->module_id)->with('success', 'Material deleted successfully.');
    }
}
