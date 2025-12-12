@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Create Subject</h5>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="mb-3">
                <label for="name" class="form-label">Subject Name*</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">Subject Code*</label>
                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ old('code') }}" placeholder="e.g., MATH, SCI, ENG" required>
                @error('code')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="3">{{ old('description') }}</textarea>
                @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{
                    old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Active (available for assignment)
                </label>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <span data-feather="save"></span> Create Subject
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection