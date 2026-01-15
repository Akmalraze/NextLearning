@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bulk Create Users</h5>
        <a href="{{ route('teacher.users.index') }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back to Users
        </a>
    </div>
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

        <form action="{{ route('teacher.users.bulk-store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label"><strong>User Role</strong></label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role')==$role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" id="class-selection" style="display: none;">
                <label class="form-label"><strong>Assign to Class (for Learners)</strong></label>
                <select name="class_id" class="form-select">
                    <option value="">Select Class (Optional)</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->form_level }} {{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label"><strong>Users Data</strong></label>
                <p class="text-muted small">Enter one user per line. Format:
                    <code>Full Name, Email, Password, ID Number (optional)</code>
                </p>
                <textarea name="users_data" class="form-control font-monospace" rows="10" placeholder="John Doe, john@example.com, password123, ID001
Jane Smith, jane@example.com, password123, ID002
Ahmad Ali, ahmad@example.com, password123">{{ old('users_data') }}</textarea>
            </div>

            <div class="alert alert-info">
                <strong><span data-feather="info"></span> Tips:</strong>
                <ul class="mb-0 mt-2">
                    <li>Each line should contain: Full Name, Email, Password, ID Number (optional)</li>
                    <li>Separate fields with commas</li>
                    <li>ID Number is optional - leave empty or omit</li>
                    <li>All users will be assigned the selected role</li>
                </ul>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <span data-feather="users"></span> Create Users
                </button>
                <a href="{{ route('teacher.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const classSelection = document.getElementById('class-selection');

    function toggleClassSelection() {
        if (roleSelect.value === 'Learner') {
            classSelection.style.display = 'block';
        } else {
            classSelection.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', toggleClassSelection);
    toggleClassSelection();

    // Reinitialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
