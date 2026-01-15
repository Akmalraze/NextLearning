@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Section Title</h5>
        <a href="{{ route('teacher.subjects.show', $sectionTitle->subject_id) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>

    <div class="card-body">
        <form action="{{ route('teacher.section-titles.update', $sectionTitle->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Hidden Subject ID -->
            <input type="hidden" name="subject_id" value="{{ $sectionTitle->subject_id }}">

            <!-- Section Title Input -->
            <div class="mb-3">
                <label for="title">Section Title* <small class="text-muted">(e.g., Week 1, Chapter 1, Unit 1)</small></label>
                <input type="text" id="title" name="title" value="{{ old('title', $sectionTitle->title) }}"
                    class="form-control @error('title') is-invalid @enderror" 
                    placeholder="Enter section title (e.g., Week 1, Chapter 1)" required>
                @error('title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Section Description Input -->
            <div class="mb-3">
                <label for="description">Section Description <small class="text-muted">(Optional)</small></label>
                <textarea id="description" name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="3"
                    placeholder="Add a brief description for this section (optional)">{{ old('description', $sectionTitle->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Order Input -->
            <div class="mb-3">
                <label for="order">Display Order <small class="text-muted">(Optional - lower numbers appear first)</small></label>
                <input type="number" id="order" name="order" value="{{ old('order', $sectionTitle->order) }}"
                    class="form-control @error('order') is-invalid @enderror" 
                    min="0" step="1">
                @error('order')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <span data-feather="save"></span> Update Section Title
                </button>
                <a href="{{ route('teacher.subjects.show', $sectionTitle->subject_id) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection


