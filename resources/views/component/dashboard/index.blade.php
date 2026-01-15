@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">

    <!-- Dynamically load dashboard based on role -->
    @if(auth()->user()->hasRole('Educator'))
    @include('component.dashboard.teacher')
    @elseif(auth()->user()->hasRole('Learner'))
    @include('component.dashboard.student')
    @else
    <!-- Default fallback dashboard -->
    <div class="alert alert-warning">
        <i data-feather="alert-triangle" style="width: 20px; height: 20px;"></i>
        No dashboard available for your role. Please contact a teacher.
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