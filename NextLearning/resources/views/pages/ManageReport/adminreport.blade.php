@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        <h4>Admin Reports</h4>
        <p class="text-muted">Select a report to view system or teacher statistics.</p>
    </div>

    <div class="card-body">

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

        {{-- User & Role Distribution --}}
        <div id="roleReport">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center bg-light">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h3>{{ $totalStudents ?? 0 }}</h3>
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
            {{-- Teacher Workload --}}
            <div class="tab-pane fade" id="workloadReport" role="tabpanel">
                <table class="table table-bordered table-striped">
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
</script>
@endsection
