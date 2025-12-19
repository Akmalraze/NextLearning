@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        Edit Material: {{ $material->materials_name }}
    </div>

    <div class="card-body">
        <form action="{{ route('materials-update', $material->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="module_id" value="{{ $material->module_id }}">
            <input type="hidden" name="subject_id" value="{{ $material->subject_id }}">

            <div class="mb-3">
                <label for="materials_name">Material Name*</label>
                <input type="text" id="materials_name" name="materials_name" value="{{ old('materials_name', $material->materials_name) }}" class="form-control @error('materials_name') is-invalid @enderror" required>
                @error('materials_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="materials_format">Material Format*</label>
                <input type="text" id="materials_format" name="materials_format" value="{{ old('materials_format', $material->materials_format) }}" class="form-control @error('materials_format') is-invalid @enderror" required>
                @error('materials_format')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="materials_uploadDate">Upload Date</label>
                <input type="date" id="materials_uploadDate" name="materials_uploadDate" value="{{ old('materials_uploadDate', $material->materials_uploadDate) }}" class="form-control @error('materials_uploadDate') is-invalid @enderror">
                @error('materials_uploadDate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="materials_notes">Material Notes</label>
                <textarea id="materials_notes" name="materials_notes" class="form-control @error('materials_notes') is-invalid @enderror">{{ old('materials_notes', $material->materials_notes) }}</textarea>
                @error('materials_notes')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Material</button>
        </form>
    </div>
</div>
@endsection
