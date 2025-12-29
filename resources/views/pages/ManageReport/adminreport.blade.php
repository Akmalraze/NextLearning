@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        <h4>Admin Reports</h4>
        <p class="text-muted">View system or teacher statistics.</p>
    </div>

    <div class="card-body">

        {{-- Flash Message --}}
        <div id="flashMessage" class="alert alert-success d-none"></div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link {{ request('tab', 'roleReport') == 'roleReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab" data-bs-target="#roleReport" type="button">
                    User & Role Distribution
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ request('tab') == 'workloadReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab" data-bs-target="#workloadReport" type="button">
                    Teacher Assignment Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ request('tab') == 'classAssignmentReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab" data-bs-target="#classAssignmentReport" type="button">
                    Class Subject Report
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ================= USER & ROLE DISTRIBUTION ================= --}}
            <div class="tab-pane fade {{ request('tab', 'roleReport') == 'roleReport' ? 'show active' : '' }}" 
                 id="roleReport">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center bg-light">
                            <div class="card-body">
                                <h5>Total Students</h5>
                                <h3>{{ $totalStudents }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center bg-light">
                            <div class="card-body">
                                <h5>Total Teachers</h5>
                                <h3>{{ $totalTeachers }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center bg-light">
                            <div class="card-body">
                                <h5>Total Admins</h5>
                                <h3>{{ $totalAdmins }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="max-width: 400px; margin:auto">
                    <canvas id="roleDistributionChart"></canvas>
                </div>
            </div>

            {{-- ================= TEACHER WORKLOAD ================= --}}
            <div class="tab-pane fade {{ request('tab') == 'workloadReport' ? 'show active' : '' }}" 
                 id="workloadReport">

                <div class="d-flex justify-content-between mb-3">
                    <form method="GET" class="d-flex w-50">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search teacher..."
                               value="{{ request('search') }}">
                        <input type="hidden" name="tab" value="workloadReport">
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>

                    <button class="btn btn-success" id="downloadWorkloadCsv">Export CSV</button>
                </div>

                <table class="table table-bordered table-striped" id="workloadTable">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Classes Assigned</th>
                            <th>Subjects Assigned</th>
                            <th>Total Assignments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workload as $w)
                        <tr>
                            <td>{{ $w['teacher'] }}</td>
                            <td>{{ $w['classes'] }}</td>
                            <td>{{ $w['subjects'] }}</td>
                            <td>{{ $w['totalAssignments'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ================= CLASS SUBJECT REPORT ================= --}}
            <div class="tab-pane fade {{ request('tab') == 'classAssignmentReport' ? 'show active' : '' }}" 
                 id="classAssignmentReport">

                <div class="d-flex justify-content-between mb-3">
                    <form method="GET" class="d-flex w-50">
                        <select name="class_id" class="form-select me-2" required>
                            <option value="">-- Select Class --</option>
                            @foreach($allClasses as $class)
                                <option value="{{ $class->id }}" 
                                    {{ ($selectedClassId ?? '') == $class->id ? 'selected' : '' }}>
                                    Form {{ $class->form_level }} - {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tab" value="classAssignmentReport">
                        <button type="submit" class="btn btn-primary">View</button>
                    </form>

                    @if(!empty($selectedClassId))
                        <button class="btn btn-success" id="downloadClassCsv">Export CSV</button>
                    @endif
                </div>

                @if(!empty($classSubjectAssignment) && isset($classSubjectAssignment['subjects']))
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Assigned Teacher</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classSubjectAssignment['subjects'] as $sub)
                            <tr>
                                <td>{{ $sub->subject_name }}</td>
                                <td>{{ $sub->teacher_name ?? '-' }}</td>
                                <td>
                                    @if(empty($sub->teacher_name))
                                        <span class="badge bg-danger">Unassigned</span>
                                    @else
                                        <span class="badge bg-success">Assigned</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Select a class to view subjects and assigned teachers.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* Pie Chart */
new Chart(document.getElementById('roleDistributionChart'), {
    type: 'pie',
    data: {
        labels: ['Students', 'Teachers', 'Admins'],
        datasets: [{
            data: [
                {{ $totalStudents }},
                {{ $totalTeachers }},
                {{ $totalAdmins }}
            ],
            backgroundColor: ['#0d6efd', '#198754', '#ffc107']
        }]
    }
});

/* ================= CSV Download with Flash ================= */
function downloadCsv(type, classId = null) {
    let params = { type: type };
    if (classId) params.class_id = classId;

    fetch("{{ route('admin.report.export') }}?" + new URLSearchParams(params))
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Show flash message
                const flash = document.getElementById('flashMessage');
                flash.innerText = 'CSV exported successfully.';
                flash.classList.remove('d-none');

                // Trigger CSV download
                const blob = new Blob([data.csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = data.filename;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
}

document.getElementById('downloadWorkloadCsv')?.addEventListener('click', () => {
    downloadCsv('workload');
});

document.getElementById('downloadClassCsv')?.addEventListener('click', () => {
    downloadCsv('class-subject', {{ $selectedClassId ?? 'null' }});
});
</script>
@endsection
