@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header">
        Edit Module
    </div>

    <div class="card-body">
        <form action="{{ route('modules-update', $module->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="subject_id" value="{{ $module->subject_id }}">

            <div class="mb-3">
                <label for="modules_name">Module Name*</label>
                <input type="text" id="modules_name" name="modules_name" value="{{ old('modules_name', $module->modules_name) }}"
                    class="form-control @error('modules_name') is-invalid @enderror" required>
                @error('modules_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="modules_description">Module Description</label>
                <textarea id="modules_description" name="modules_description"
                    class="form-control @error('modules_description') is-invalid @enderror">{{ old('modules_description', $module->modules_description) }}</textarea>
                @error('modules_description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button class="btn btn-primary" type="submit">Update Module</button>
        </form>
    </div>
</div>

@endsection
