@extends('layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    <!-- Course Header Section -->
    <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 1rem; padding: 2.5rem; margin-bottom: 2rem; color: white; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 1rem 0; color: white;">
                    {{ $subject->name ?? $subject->subjects_name }}
                </h1>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                    @if($subject->code ?? $subject->subjects_code)
                        <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Level: {{ $subject->code ?? $subject->subjects_code }}
                        </span>
                    @endif
                    @if($subject->is_published)
                        <span style="background: rgba(16, 185, 129, 0.3); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Published
                        </span>
                    @else
                        <span style="background: rgba(255,255,255,0.15); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Draft
                        </span>
                    @endif
                    @if($subject->is_active)
                        <span style="background: rgba(16, 185, 129, 0.3); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Active
                        </span>
                    @else
                        <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; backdrop-filter: blur(10px);">
                            Inactive
                        </span>
                    @endif
                </div>
                @if($subject->description)
                    <p style="margin: 0; opacity: 0.95; font-size: 1rem; line-height: 1.6;">
                        {{ $subject->description }}
                    </p>
                @endif
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('teacher.subjects.edit', $subject->id) }}" style="background: rgba(255,255,255,0.2); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem; backdrop-filter: blur(10px); transition: all 0.3s; border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="edit-2" style="width: 16px; height: 16px;"></i> Edit Course
                </a>
                <button type="button" onclick="confirmDelete({{ $subject->id }}, '{{ addslashes($subject->name ?? $subject->subjects_name) }}')" style="background: rgba(239, 68, 68, 0.3); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3); font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; backdrop-filter: blur(10px); transition: all 0.3s;">
                    <i data-feather="trash-2" style="width: 16px; height: 16px;"></i> Delete
                </button>
                <form id="delete-form-{{ $subject->id }}" action="{{ route('teacher.subjects.destroy', $subject->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <!-- Action Buttons Row -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        @if($subject->modules->isNotEmpty())
            <a href="{{ route('materials-create', $subject->modules->first()->id) }}" style="background: white; color: #6366f1; padding: 1rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.75rem; border: 2px solid #6366f1; transition: all 0.2s;">
                <i data-feather="upload" style="width: 20px; height: 20px;"></i> Upload Material
            </a>
        @else
            <button type="button" onclick="alert('Please create a module first before uploading materials.');" style="background: #e2e8f0; color: #64748b; padding: 1rem 1.5rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; font-weight: 600; display: inline-flex; align-items: center; gap: 0.75rem; cursor: not-allowed;">
                <i data-feather="upload" style="width: 20px; height: 20px;"></i> Upload Material
            </button>
        @endif
        <a href="{{ route('assessments.create', ['subject_id' => $subject->id]) }}" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: transform 0.2s;">
            <i data-feather="clipboard" style="width: 20px; height: 20px;"></i> Create Assessment
        </a>
    </div>

    <!-- Main Content Grid -->
    <div style="display: flex; flex-direction: column; gap: 2rem; margin-bottom: 2rem;">
        <!-- Modules & Materials Section -->
        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f1f5f9;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i data-feather="book-open" style="width: 24px; height: 24px; color: #6366f1;"></i>
                    Course Sections
                </h2>
                <a href="{{ route('teacher.section-titles.create', $subject->id) }}" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 0.625rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); transition: transform 0.2s; font-size: 0.875rem;">
                    <i data-feather="plus" style="width: 18px; height: 18px;"></i> Add Section Title
                </a>
            </div>
            
            @if($subject->sectionTitles->isEmpty())
                <div style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 3rem; text-align: center;">
                    <i data-feather="inbox" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 1rem;"></i>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">No sections added yet. Click "Add Section Title" to create sections like Week 1, Chapter 1, etc.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    @foreach($subject->sectionTitles as $sectionTitle)
                        <!-- Section Title Box with Materials Inside -->
                        <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #6366f1;">
                            <!-- Section Title Header -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e2e8f0;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">
                                        {{ $sectionTitle->title }}
                                    </h3>
                                    @if($sectionTitle->description)
                                        <p style="color: #64748b; font-size: 0.95rem; margin: 0; line-height: 1.5;">
                                            {{ $sectionTitle->description }}
                                        </p>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <a href="{{ route('materials-create', $sectionTitle->id) }}" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.625rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); transition: transform 0.2s; white-space: nowrap;">
                                        <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                                        Add Material
                                    </a>
                                    <a href="{{ route('teacher.section-titles.edit', $sectionTitle->id) }}" style="width: 40px; height: 40px; background: #f1f5f9; color: #6366f1; border-radius: 0.5rem; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0;" title="Edit Section Title">
                                        <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    <form action="{{ route('teacher.section-titles.destroy', $sectionTitle->id) }}" method="POST" style="display: inline; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this section title?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width: 40px; height: 40px; background: #fef2f2; color: #ef4444; border-radius: 0.5rem; border: 1px solid #fee2e2; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" title="Delete Section Title">
                                            <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Materials List - Inside the Section Title Box -->
                            @if($sectionTitle->materials && $sectionTitle->materials->count() > 0)
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    @foreach($sectionTitle->materials as $material)
                                        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; transition: all 0.3s; hover:box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                                            <div style="flex: 1; display: flex; align-items: center; gap: 1rem;">
                                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                                    <i data-feather="file-text" style="width: 24px; height: 24px; color: white;"></i>
                                                </div>
                                                <div style="flex: 1;">
                                                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">
                                                        {{ $material->materials_name }}
                                                    </h4>
                                                    @if($material->materials_notes)
                                                        <p style="color: #64748b; font-size: 0.9rem; margin: 0 0 0.25rem 0; line-height: 1.5;">
                                                            {{ $material->materials_notes }}
                                                        </p>
                                                    @endif
                                                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                                                        <span style="color: #94a3b8; font-size: 0.875rem;">
                                                            <i data-feather="calendar" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                                            {{ $material->materials_uploadDate ? \Carbon\Carbon::parse($material->materials_uploadDate)->format('M d, Y') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <a href="{{ route('materials-edit', $material->id) }}" style="width: 40px; height: 40px; background: #f1f5f9; color: #6366f1; border-radius: 0.5rem; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0;" title="Edit Material">
                                                    <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                                </a>
                                                <form action="{{ route('materials-destroy', $material->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="width: 40px; height: 40px; background: #fef2f2; color: #ef4444; border-radius: 0.5rem; border: 1px solid #fee2e2; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" title="Delete Material">
                                                        <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                                    </button>
                                                </form>
                                                @php
                                                    $filename = basename($material->file_path);
                                                @endphp
                                                <a href="{{ route('materials.show', ['filename' => $filename]) }}" target="_blank" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3); transition: transform 0.2s; white-space: nowrap;">
                                                    <i data-feather="external-link" style="width: 18px; height: 18px;"></i>
                                                    Open
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="background: white; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 2rem; text-align: center;">
                                    <i data-feather="file-text" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 1rem;"></i>
                                    <p style="color: #64748b; margin: 0; font-weight: 500;">No materials added yet. Click "Add Material" to upload files.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Assessments Section -->
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
                <i data-feather="clipboard" style="width: 24px; height: 24px; color: #6366f1;"></i>
                Assessments
            </h2>
            
            @if($assessments->isEmpty())
                <div style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 3rem; text-align: center;">
                    <i data-feather="clipboard" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 1rem;"></i>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">No assessments created yet. Click "Create Assessment" to get started.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($assessments as $assessment)
                        <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #10b981; transition: all 0.3s;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">
                                        {{ $assessment->title ?? 'Assessment #'.$assessment->id }}
                                    </h3>
                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; color: #475569;">
                                        <i data-feather="tag" style="width: 16px; height: 16px;"></i>
                                        <span style="font-weight: 600; text-transform: capitalize;">{{ ucfirst($assessment->type ?? 'quiz') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('assessments.edit', $assessment->id) }}" style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; white-space: nowrap;">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
        div[style*="display: flex"][style*="justify-content: space-between"] {
            flex-direction: column !important;
            gap: 1rem !important;
        }
    }
    a[style*="background"]:hover {
        transform: translateY(-2px);
    }
    div[style*="border-left: 4px solid"]:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    }
    /* Material box hover effect */
    div[style*="display: flex; justify-content: space-between; align-items: center"][style*="background: white"]:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
        transform: translateY(-2px);
        transition: all 0.3s;
    }
    /* Icon button hover effects */
    a[title="Edit Material"]:hover,
    a[title="Edit Section Title"]:hover {
        background: #6366f1 !important;
        color: white !important;
        transform: scale(1.05);
    }
    button[title="Delete Material"]:hover,
    button[title="Delete Section Title"]:hover {
        background: #ef4444 !important;
        color: white !important;
        transform: scale(1.05);
    }
    /* Add Material button hover */
    a[style*="background: linear-gradient(135deg, #10b981"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4) !important;
    }
</style>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 1rem; padding: 2rem; max-width: 500px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-feather="alert-triangle" style="width: 24px; height: 24px; color: #ef4444;"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #1e293b;">Delete Course</h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #64748b;">This action cannot be undone</p>
            </div>
        </div>
        
        <p style="color: #475569; margin-bottom: 1.5rem; line-height: 1.6;">
            Are you sure you want to delete <strong id="deleteCourseName" style="color: #1e293b;"></strong>? 
            This will permanently remove the course and all its content including sections, materials, and assessments.
        </p>
        
        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <button type="button" onclick="closeDeleteModal()" style="padding: 0.75rem 1.5rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBtn" onclick="proceedDelete()" style="padding: 0.75rem 1.5rem; background: #ef4444; color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Delete Course
            </button>
        </div>
    </div>
</div>

<script>
    let deleteSubjectId = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
    
    function confirmDelete(subjectId, courseName) {
        deleteSubjectId = subjectId;
        document.getElementById('deleteCourseName').textContent = courseName;
        document.getElementById('deleteModal').style.display = 'flex';
        
        // Re-initialize feather icons in modal
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteSubjectId = null;
    }
    
    function proceedDelete() {
        if (deleteSubjectId) {
            document.getElementById('delete-form-' + deleteSubjectId).submit();
        }
    }
    
    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('deleteModal').style.display === 'flex') {
            closeDeleteModal();
        }
    });
</script>
@endsection


