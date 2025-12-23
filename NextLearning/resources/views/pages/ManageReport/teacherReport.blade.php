@extends('layouts.master')

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="mb-1">Teaching Progress Report</h4>
            <p class="text-muted mb-0">View assessment progress and student lists for your assigned classes.</p>
        </div>
    </div>

    {{-- SESSION FILTER --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.report') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Academic Session</label>
                    <select name="session" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach($allSessions as $session)
                            <option value="{{ $session }}" {{ $selectedSession == $session ? 'selected' : '' }}>
                                {{ $session }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CLASS TABS --}}
    @if(count($classReports) > 0)
        <ul class="nav nav-tabs mb-3" role="tablist">
            @foreach($classReports as $index => $class)
                <li class="nav-item">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#classTab{{ $index }}" type="button" role="tab">
                        {{ $class['class_name'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($classReports as $index => $class)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="classTab{{ $index }}">
                    {{-- EXPORT BUTTONS --}}
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        {{-- Progress --}}
                        <form method="GET" action="{{ route('teacher.report.export') }}">
                            <input type="hidden" name="type" value="progress">
                            <input type="hidden" name="class_ids[]" value="{{ $class['class_id'] }}">
                            <input type="hidden" name="session" value="{{ $selectedSession }}">
                            <button class="btn btn-outline-primary btn-sm">Export Progress CSV</button>
                        </form>
                        {{-- Students --}}
                        <form method="GET" action="{{ route('teacher.report.export') }}">
                            <input type="hidden" name="type" value="students">
                            <input type="hidden" name="class_ids[]" value="{{ $class['class_id'] }}">
                            <input type="hidden" name="session" value="{{ $selectedSession }}">
                            <button class="btn btn-outline-secondary btn-sm">Export Students CSV</button>
                        </form>
                    </div>

                    {{-- SUB TABS --}}
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#progress{{ $index }}">
                                Progress
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#students{{ $index }}">
                                Students
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- PROGRESS TAB --}}
                        <div class="tab-pane fade show active" id="progress{{ $index }}">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Total Assessments</th>
                                            <th>Completed</th>
                                            <th>Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($class['subjects'] as $subject)
                                            <tr>
                                                <td>{{ $subject['subject_name'] }}</td>
                                                <td>{{ $subject['total'] }}</td>
                                                <td>{{ $subject['completed'] }}</td>
                                                <td><span class="badge bg-info">{{ $subject['progress'] }}%</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">No subject data available.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- STUDENTS TAB --}}
                        <div class="tab-pane fade" id="students{{ $index }}">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($class['students'] as $student)
                                            <tr>
                                                <td>{{ $student->id }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->email }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted">No students found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">No classes assigned for the selected session.</div>
    @endif
</div>

@endsection
