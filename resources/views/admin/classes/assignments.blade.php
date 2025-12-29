@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Subject Assignments: {{ $class->form_level }} {{ $class->name ?? $class->class_name }}</h5>
        <a href="{{ route('admin.classes.show', $class->id) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back to Class
        </a>
    </div>
    <div class="card-body">
        <!-- Assign Teacher Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><span data-feather="user-plus"></span> Assign Teacher to Subject</h6>
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

        <!-- Current Assignments List -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <span data-feather="book-open"></span>
                    Current Subject Assignments ({{ $assignments->count() }})
                </h6>
            </div>
            <div class="card-body">
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
                                    <form action="{{ route('classes.unassign-teacher', $assignment->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove {{ $assignment->teacher->name }} from teaching {{ $assignment->subject->name }}?');"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Remove assignment">
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

        <!-- Quick Tips -->
        <div class="alert alert-info mt-4">
            <strong><span data-feather="info"></span> Quick Tips:</strong>
            <ul class="mb-0 mt-2">
                <li><span data-feather="book-open" style="width:16px;height:16px;"></span> <strong>Assign:</strong>
                    Select a subject and teacher, then click "Assign"</li>
                <li><span data-feather="x-circle" style="width:16px;height:16px;"></span> <strong>Remove:</strong> Click
                    the remove button to unassign a teacher from a subject</li>
                <li><span data-feather="alert-circle" style="width:16px;height:16px;"></span> <strong>Note:</strong>
                    Only one teacher can be assigned to each subject per class</li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection