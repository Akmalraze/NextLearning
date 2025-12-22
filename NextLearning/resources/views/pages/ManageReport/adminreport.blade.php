@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        <h4>Admin Reports</h4>
        <p class="text-muted">View system or teacher statistics.</p>
    </div>

    <div class="card-body">

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link {{ request('tab', 'roleReport') == 'roleReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab"
                        data-bs-target="#roleReport" type="button">
                    User & Role Distribution
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ request('tab') == 'workloadReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab"
                        data-bs-target="#workloadReport" type="button">
                    Teacher Assignment Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ request('tab') == 'classAssignmentReport' ? 'active' : '' }}" 
                        data-bs-toggle="tab"
                        data-bs-target="#classAssignmentReport" type="button">
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

                    <a href="{{ route('admin.report.export', ['type' => 'workload', 'search' => request('search')]) }}"
                       class="btn btn-success">
                        Export CSV
                    </a>
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

            {{-- ================= CLASS SUBJECT ASSIGNMENT ================= --}}
            <div class="tab-pane fade {{ request('tab') == 'classAssignmentReport' ? 'show active' : '' }}" 
                 id="classAssignmentReport">

                <div class="d-flex justify-content-between mb-3">
                    <form method="GET" class="d-flex w-50">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search class / subject / teacher..."
                               value="{{ request('search') }}">
                        <input type="hidden" name="tab" value="classAssignmentReport">
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>

                    <a href="{{ route('admin.report.export', ['type' => 'class-subject', 'search' => request('search')]) }}"
                       class="btn btn-success">
                        Export CSV
                    </a>
                </div>

                <table class="table table-bordered table-striped" id="classAssignmentTable">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Assigned Teacher</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classSubjectAssignment as $class)
                            @foreach($class['subjects'] as $sub)
                            <tr>
                                <td>{{ $class['class_name'] }}</td>
                                <td>{{ $sub->subject_name }}</td>
                                <td>{{ $sub->teacher_name }}</td>
                                <td>
                                    @if(empty($sub->teacher_name))
                                        <span class="badge bg-danger">Unassigned</span>
                                    @else
                                        <span class="badge bg-success">Assigned</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
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

/* Optional: Frontend table search without reload (uncomment if needed) */

function filterTable(input, tableId) {
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    if (!table) return;

    if (table.closest('.tab-pane').classList.contains('show', 'active')) {
        table.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
}

</script>
@endsection
