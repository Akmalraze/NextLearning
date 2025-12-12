{{-- Admin Dashboard Component --}}

<!-- Key Metric Cards -->
<div class="row mb-4">
    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalUsers'] ?? 0 }}</div>
                        <small class="text-muted">
                            <span class="badge bg-info">{{ $stats['students'] ?? 0 }} Students</span>
                            <span class="badge bg-warning">{{ $stats['teachers'] ?? 0 }} Teachers</span>
                        </small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="users" style="width: 40px; height: 40px; color: #4e73df;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Classes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Classes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['activeClasses'] ?? 0 }}</div>
                        <small class="text-muted">Across all form levels</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="book-open" style="width: 40px; height: 40px; color: #1cc88a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Subjects Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Subjects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['activeSubjects'] ?? 0 }}</div>
                        <small class="text-muted">Available for assignment</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="book" style="width: 40px; height: 40px; color: #36b9cc;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">System Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge bg-success"><i data-feather="check-circle"
                                    style="width: 16px; height: 16px;"></i> Online</span>
                        </div>
                        <small class="text-muted">All services operational</small>
                    </div>
                    <div class="col-auto">
                        <i data-feather="activity" style="width: 40px; height: 40px; color: #1cc88a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity Row -->
<div class="row mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i data-feather="user-plus" style="width: 18px; height: 18px;"></i> Add New User
                    </a>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-success">
                        <i data-feather="plus-square" style="width: 18px; height: 18px;"></i> Create Class
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-info">
                        <i data-feather="link" style="width: 18px; height: 18px;"></i> Manage Subject Assignments
                    </a>
                    <a href="{{ route('admin.users.index') }}?tab=teachers" class="btn btn-warning">
                        <i data-feather="users" style="width: 18px; height: 18px;"></i> View All Teachers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers ?? [] as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                    <span
                                        class="badge bg-{{ $role->name == 'Admin' ? 'danger' : ($role->name == 'Teacher' ? 'warning' : 'info') }}">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent users</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Enrollment Distribution Pie Chart -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Classes by Form Level</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie" style="height: 280px; position: relative;">
                    <canvas id="enrollmentPieChart"></canvas>
                </div>
                <div class="mt-3 text-center small">
                    @foreach($enrollmentByForm ?? [] as $form => $count)
                    <span class="me-2">
                        <i class="fas fa-circle"
                            style="color: {{ $loop->index == 0 ? '#4e73df' : ($loop->index == 1 ? '#1cc88a' : ($loop->index == 2 ? '#36b9cc' : '#f6c23e')) }};"></i>
                        {{ $form }} ({{ $count }})
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Line Chart -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Weekly User Registrations</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 280px; position: relative;">
                    <canvas id="activityLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Enrollment Pie Chart
    const enrollmentData = @json($enrollmentByForm ?? []);
    const formLabels = Object.keys(enrollmentData);
    const formCounts = Object.values(enrollmentData);
    
    if (formLabels.length > 0) {
        new Chart(document.getElementById('enrollmentPieChart'), {
            type: 'doughnut',
            data: {
                labels: formLabels,
                datasets: [{
                    data: formCounts,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    borderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Activity Line Chart (Sample weekly data)
    const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const sampleActivity = [3, 5, 2, 8, 4, 1, 2]; // Sample data

    new Chart(document.getElementById('activityLineChart'), {
        type: 'line',
        data: {
            labels: weekDays,
            datasets: [{
                label: 'New Registrations',
                data: sampleActivity,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Reinitialize Feather icons
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

    .text-gray-300 {
        color: #dddfeb !important;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>