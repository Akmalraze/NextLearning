@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Assessment</h5>
        <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('assessments.update', $assessment->id) }}" method="POST">
        @csrf
        @method('PUT')
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
                <label for="title" class="form-label">Assessment Title*</label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $assessment->title) }}" required>
                @error('title')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="4">{{ old('description', $assessment->description) }}</textarea>
                @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Assessment Type*</label>
                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">Select Type</option>
                        <option value="quiz" {{ old('type', $assessment->type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="test" {{ old('type', $assessment->type) === 'test' ? 'selected' : '' }}>Test</option>
                        <option value="homework" {{ old('type', $assessment->type) === 'homework' ? 'selected' : '' }}>Homework</option>
                    </select>
                    @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_marks" class="form-label">Total Marks*</label>
                    <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" max="1000"
                        class="form-control @error('total_marks') is-invalid @enderror"
                        value="{{ old('total_marks', $assessment->total_marks) }}" required>
                    @error('total_marks')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="class_id" class="form-label">Class*</label>
                    <select id="class_id" name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $assessment->class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->form_level }} {{ $class->name }} ({{ $class->academic_session }})
                        </option>
                        @endforeach
                    </select>
                    @error('class_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label">Subject*</label>
                    <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $assessment->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }})
                        </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', $assessment->start_date ? $assessment->start_date->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}">
                    @error('start_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date"
                        class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ old('end_date', $assessment->end_date ? $assessment->end_date->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}">
                    @error('end_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" id="due_date" name="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date', $assessment->due_date ? $assessment->due_date->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}">
                    @error('due_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{
                            old('is_published', $assessment->is_published) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_published">
                            Publish Assessment (make visible to students)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">
                <span data-feather="save"></span> Update Assessment
            </button>
            <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
