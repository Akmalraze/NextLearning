@extends('layouts.master')
@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit User</h5>
        <a href="{{ route('teacher.users.index') }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('teacher.users.update', $user->id) }}" method="POST">
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
                <label for="name" class="form-label">Name*</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email*</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required>
                @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" id="id_number" name="id_number"
                    class="form-control @error('id_number') is-invalid @enderror"
                    value="{{ old('id_number', $user->id_number) }}">
                @error('id_number')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror">
                <small class="text-muted">Minimum 8 characters</small>
                @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role*</label>
                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                @error('role')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3" id="class-selection" style="display: none;">
                <label for="class_id" class="form-label">Assign to Class</label>
                <select name="class_id" id="class_id" class="form-select">
                    <option value="">Select Class (Optional)</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' : '' }}>
                        {{ $class->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <span data-feather="save"></span> Update User
            </button>
            <a href="{{ route('teacher.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
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

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
