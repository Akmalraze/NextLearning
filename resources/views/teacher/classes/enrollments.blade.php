@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Enrollments: {{ $class->form_level }} {{ $class->name ?? $class->class_name }}</h5>
        <a href="{{ route('teacher.classes.show', $class->id) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back to Class
        </a>
    </div>
    <div class="card-body">
        <!-- Add Student Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><span data-feather="user-plus"></span> Add Student to Class</h6>
            </div>
            <div class="card-body">
                @if($availableStudents->count() > 0)
                <form action="{{ route('teacher.classes.enroll', $class->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <select name="student_id" class="form-select" required>
                                <option value="">Select a student...</option>
                                @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }} ({{ $student->email }})
                                    @if($student->id_number)
                                    - ID: {{ $student->id_number }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <span data-feather="plus-circle"></span> Enroll Student
                            </button>
                        </div>
                    </div>
                </form>
                @else
                <div class="alert alert-info mb-0">
                    <span data-feather="info"></span> All students are already enrolled in classes.
                </div>
                @endif
            </div>
        </div>

        <!-- Enrolled Students List -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <span data-feather="users"></span>
                    Enrolled Students ({{ $enrolledStudents->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($enrolledStudents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>ID Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrolledStudents as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if($student->id_number)
                                    <span class="badge bg-secondary">{{ $student->id_number }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <form action="{{ route('teacher.classes.unenroll', [$class->id, $student->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove {{ $student->name }} from this class?');"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Remove from class">
                                            <span data-feather="user-x"></span>
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
                    <span data-feather="alert-triangle"></span> No students enrolled in this class yet.
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="alert alert-info mt-4">
            <strong><span data-feather="info"></span> Quick Tips:</strong>
            <ul class="mb-0 mt-2">
                <li><span data-feather="plus-circle" style="width:16px;height:16px;"></span> <strong>Enroll:</strong>
                    Select a student from the dropdown and click "Enroll Student"</li>
                <li><span data-feather="user-x" style="width:16px;height:16px;"></span> <strong>Remove:</strong> Click
                    the remove button to unenroll a student from this class</li>
                <li><span data-feather="alert-circle" style="width:16px;height:16px;"></span> <strong>Note:</strong>
                    Only students without an active class enrollment are shown in the dropdown</li>
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
