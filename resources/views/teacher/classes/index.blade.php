@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Class Management</h5>
        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary btn-sm">
            <span data-feather="plus"></span> Add Class
        </a>
    </div>
    <div class="card-body">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('teacher.classes.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by class name..."
                        value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <select name="form_level" class="form-select">
                        <option value="">All Form Levels</option>
                        @foreach($formLevels ?? [] as $level)
                        <option value="{{ $level }}" {{ ($formLevel ?? '' )==$level ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="academic_session" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach($academicSessions ?? [] as $session)
                        <option value="{{ $session }}" {{ ($academicSession ?? '' )==$session ? 'selected' : '' }}>
                            {{ $session }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <span data-feather="search"></span> Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Classes Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Form Level</th>
                        <th>Class Name</th>
                        <th>Academic Session</th>
                        <th>Homeroom Teacher</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td>{{ $class->form_level }}</td>
                        <td>{{ $class->name }}</td>
                        <td>{{ $class->academic_session }}</td>
                        <td>
                            @if($class->homeroomTeacher)
                            {{ $class->homeroomTeacher->name }}
                            @else
                            <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $class->activeStudents()->count() }} students</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('teacher.classes.show', $class->id) }}"
                                    class="btn btn-sm btn-outline-info" title="View">
                                    <span data-feather="file-text"></span>
                                </a>
                                <a href="{{ route('teacher.classes.enrollments', $class->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Manage Enrollments">
                                    <span data-feather="users"></span>
                                </a>
                                <a href="{{ route('teacher.classes.edit', $class->id) }}"
                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                    <span data-feather="edit-2"></span>
                                </a>
                                <form action="{{ route('teacher.classes.destroy', $class->id) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this class?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <span data-feather="trash-2"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No classes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-center">
            {{ $classes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Quick Tips -->
<div class="alert alert-info mt-4">
    <strong><span data-feather="info"></span> Quick Tips - Action Icons:</strong>
    <ul class="mb-0 mt-2">
        <li><span data-feather="file-text" style="width:16px;height:16px;"></span> <strong>Manage Assignments</strong> -
            Assign teachers and
            subjects to classes</li>
        <li><span data-feather="users" style="width:16px;height:16px;"></span> <strong>Manage Enrollments</strong> - Add
            or remove students from this class</li>
        <li><span data-feather="edit-2" style="width:16px;height:16px;"></span> <strong>Edit</strong> - Modify class
            name, form level, session, or homeroom teacher</li>
        <li><span data-feather="trash-2" style="width:16px;height:16px;"></span> <strong>Delete</strong> - Permanently
            remove class (requires confirmation)</li>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
