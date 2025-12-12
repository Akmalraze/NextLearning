@extends('layouts.master')

@section('content')
<div class="container-fluid py-3">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
            <p class="text-muted mb-0">
                @if(auth()->user()->hasRole('Admin'))
                <span class="badge bg-danger">Administrator</span>
                @elseif(auth()->user()->hasRole('Teacher'))
                <span class="badge bg-warning text-dark">Teacher</span>
                @elseif(auth()->user()->hasRole('Student'))
                <span class="badge bg-info">Student</span>
                @endif
                <small class="ms-2">{{ now()->format('l, F j, Y') }}</small>
            </p>
        </div>
    </div>

    <!-- Dynamically load dashboard based on role -->
    @if(auth()->user()->hasRole('Admin'))
    @include('component.dashboard.admin')
    @elseif(auth()->user()->hasRole('Teacher'))
    @include('component.dashboard.teacher')
    @elseif(auth()->user()->hasRole('Student'))
    @include('component.dashboard.student')
    @else
    <!-- Default fallback dashboard -->
    <div class="alert alert-warning">
        <i data-feather="alert-triangle" style="width: 20px; height: 20px;"></i>
        No dashboard available for your role. Please contact the administrator.
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection