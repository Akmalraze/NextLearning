<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class MaterialController extends Controller
{
    /**
     * Show the form to create a new material for a module.
     */
    public function create($moduleId)
    {
        $module = Modules::with('subject')->findOrFail($moduleId);

        $subjectId = $module->subject_id;
        $currentModuleId = $module->id;

        $subjects = Subjects::with('modules')
            ->where('is_active', true)
            ->get();

        return view(
            'pages.Managematerial.create',
            compact('module', 'subjects', 'subjectId', 'currentModuleId')
        );
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx,pptx,txt,mp4|max:10240',
            'materials_notes' => 'nullable|string|max:255',
            'module_id' => 'required|exists:modules,id',
        ]);

        $module = Modules::with('subject')->findOrFail($validated['module_id']);

        // Upload file
        $filePath = $request->file('file')->store('materials', 'public');

        Material::create([
            'materials_name' => $validated['materials_name'],
            'file_path' => $filePath,
            'materials_uploadDate' => now(),
            'materials_notes' => $validated['materials_notes'],
            'module_id' => $module->id,
            'subject_id' => $module->subject_id,
        ]);

        flash()->addSuccess('Material uploaded successfully!');
        
        return redirect()
            ->route('modules-view', $module->id)
            ->with('success', 'Material uploaded successfully!');
    }

    /**
     * Show the form to edit a material.
     */
    public function edit($materialId)
    {
        $material = Material::with('module.subject')->findOrFail($materialId);

        $subjectId = $material->module->subject_id;
        $currentModuleId = $material->module_id;

        $subjects = Subjects::with('modules')
            ->where('is_active', true)
            ->get();

        return view(
            'pages.Managematerial.edit',
            compact('material', 'subjects', 'subjectId', 'currentModuleId')
        );
    }

    /**
     * Update an existing material.
     */
    public function update(Request $request, $materialId)
    {
        abort_if(Gate::denies('edit materials'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $material = Material::with('module')->findOrFail($materialId);

        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'materials_notes' => 'nullable|string|max:255',
        ]);

        $material->update($validated);

        return redirect()
            ->route('modules-view', $material->module_id)
            ->with('success', 'Material updated successfully.');
    }

    /**
     * Delete a material.
     */
    public function destroy($materialId)
    {
        $material = Material::findOrFail($materialId);
        $moduleId = $material->module_id;

        $material->delete();

        return redirect()
            ->route('modules-view', $moduleId)
            ->with('success', 'Material deleted successfully.');
    }
}
