<?php

namespace App\Http\Controllers;

use App\Models\SectionTitle;
use App\Models\Subjects;
use Illuminate\Http\Request;

class SectionTitleController extends Controller
{
    /**
     * Show the form to create a new section title.
     */
    public function create($subjectId)
    {
        $subject = Subjects::findOrFail($subjectId);
        if (!$subject->is_active) {
            return redirect()->back()->with('error', 'This subject is not active.');
        }
        return view('pages.SectionTitle.create', compact('subject'));
    }

    /**
     * Store a newly created section title in the database.
     */
    public function store(Request $request)
    {
        // Validate the input data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'order' => 'nullable|integer|min:0',
        ]);

        // Create a new section title under the selected subject
        SectionTitle::create($validated);

        // Redirect back with a success message
        return redirect()->route('teacher.subjects.show', $validated['subject_id'])
            ->with('success', 'Section title created successfully.');
    }

    /**
     * Show the form to edit an existing section title.
     */
    public function edit($id)
    {
        $sectionTitle = SectionTitle::with('subject')->findOrFail($id);
        return view('pages.SectionTitle.edit', compact('sectionTitle'));
    }

    /**
     * Update an existing section title in the database.
     */
    public function update(Request $request, $id)
    {
        $sectionTitle = SectionTitle::findOrFail($id);

        // Validate input data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'order' => 'nullable|integer|min:0',
        ]);

        // Update the section title
        $sectionTitle->update($validated);

        // Redirect back with success message
        return redirect()->route('teacher.subjects.show', $validated['subject_id'])
            ->with('success', 'Section title updated successfully.');
    }

    /**
     * Delete a section title from the database.
     */
    public function destroy($id)
    {
        $sectionTitle = SectionTitle::findOrFail($id);
        $subjectId = $sectionTitle->subject_id;
        $sectionTitle->delete();

        return redirect()->route('teacher.subjects.show', $subjectId)
            ->with('success', 'Section title deleted successfully.');
    }
}
