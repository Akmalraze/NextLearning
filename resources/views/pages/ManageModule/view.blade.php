@extends('layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        Module: {{ $module->modules_name }}
    </div>

    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">Materials Available</h5>
            @if(!auth()->user()->hasRole('Learner')) 
            <a href="{{ route('materials-create', $module->id) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i data-feather="plus" style="width: 18px; height: 18px;"></i> Add Material
            </a>
            @endif
        </div>

        @if($module->materials && $module->materials->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($module->materials as $material)
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
            <div style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 2rem; text-align: center;">
                <i data-feather="file-text" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 1rem;"></i>
                <p style="color: #64748b; margin: 0; font-weight: 500;">No materials available for this module.</p>
            </div>
        @endif
    </div>
</div>
@endsection
