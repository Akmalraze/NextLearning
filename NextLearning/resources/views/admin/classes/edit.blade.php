@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Class</h5>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
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
                <label for="form_level" class="form-label">Form Level*</label>
                <select name="form_level" id="form_level" class="form-select @error('form_level') is-invalid @enderror"
                    required>
                    <option value="">Select Form Level</option>
                    <option value="Form 1" {{ old('form_level', $class->form_level) == 'Form 1' ? 'selected' : ''
                        }}>Form 1</option>
                    <option value="Form 2" {{ old('form_level', $class->form_level) == 'Form 2' ? 'selected' : ''
                        }}>Form 2</option>
                    <option value="Form 3" {{ old('form_level', $class->form_level) == 'Form 3' ? 'selected' : ''
                        }}>Form 3</option>
                    <option value="Form 4" {{ old('form_level', $class->form_level) == 'Form 4' ? 'selected' : ''
                        }}>Form 4</option>
                    <option value="Form 5" {{ old('form_level', $class->form_level) == 'Form 5' ? 'selected' : ''
                        }}>Form 5</option>
                </select>
                @error('form_level')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Class Name*</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $class->name ?? str_replace($class->form_level.' ', '', $class->class_name)) }}"
                    placeholder="e.g., Amanah, Bestari, Cerdik" required>
                <small class="text-muted">Combined with Form Level to create full class name</small>
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="academic_session" class="form-label">Academic Session*</label>
                <input type="text" id="academic_session" name="academic_session"
                    class="form-control @error('academic_session') is-invalid @enderror"
                    value="{{ old('academic_session', $class->academic_session) }}" placeholder="e.g., 2024, 2024/2025"
                    required>
                @error('academic_session')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="homeroom_teacher_id" class="form-label">Homeroom Teacher</label>
                <select name="homeroom_teacher_id" id="homeroom_teacher_id" class="form-select">
                    <option value="">Select Teacher (Optional)</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) ==
                        $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <span data-feather="save"></span> Update Class
            </button>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection