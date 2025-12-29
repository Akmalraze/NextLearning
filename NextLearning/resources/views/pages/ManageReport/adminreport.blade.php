@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        <h4>Admin Reports</h4>
        <p class="text-muted">Select a report to view system or teacher statistics.</p>
    </div>

    <div class="card-body">

<<<<<<< HEAD
<<<<<<< HEAD
        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="role-tab" data-bs-toggle="tab" data-bs-target="#roleReport" type="button" role="tab">User & Role Distribution</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="workload-tab" data-bs-toggle="tab" data-bs-target="#workloadReport" type="button" role="tab">Teacher Workload</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="classAssignment-tab" data-bs-toggle="tab" data-bs-target="#classAssignmentReport" type="button" role="tab">Class Subject Report</button>
=======
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
>>>>>>> parent of 694f6f0 (update)
            </li>
        </ul>
=======
        {{-- Report Selector --}}
        <div class="mb-4">
            <label for="reportType" class="form-label"><strong>Select Report:</strong></label>
            <select id="reportType" class="form-select w-50">
                <option value="role">User & Role Distribution</option>
                <option value="workload">Teacher Class & Subject Report</option>
            </select>
        </div>
>>>>>>> parent of 1071ee9 (update)

<<<<<<< HEAD
        {{-- User & Role Distribution --}}
        <div id="roleReport">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h3>{{ $totalStudents ?? 0 }}</h3>
=======
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
>>>>>>> parent of 694f6f0 (update)
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h5>Total Teachers</h5>
                            <h3>{{ $totalTeachers ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h5>Total Admins</h5>
                            <h3>{{ $totalAdmins ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
<<<<<<< HEAD
            {{-- Teacher Workload --}}
            <div class="tab-pane fade" id="workloadReport" role="tabpanel">
                <table class="table table-bordered table-striped">
=======
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
>>>>>>> parent of 694f6f0 (update)
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
=======
            {{-- Pie Chart --}}
            <div class="mb-4" style="max-width: 400px; margin: auto;">
                <canvas id="roleDistributionChart" height="200"></canvas>
>>>>>>> parent of 1071ee9 (update)
            </div>

<<<<<<< HEAD
            {{-- Teacher Staffing Notes --}}
            @php
                $totalClasses = \App\Models\Classes::count();
                $minTeachersNeeded = $totalClasses; // adjust if needed
            @endphp
            <div class="alert alert-info text-center">
                @if($totalTeachers < $minTeachersNeeded)
                    <strong>Warning:</strong> Not enough teachers for the {{ $totalClasses }} classes.
                @else
                    Teacher staffing is sufficient.
                @endif
=======
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
>>>>>>> parent of 694f6f0 (update)
            </div>
        </div>

        {{-- Teacher Workload --}}
        <div id="workloadReport" style="display: none;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Classes Assigned</th>
                        <th>Subjects Assigned</th>
                        <th>Total Assignments</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workload as $w)
                    <tr>
                        <td>{{ $w['teacher'] }}</td>
                        <td>{{ $w['classes'] }}</td>
                        <td>{{ $w['subjects'] }}</td>
                        <td>{{ $w['totalAssignments'] }}</td>
                        <td>
                            @if($w['totalAssignments'] > 5) {{-- adjust overload threshold --}}
                                <span class="badge bg-danger">Overloaded</span>
                            @else
                                <span class="badge bg-success">Normal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle reports
    document.getElementById('reportType').addEventListener('change', function() {
        const roleDiv = document.getElementById('roleReport');
        const workloadDiv = document.getElementById('workloadReport');
        if(this.value === 'role') {
            roleDiv.style.display = 'block';
            workloadDiv.style.display = 'none';
        } else {
            roleDiv.style.display = 'none';
            workloadDiv.style.display = 'block';
        }
    });

<<<<<<< HEAD
    // Role Distribution Pie Chart
    const ctx = document.getElementById('roleDistributionChart').getContext('2d');
    const roleDistributionChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Students', 'Teachers', 'Admins'],
            datasets: [{
                label: 'Role Distribution',
                data: [
                    {{ $totalStudents ?? 0 }},
                    {{ $totalTeachers ?? 0 }},
                    {{ $totalAdmins ?? 0 }}
                ],
                backgroundColor: ['#007bff', '#28a745', '#ffc107'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
=======
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

>>>>>>> parent of 694f6f0 (update)
</script>
@endsection
