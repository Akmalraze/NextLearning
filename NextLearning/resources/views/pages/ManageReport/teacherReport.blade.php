@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>Teaching Progress Report</h4>
        <p class="text-muted">View assessment progress & student list for each class you teach.</p>
    </div>

    <div class="card-body">

        {{-- Filter & Export --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            {{-- Filter by Session --}}
            <form action="{{ route('teacher.report') }}" method="get" class="d-flex">
                <select name="session" class="form-select me-2">
                    <option value="">All Sessions</option>
                    @foreach($allSessions as $session)
                        <option value="{{ $session }}" {{ ($selectedSession ?? '') == $session ? 'selected' : '' }}>
                            {{ $session }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            {{-- Export Buttons --}}
            <div>
                <form action="{{ route('teacher.report.export') }}" method="get" class="d-inline me-2">
                    <input type="hidden" name="type" value="progress">
                    @if($selectedSession)
                        <input type="hidden" name="session" value="{{ $selectedSession }}">
                    @endif
                    <button type="submit" class="btn btn-success">Export Progress CSV</button>
                </form>

                <form action="{{ route('teacher.report.export') }}" method="get" class="d-inline">
                    <input type="hidden" name="type" value="students">
                    @if($selectedSession)
                        <input type="hidden" name="session" value="{{ $selectedSession }}">
                    @endif
                    <button type="submit" class="btn btn-info">Export Students CSV</button>
                </form>
            </div>
        </div>

        @if(count($classReports) > 0)
        <ul class="nav nav-tabs mb-4" role="tablist">
            @foreach($classReports as $index => $class)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                        data-bs-toggle="tab"
                        data-bs-target="#classTab{{ $index }}" type="button">
                        {{ $class['class_name'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($classReports as $index => $class)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                id="classTab{{ $index }}">

                {{-- Nested tabs: Progress / Students --}}
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill"
                            data-bs-target="#progress{{ $index }}" type="button">Progress</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill"
                            data-bs-target="#students{{ $index }}" type="button">Students</button>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Progress Table --}}
                    <div class="tab-pane fade show active" id="progress{{ $index }}">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject</th>
                                    <th>Total Assessments</th>
                                    <th>Completed Submissions</th>
                                    <th>Class Submissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($class['subjects'] as $sub)
                                <tr>
                                    <td>{{ $sub['subject_name'] }}</td>
                                    <td>{{ $sub['total'] }}</td>
                                    <td>{{ $sub['completed'] }}</td>
                                    <td style="width: 180px;">
                                        <div class="progress" style="height: 20px;">
                                            @if($sub['progress'] > 0)
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $sub['progress'] }}%;"
                                                aria-valuenow="{{ $sub['progress'] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ $sub['progress'] }}%
                                            </div>
                                            @else
                                            <div class="progress-bar bg-secondary" role="progressbar"
                                                style="width: 100%;" aria-valuenow="0"
                                                aria-valuemin="0" aria-valuemax="100">
                                                No data yet
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No subjects assigned
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Students Table --}}
                    <div class="tab-pane fade" id="students{{ $index }}">
                        <table class="table table-bordered table-striped">
                            <thead>
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
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        No students in this class
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
            @endforeach
        </div>

        @else
        <div class="alert alert-warning text-center">
            You have not been assigned to any classes yet.
        </div>
        @endif

    </div>
</div>

@endsection
