@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        Edit Material — {{ $material->materials_name }}
    </div>

    <div class="card-body">
        <form action="{{ route('materials-update', $material->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="materials_name">Material Name *</label>
                <input type="text"
                       name="materials_name"
                       class="form-control @error('materials_name') is-invalid @enderror"
                       value="{{ old('materials_name', $material->materials_name) }}"
                       required>

                @error('materials_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="materials_notes">Notes</label>
                <textarea name="materials_notes"
                          class="form-control @error('materials_notes') is-invalid @enderror"
                          rows="3">{{ old('materials_notes', $material->materials_notes) }}</textarea>

                @error('materials_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">
                Update Material
            </button>

            @if(isset($sectionTitleId) && $sectionTitleId)
                <a href="{{ route('teacher.subjects.show', $subjectId) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            @else
                <a href="{{ route('modules-view', $material->module_id) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            @endif
        </form>
    </div>
</div>
@endsection
