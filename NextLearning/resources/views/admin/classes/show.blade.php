@extends('layouts.master')
@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Class Details: {{ $class->form_level }} {{ $class->name ?? $class->class_name }}</h5>
        <div>
            <a href="{{ route('admin.classes.enrollments', $class->id) }}" class="btn btn-primary btn-sm me-2">
                <span data-feather="users"></span> Manage Students
            </a>
            <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-warning btn-sm me-2">
                <span data-feather="edit-2"></span> Edit
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
                <span data-feather="arrow-left"></span> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-0">
            <div class="col-md-6">
                <p><strong>Form Level:</strong> <span class="badge bg-primary">{{ $class->form_level }}</span></p>
                <p><strong>Academic Session:</strong> {{ $class->academic_session }}</p>
                <p><strong>Homeroom Teacher:</strong>
                    @if($class->homeroomTeacher)
                    {{ $class->homeroomTeacher->name }}
                    @else
                    <span class="text-muted">Not assigned</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Students:</strong> {{ $students->total() }}</p>
                <p><strong>Subjects Assigned:</strong> {{ $subjects->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Subject-Teacher Assignments -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><span data-feather="book-open"></span> Subject-Teacher Assignments</h6>
    </div>
    <div class="card-body">
        <!-- Assign Teacher Form -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong><span data-feather="user-plus"></span> Assign Teacher to Subject</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.classes.assign-teacher', $class->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <label for="subject_id" class="form-label">Subject*</label>
                            <select name="subject_id" id="subject_id" class="form-select" required>
                                <option value="">Select Subject...</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="teacher_id" class="form-label">Teacher*</label>
                            <select name="teacher_id" id="teacher_id" class="form-select" required>
                                <option value="">Select Teacher...</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                    @if($teacher->id_number)
                                    (ID: {{ $teacher->id_number }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <span data-feather="plus-circle"></span> Assign
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Assignments -->
        @if($assignments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Assigned Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    <tr>
                        <td>
                            <span class="badge bg-info">{{ $assignment->subject->code }}</span>
                        </td>
                        <td>
                            <strong>{{ $assignment->subject->name }}</strong>
                        </td>
                        <td>
                            <span data-feather="user"></span>
                            {{ $assignment->teacher->name }}
                            @if($assignment->teacher->id_number)
                            <small class="text-muted">(ID: {{ $assignment->teacher->id_number }})</small>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.classes.unassign-teacher', $assignment->id) }}" method="POST"
                                onsubmit="return confirm('Remove {{ $assignment->teacher->name }} from teaching {{ $assignment->subject->name }}?');"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove assignment">
                                    <span data-feather="x-circle"></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-warning">
            <span data-feather="alert-triangle"></span> No subjects assigned to teachers yet in this class.
        </div>
        @endif
    </div>
</div>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection