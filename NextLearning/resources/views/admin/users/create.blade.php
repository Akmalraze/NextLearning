@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Create User</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name*</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_number" class="form-label">ID Number</label>
                    <input type="text" id="id_number" name="id_number"
                        class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}"
                        placeholder="Student ID / Staff ID">
                    @error('id_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role*</label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role')==$role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 mb-3" id="class-field" style="display: none;">
                    <label for="class_id" class="form-label">Assign to Class</label>
                    <select name="class_id" id="class_id" class="form-select">
                        <option value="">Select Class (Optional)</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' : '' }}>
                            {{ $class->form_level }} {{ $class->name ?? $class->class_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="status" name="status" {{ old('status', true)
                    ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                    Active (user can login)
                </label>
            </div>
        </div>

        <div class="card-footer">
            <button class="btn btn-success" type="submit">
                <span data-feather="save"></span> Create User
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const classField = document.getElementById('class-field');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'Student') {
            classField.style.display = 'block';
        } else {
            classField.style.display = 'none';
        }
    });

    // Trigger on page load if role is already selected
    if (roleSelect.value === 'Student') {
        classField.style.display = 'block';
    }

    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection