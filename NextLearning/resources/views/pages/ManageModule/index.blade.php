@extends('layouts.master')
@section('content')

<div class="row">
   
    {{-- Main Content --}}
    <div class="col-md-9 col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Module Management</span>

                {{-- Subject Filter --}}
                <form method="GET" action="{{ route('modules-index') }}" class="form-inline">
                    <select name="subject_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="card-body">
                @if($modules->count() > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Module Name</th>
                                <th>Description</th>
                                <th>Subject</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $index => $module)
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td>{{ $module->modules_name }}</td>
                                    <td>{{ Str::limit($module->modules_description, 60) }}</td>
                                    <td>{{ $module->subject->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('modules-view', $module->id) }}" class="btn btn-sm btn-info">View</a>
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

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end">
                        {{ $modules->withQueryString()->links() }}
                    </div>
                @else
                    <p class="text-center text-muted">No modules found for this subject.</p>
                @endif

                @if($subjectId)
                    @if(!auth()->user()->hasRole('Student')) 
                        <a class="btn btn-primary" href="{{ route('modules-create', $subjectId) }}">Add New Module</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Feather Icons --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

<style>
    .sidebar {
        height: 100%;
        min-height: calc(100vh - 56px);
        padding-top: 1rem;
    }
    .bg-purple-300 {
        background-color: #e0c3fc !important;
    }
</style>

@endsection
