@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Subject Management</h5>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
            <span data-feather="plus"></span> Add Subject
        </a>
    </div>
    <div class="card-body">
        <!-- Search & Filter -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or code..."
                        value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-4">
                    <select name="is_active" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ ($isActive ?? '' )==='1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($isActive ?? '' )==='0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <span data-feather="search"></span> Filter
                    </button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>{{ $subject->name ?? $subject->subjects_name }}</td>
                        <td><code>{{ $subject->code ?? $subject->subjects_code }}</code></td>
                        <td>{{ Str::limit($subject->description ?? '', 50) }}</td>
                        <td>
                            @if($subject->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                    <span data-feather="edit-2"></span>
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST"
                                    style="display:inline;" onsubmit="return confirm('Delete this subject?')">
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
                        <td colspan="5" class="text-center text-muted">No subjects found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-center">{{ $subjects->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
<div class="alert alert-info mt-4">
    <strong><span data-feather="info"></span> Quick Tips - Action Icons:</strong>
    <ul class="mb-0 mt-2">
        <li><span data-feather="edit-2" style="width:16px;height:16px;"></span> <strong>Edit</strong> - Modify subject
            name, code, description, or status</li>
        <li><span data-feather="trash-2" style="width:16px;height:16px;"></span> <strong>Delete</strong> - Permanently
            remove subject (requires confirmation)</li>
        <li>Subjects are <strong>form-agnostic</strong> - they can be assigned to any class</li>
    </ul>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection