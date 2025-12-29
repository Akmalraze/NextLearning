{{-- Teacher Dashboard Component --}}

<!-- Key Metric Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">My Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teacherStats['totalStudents'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i data-feather="users" style="width: 40px; height: 40px; color: #4e73df;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Classes Assigned</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teacherStats['totalClasses'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i data-feather="book-open" style="width: 40px; height: 40px; color: #1cc88a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Subjects Teaching</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teacherStats['totalSubjects'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i data-feather="book" style="width: 40px; height: 40px; color: #36b9cc;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">My Subjects</h6>
    </div>
    <div class="card-body">

        @if(isset($teachingClasses) && $teachingClasses->count() > 0)
            <div class="row">

                @foreach($teachingClasses as $classId => $subjects)
                    @php $class = $subjects->first()->class; @endphp

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-left: 4px solid {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$loop->index % 5] }} !important;">

                            <div class="card-body">
                                <h5 class="card-title mb-2">
                                    <i data-feather="book-open"></i>
                                    {{ $class->form_level }} {{ $class->name ?? $class->class_name }}
                                </h5>

                                <ul class="list-unstyled mb-3">
                                    @foreach($subjects as $item)
                                        <li class="mb-1 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i data-feather="book" style="width:14px;"></i>
                                                {{ $item->subject->name }}
                                            </span>

                                            <a href="{{ route('modules-list', $item->subject->id) }}"
                                            class="btn btn-sm btn-outline-primary btn-sm">
                                                Modules
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        @else
            <div class="text-center text-muted py-4">
                <i data-feather="inbox"></i>
                <p class="mt-3">No subjects assigned yet</p>
            </div>
        @endif

    </div>
</div>

{{-- 
<!-- Class Management Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">My Classes</h6>
    </div>
    <div class="card-body">
        @if(isset($teachingClasses) && $teachingClasses->count() > 0)
        <div class="row">
            <div class="row">
            @foreach($teachingClasses as $class)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$loop->index % 5] }} !important;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i data-feather="book-open" style="width: 20px; height: 20px;"></i>
                            {{ $class->form_level ?? '' }} {{ $class->name ?? $class->class_name }}
                        </h5>
                        <p class="card-text text-muted mb-2">
                            <i data-feather="calendar" style="width: 16px; height: 16px;"></i>
                            <small>{{ $class->academic_session ?? 'N/A' }}</small>
                        </p>
                        <p class="card-text">
                            <span class="badge bg-info">
                                <i data-feather="users" style="width: 14px; height: 14px;"></i>
                                {{ $class->activeStudents()->count() }} Students
                            </span>
                        </p>
                        <a href="{{ route('admin.classes.show', $class->id) }}"
                            class="btn btn-sm btn-outline-primary mt-2">
                            <i data-feather="eye" style="width: 14px; height: 14px;"></i> View Class
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i data-feather="inbox" style="width: 48px; height: 48px;"></i>
            <p class="mt-3">No classes assigned yet</p>
        </div>
        @endif
    </div>
</div> --}}

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

    .text-xs {
        font-size: .7rem;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>