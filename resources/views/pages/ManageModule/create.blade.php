@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        Create New Module
    </div>

    <div class="card-body">
        <form action="{{ route('modules-store') }}" method="POST">
            @csrf

            <!-- Hidden Subject ID -->
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">

            <!-- Module Name Input -->
            <div class="mb-3">
                <label for="modules_name">Module Name*</label>
                <input type="text" id="modules_name" name="modules_name" value="{{ old('modules_name') }}"
                    class="form-control @error('modules_name') is-invalid @enderror" required>
                @error('modules_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Module Description Input -->
            <div class="mb-3">
                <label for="modules_description">Module Description</label>
                <textarea id="modules_description" name="modules_description"
                    class="form-control @error('modules_description') is-invalid @enderror">{{ old('modules_description') }}</textarea>
                @error('modules_description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary" type="submit">Create Module</button>
        </form>
    </div>
</div>

@endsection
