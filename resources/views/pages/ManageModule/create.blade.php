@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        Add Section Title
    </div>

    <div class="card-body">
        <form action="{{ route('modules-store') }}" method="POST">
            @csrf

            <!-- Hidden Subject ID -->
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">

            <!-- Section Title Input -->
            <div class="mb-3">
                <label for="modules_name">Section Title* <small class="text-muted">(e.g., Week 1, Chapter 1, Unit 1)</small></label>
                <input type="text" id="modules_name" name="modules_name" value="{{ old('modules_name') }}"
                    class="form-control @error('modules_name') is-invalid @enderror" 
                    placeholder="Enter section title (e.g., Week 1, Chapter 1)" required>
                @error('modules_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Section Description Input -->
            <div class="mb-3">
                <label for="modules_description">Section Description <small class="text-muted">(Optional)</small></label>
                <textarea id="modules_description" name="modules_description"
                    class="form-control @error('modules_description') is-invalid @enderror"
                    placeholder="Add a brief description for this section (optional)">{{ old('modules_description') }}</textarea>
                @error('modules_description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary" type="submit">Create Section</button>
        </form>
    </div>
</div>

@endsection
