{{-- Student Dashboard Component --}}

<!-- Class Info Banner -->
@if(isset($activeClass))
<div class="alert alert-info mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">
                <i data-feather="book-open" style="width: 20px; height: 20px;"></i>
                {{ $studentStats['className'] ?? 'Not Assigned' }}
            </h5>
            <small>Academic Session: {{ $activeClass->academic_session ?? 'N/A' }}</small>
        </div>
        <div>
            @if($activeClass->homeroomTeacher)
            <small class="text-muted">
                <i data-feather="user" style="width: 14px; height: 14px;"></i>
                Homeroom: {{ $activeClass->homeroomTeacher->name }}
            </small>
            @endif
        </div>
    </div>
</div>
@else
<div class="alert alert-warning mb-4">
    <i data-feather="alert-triangle" style="width: 20px; height: 20px;"></i>
    You are not enrolled in any class. Please contact your administrator.
</div>
@endif

<!-- Key Metric Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subjects Enrolled</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentStats['subjectsEnrolled'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i data-feather="book" style="width: 40px; height: 40px; color: #4e73df;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Subjects Completed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        <small class="text-muted">Coming soon</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="check-circle" style="width: 40px; height: 40px; color: #1cc88a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Activity Completed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        <small class="text-muted">Coming soon</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="activity" style="width: 40px; height: 40px; color: #36b9cc;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Activity Due</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        <small class="text-muted">Coming soon</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="clock" style="width: 40px; height: 40px; color: #f6c23e;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject Cards -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">My Subjects</h6>
    </div>
    <div class="card-body">
        @if(isset($enrolledSubjects) && count($enrolledSubjects) > 0)
        <div class="row">
            @foreach($enrolledSubjects as $subject)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'][$loop->index % 6] }} !important;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i data-feather="book" style="width: 20px; height: 20px;"></i>
                            {{ $subject->name ?? $subject->subjects_name }}
                        </h5>
                        <p class="card-text text-muted mb-2">
                            <i data-feather="code" style="width: 16px; height: 16px;"></i>
                            <small>{{ $subject->code ?? $subject->subjects_code ?? 'N/A' }}</small>
                        </p>
                        @if($subject->description)
                        <p class="card-text">
                            <small class="text-muted">{{ Str::limit($subject->description, 60) }}</small>
                        </p>
                        @endif
                        <a href="{{ route('modules-index') }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i data-feather="eye" style="width: 14px; height: 14px;"></i> View Subject
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
            <p class="mt-3">No subjects enrolled yet</p>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }

    .text-xs {
        font-size: .7rem;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>