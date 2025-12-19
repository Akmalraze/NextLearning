@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Student Submissions - {{ $assessment->title }}</h5>
        <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back to Assessment
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="40%">Assessment Type:</th>
                            <td>
                                <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }}">
                                    {{ ucfirst($assessment->type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Class:</th>
                            <td>{{ $assessment->class ? $assessment->class->form_level . ' ' . $assessment->class->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>{{ $assessment->subject ? $assessment->subject->name : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="40%">Total Marks:</th>
                            <td>{{ number_format($assessment->total_marks, 2) }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $assessment->end_date ? $assessment->end_date->format('F d, Y h:i A') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Total Students:</th>
                            <td>{{ $students->count() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>ID Number</th>
                        <th>Status</th>
                        @if($assessment->type === 'quiz')
                        <th>Score (Auto-graded)</th>
                        <th>Submitted At</th>
                        @else
                        <th>Submitted</th>
                        <th>Submission Date</th>
                        <th>Mark</th>
                        <th>File</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    @php
                        $submission = $submissions[$student->id] ?? null;
                        $hasSubmitted = $submission && $submission->submitted_at;
                        $isStarted = $submission && $submission->started_at;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->id_number ?? 'N/A' }}</td>
                        <td>
                            @if($hasSubmitted)
                                <span class="badge bg-success">Submitted</span>
                            @elseif($isStarted && $assessment->type === 'quiz')
                                <span class="badge bg-warning">In Progress</span>
                            @else
                                <span class="badge bg-danger">Not Submitted</span>
                            @endif
                        </td>
                        @if($assessment->type === 'quiz')
                        <td>
                            @if($hasSubmitted)
                                @if($submission->score !== null)
                                    <strong>{{ number_format($submission->score, 2) }} / {{ number_format($assessment->total_marks, 2) }}</strong>
                                    <br><small class="text-muted">{{ number_format(($submission->score / $assessment->total_marks) * 100, 2) }}%</small>
                                @else
                                    <span class="text-muted">Not graded</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($hasSubmitted)
                                {{ $submission->submitted_at->format('M d, Y h:i A') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @else
                        <td>
                            @if($hasSubmitted)
                                <span class="badge bg-success">
                                    <span data-feather="check-circle"></span> Yes
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <span data-feather="x-circle"></span> No
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($hasSubmitted)
                                {{ $submission->submitted_at->format('M d, Y h:i A') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($hasSubmitted)
                                <form action="{{ route('assessments.submissions.updateMark', [$assessment->id, $submission->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="input-group" style="width: 180px;">
                                        <input type="number" 
                                               name="score" 
                                               class="form-control form-control-sm" 
                                               step="0.01" 
                                               min="0" 
                                               max="{{ $assessment->total_marks }}"
                                               value="{{ $submission->score ?? '' }}"
                                               placeholder="Enter mark"
                                               required>
                                        <button type="submit" class="btn btn-sm btn-primary" title="Save mark">
                                            <span data-feather="save"></span>
                                        </button>
                                    </div>
                                    <div>
                                        @if($submission->score !== null)
                                        <small class="text-muted">{{ number_format(($submission->score / $assessment->total_marks) * 100, 2) }}%</small>
                                        @endif
                                    </div>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($hasSubmitted && $submission->answer_file_path)
                                <a href="{{ asset('storage/' . $submission->answer_file_path) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Download submitted file">
                                    <span data-feather="download"></span>
                                </a>
                                @if($submission->answer_original_name)
                                <br><small class="text-muted">{{ Str::limit($submission->answer_original_name, 20) }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endif
                    @empty
                    <tr>
                        <td colspan="{{ $assessment->type === 'quiz' ? '6' : '8' }}" class="text-center text-muted">
                            No students enrolled in this class.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <div class="alert alert-info">
                <h6 class="alert-heading">
                    <span data-feather="info"></span> Summary
                </h6>
                <p class="mb-0">
                    <strong>Total Students:</strong> {{ $students->count() }} | 
                    <strong>Submitted:</strong> {{ $submissions->filter(function($s) { return $s->submitted_at !== null; })->count() }} | 
                    <strong>Not Submitted:</strong> {{ $students->count() - $submissions->filter(function($s) { return $s->submitted_at !== null; })->count() }}
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
