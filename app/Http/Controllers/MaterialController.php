<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Modules;
use App\Models\SectionTitle;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class MaterialController extends Controller
{
    /**
     * Show the form to create a new material for a module or section title.
     */
    public function create($id)
    {
        // Try to find as section title first, then as module
        $sectionTitle = SectionTitle::with('subject')->find($id);
        
        if ($sectionTitle) {
            $subjectId = $sectionTitle->subject_id;
            $sectionTitleId = $sectionTitle->id;
            $moduleId = null;
            
            $subjects = Subjects::with('sectionTitles')
                ->where('is_active', true)
                ->get();
            
            return view(
                'pages.Managematerial.create',
                compact('sectionTitle', 'subjects', 'subjectId', 'sectionTitleId', 'moduleId')
            );
        }
        
        // Fallback to module
        $module = Modules::with('subject')->findOrFail($id);
        $subjectId = $module->subject_id;
        $currentModuleId = $module->id;
        $sectionTitleId = null;

        $subjects = Subjects::with('modules')
            ->where('is_active', true)
            ->get();

        return view(
            'pages.Managematerial.create',
            compact('module', 'subjects', 'subjectId', 'currentModuleId', 'sectionTitleId')
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
            'module_id' => 'nullable|exists:modules,id',
            'section_title_id' => 'nullable|exists:section_titles,id',
        ]);

        // Get module_id and section_title_id safely (they may not be in validated array if null)
        $moduleId = $validated['module_id'] ?? null;
        $sectionTitleId = $validated['section_title_id'] ?? null;

        // Ensure at least one is provided
        if (!$moduleId && !$sectionTitleId) {
            return back()->withErrors(['module_id' => 'Either module or section title must be selected.'])->withInput();
        }

        $subjectId = null;
        
        // Handle section title
        if ($sectionTitleId) {
            $sectionTitle = SectionTitle::with('subject')->findOrFail($sectionTitleId);
            $subjectId = $sectionTitle->subject_id;
        }
        
        // Handle module (for backward compatibility)
        if ($moduleId) {
            $module = Modules::with('subject')->findOrFail($moduleId);
            $subjectId = $module->subject_id;
        }

        // Upload file
        $filePath = $request->file('file')->store('materials', 'public');

        Material::create([
            'materials_name' => $validated['materials_name'],
            'file_path' => $filePath,
            'materials_uploadDate' => now(),
            'materials_notes' => $validated['materials_notes'] ?? null,
            'module_id' => $moduleId,
            'section_title_id' => $sectionTitleId,
            'subject_id' => $subjectId,
        ]);

        // Redirect based on what was used
        if ($sectionTitleId) {
            return redirect()
                ->route('teacher.subjects.show', $subjectId)
                ->with('success', 'Material uploaded successfully!');
        } else {
            return redirect()
                ->route('modules-view', $moduleId)
                ->with('success', 'Material uploaded successfully!');
        }
    }

    /**
     * Show the form to edit a material.
     */
    public function edit($materialId)
    {
        $material = Material::with(['module.subject', 'sectionTitle.subject'])->findOrFail($materialId);

        // Determine subject ID and whether it's a module or section title
        if ($material->section_title_id) {
            $subjectId = $material->sectionTitle->subject_id;
            $sectionTitleId = $material->section_title_id;
            $moduleId = null;
        } else {
            $subjectId = $material->module->subject_id;
            $currentModuleId = $material->module_id;
            $sectionTitleId = null;
        }

        $subjects = Subjects::with(['modules', 'sectionTitles'])
            ->where('is_active', true)
            ->get();

        return view(
            'pages.Managematerial.edit',
            compact('material', 'subjects', 'subjectId', 'currentModuleId', 'sectionTitleId', 'moduleId')
        );
    }

    /**
     * Update an existing material.
     */
    public function update(Request $request, $materialId)
    {
        abort_if(Gate::denies('edit materials'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $material = Material::with(['module', 'sectionTitle'])->findOrFail($materialId);

        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'materials_notes' => 'nullable|string|max:255',
        ]);

        $material->update($validated);

        // Redirect based on whether it's a section title or module
        if ($material->section_title_id) {
            return redirect()
                ->route('teacher.subjects.show', $material->sectionTitle->subject_id)
                ->with('success', 'Material updated successfully.');
        } else {
            return redirect()
                ->route('modules-view', $material->module_id)
                ->with('success', 'Material updated successfully.');
        }
    }

    /**
     * Delete a material.
     */
    public function destroy($materialId)
    {
        $material = Material::with(['module', 'sectionTitle'])->findOrFail($materialId);
        
        // Store IDs before deletion
        $sectionTitleId = $material->section_title_id;
        $moduleId = $material->module_id;
        $subjectId = null;
        
        if ($sectionTitleId) {
            $subjectId = $material->sectionTitle->subject_id;
        }

        $material->delete();

        // Redirect based on whether it was a section title or module
        if ($sectionTitleId && $subjectId) {
            return redirect()
                ->route('teacher.subjects.show', $subjectId)
                ->with('success', 'Material deleted successfully.');
        } else {
            return redirect()
                ->route('modules-view', $moduleId)
                ->with('success', 'Material deleted successfully.');
        }
    }
}
