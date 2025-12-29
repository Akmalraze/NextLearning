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

        {{-- Tab Contents --}}
        <div class="tab-content">

            {{-- User & Role Distribution --}}
            <div class="tab-pane fade show active" id="roleReport" role="tabpanel">
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

                {{-- Pie Chart --}}
                <div class="mb-4" style="max-width: 400px; margin: auto;">
                    <canvas id="roleDistributionChart" height="200"></canvas>
                </div>
            </div>

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
            </div>

            {{-- Class Subject Assignment --}}
            <div class="tab-pane fade" id="classAssignmentReport" role="tabpanel">
                @foreach($classSubjectAssignment as $class)
                    <div class="mb-4">
                        <h5 class="mb-2">{{ $class['class_name'] }}</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Assigned Teacher</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($class['subjects'] as $sub)
                                <tr>
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
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No subjects assigned</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
