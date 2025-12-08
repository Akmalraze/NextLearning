@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User Management</h5>
        <div>
            @can('create users')
            <a class="btn btn-success btn-sm me-2" href="{{ route('admin.users.create') }}">
                <span data-feather="user-plus"></span> Add User
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.users.bulk-create') }}">
                <span data-feather="users"></span> Bulk Create
            </a>
            @endcan
        </div>
    </div>

    <!-- Tabs for filtering by role -->
    <div class="card-body">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'students' ? 'active' : '' }}"
                    href="{{ route('admin.users.index', ['tab' => 'students']) }}">
                    <span data-feather="users"></span> Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'teachers' ? 'active' : '' }}"
                    href="{{ route('admin.users.index', ['tab' => 'teachers']) }}">
                    <span data-feather="user-check"></span> Teachers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'admins' ? 'active' : '' }}"
                    href="{{ route('admin.users.index', ['tab' => 'admins']) }}">
                    <span data-feather="shield"></span> Admins
                </a>
            </li>
        </ul>

        <!-- Search & Filter -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                        value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ ($status ?? '' )==='1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ ($status ?? '' )==='0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <span data-feather="search"></span> Search
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Roles
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Register At
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr data-entry-id="{{ $user->id }}">
                        <td>
                            {{ $user->id ?? '' }}
                        </td>
                        <td>
                            {{ $user->name ?? '' }}
                        </td>
                        <td>
                            {{ $user->email ?? '' }}
                        </td>
                        <td>
                            @foreach($user->getRoleNames() as $key => $item)
                            <span class="badge bg-info">{{ $item }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->status)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Blocked</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('Y-m-d') ?? '' }}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('edit users')
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                    <span data-feather="edit-2"></span>
                                </a>
                                @endcan

                                @can('delete users')
                                @if (auth()->user()->hasRole('Admin') && $user->id !== auth()->id())
                                @if($user->status)
                                <a href="{{ route('admin.user.toggleStatus', ['id' => $user->id, 'status' => 0]) }}"
                                    class="btn btn-sm btn-outline-danger" title="Deactivate">
                                    <span data-feather="user-x"></span>
                                </a>
                                @else
                                <a href="{{ route('admin.user.toggleStatus', ['id' => $user->id, 'status' => 1]) }}"
                                    class="btn btn-sm btn-outline-success" title="Activate">
                                    <span data-feather="user-check"></span>
                                </a>
                                @endif
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $users->links() }}
    </div>
</div>

<!-- Quick Tips -->
<div class="alert alert-info mt-4">
    <strong><span data-feather="info"></span> Quick Tips - Action Icons:</strong>
    <ul class="mb-0 mt-2">
        <li><span data-feather="edit-2" style="width:16px;height:16px;"></span> <strong>Edit</strong> - Modify user
            details, role, or class enrollment</li>
        <li><span data-feather="user-x" style="width:16px;height:16px;"></span> <strong>Deactivate</strong> -
            Temporarily disable user account (shown for active users)</li>
        <li><span data-feather="user-check" style="width:16px;height:16px;"></span> <strong>Activate</strong> -
            Re-enable user account (shown for inactive users)</li>
        <li>Use the <strong>tabs</strong> (Students, Teachers, Admins) to filter users by role</li>
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