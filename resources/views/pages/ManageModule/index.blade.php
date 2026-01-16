@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Module Management</span>

        <!-- Subject Filter -->
        <form method="GET" action="{{ route('modules-index') }}" class="form-inline">
            <select name="subject_id" class="form-control form-control-sm mr-2">
                <option value="">-- All Subjects --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ isset($subjectId) && $subjectId == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
        </form>
    </div>

    <div class="card-body">
        @if($modules->count() > 0)
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Module Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $index => $module)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $module->modules_name }}</td>
                    <td>{{ Str::limit($module->modules_description, 60) }}</td>
                    <td>{{ $module->subject->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('modules-view', $module->id) }}" class="btn btn-sm btn-info">View</a>
                        <!-- Only show Edit and Delete buttons if the user is not a Student -->
                        <!-- Only show Edit and Delete buttons if the user is not a Student -->
                        @if(!auth()->user()->hasRole('Student')) 
                            <a href="{{ route('modules-edit', $module->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('modules-destroy', $module->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-end">
            {{ $modules->withQueryString()->links() }}
        </div>
        @else
        <p class="text-center text-muted">No modules found.</p>
        @endif

        @if($subjectId)
        @if(!auth()->user()->hasRole('Student')) 
            <a class="btn btn-primary"
            href="{{ route('modules-create', $subjectId) }}">
                Add New Module
            </a>
        @endif

            <a class="btn btn-outline-primary"
            href="{{ route('modules-list', $subjectId) }}">
                View Subject
            </a>
        @endif

    </div>
</div>

@endsection
