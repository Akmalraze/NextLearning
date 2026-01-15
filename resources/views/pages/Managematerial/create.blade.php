@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        Add Material — {{ isset($sectionTitle) ? $sectionTitle->title : ($module->modules_name ?? 'N/A') }}
    </div>

    <div class="card-body">
        <form action="{{ route('materials-store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- REQUIRED --}}
            @if(isset($sectionTitle))
                <input type="hidden" name="section_title_id" value="{{ $sectionTitle->id }}">
            @endif
            @if(isset($module))
                <input type="hidden" name="module_id" value="{{ $module->id }}">
            @endif

            <div class="mb-3">
                <label for="materials_name">Material Name *</label>
                <input type="text"
                       name="materials_name"
                       class="form-control @error('materials_name') is-invalid @enderror"
                       value="{{ old('materials_name') }}"
                       required>

                @error('materials_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="file">Upload File *</label>
                <input type="file"
                       name="file"
                       class="form-control @error('file') is-invalid @enderror"
                       required>

                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="materials_notes">Notes (optional)</label>
                <textarea name="materials_notes"
                          class="form-control @error('materials_notes') is-invalid @enderror"
                          rows="3">{{ old('materials_notes') }}</textarea>

                @error('materials_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">
                Upload Material
            </button>
        </form>
    </div>
</div>
@endsection
