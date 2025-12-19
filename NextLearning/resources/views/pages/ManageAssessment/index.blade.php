@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Assessment Management</h5>
    </div>
    <div class="card-body">
        @if(auth()->user()->hasRole('Teacher'))
            @if(isset($classSubjectCombos) && $classSubjectCombos->isNotEmpty())
                <!-- Class-Subject Cards -->
                @if(!$classId || !$subjectId)
                <div class="row mb-4">
                    <h6 class="mb-3">Select Class & Subject:</h6>
                    @foreach($classSubjectCombos as $combo)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="cursor: pointer; transition: transform 0.2s;" 
                             onclick="window.location.href='{{ route('assessments.index', ['class_id' => $combo['class_id'], 'subject_id' => $combo['subject_id']]) }}'"
                             onmouseover="this.style.transform='scale(1.02)'" 
                             onmouseout="this.style.transform='scale(1)'">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <span data-feather="book-open" style="width: 20px; height: 20px;"></span>
                                    {{ $combo['class'] ? $combo['class']->form_level . ' ' . $combo['class']->name : 'N/A' }}
                                </h5>
                                <p class="card-text mb-2">
                                    <span data-feather="book" style="width: 16px; height: 16px;"></span>
                                    <strong>{{ $combo['subject'] ? $combo['subject']->name : 'N/A' }}</strong>
                                </p>
                                @if($combo['subject'] && $combo['subject']->code)
                                <small class="text-muted">Code: {{ $combo['subject']->code }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Selected Class-Subject View -->
                @if($classId && $subjectId && $selectedClass && $selectedSubject)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5>
                                <span data-feather="book-open"></span>
                                {{ $selectedClass->form_level }} {{ $selectedClass->name }} - 
                                <span data-feather="book"></span>
                                {{ $selectedSubject->name }}
                            </h5>
                            <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                <span data-feather="arrow-left"></span> Back to All Classes
                            </a>
                        </div>
                        <a href="{{ route('assessments.create', ['class_id' => $classId, 'subject_id' => $subjectId]) }}" 
                           class="btn btn-primary">
                            <span data-feather="plus"></span> Create Assessment
                        </a>
                    </div>

                    <!-- Search & Filter -->
                    <form method="GET" class="mb-4">
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by title..."
                                    value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="quiz" {{ ($type ?? '') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="test" {{ ($type ?? '') === 'test' ? 'selected' : '' }}>Test</option>
                                    <option value="homework" {{ ($type ?? '') === 'homework' ? 'selected' : '' }}>Homework</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <span data-feather="search"></span> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Assessments List -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>End Date & Time</th>
                                    <th>Total Marks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }}">
                                            {{ ucfirst($assessment->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $assessment->end_date ? $assessment->end_date->format('M d, Y h:i A') : 'N/A' }}</td>
                                    <td>{{ number_format($assessment->total_marks, 2) }}</td>
                                    <td>
                                        @if($assessment->is_published)
                                        <span class="badge bg-success">Published</span>
                                        @else
                                        <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('assessments.show', $assessment->id) }}"
                                                class="btn btn-sm btn-outline-info" title="View Details">
                                                <span data-feather="eye"></span> Details
                                            </a>
                                            <a href="{{ route('assessments.edit', $assessment->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <span data-feather="edit-2"></span> Edit
                                            </a>
                                            <form action="{{ route('assessments.destroy', $assessment->id) }}" method="POST"
                                                style="display:inline;" onsubmit="return confirm('Delete this assessment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No assessments found for this class and subject.
                                        <br>
                                        <a href="{{ route('assessments.create', ['class_id' => $classId, 'subject_id' => $subjectId]) }}" 
                                           class="btn btn-sm btn-primary mt-2">
                                            <span data-feather="plus"></span> Create First Assessment
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($assessments) && method_exists($assessments, 'links'))
                    <div class="mt-4">
                        {{ $assessments->links() }}
                    </div>
                    @endif
                @endif
            @else
                <div class="alert alert-warning">
                    <span data-feather="alert-triangle"></span>
                    You are not assigned to any classes or subjects. Please contact the administrator to assign you to classes and subjects.
                </div>
            @endif

        @elseif(auth()->user()->hasRole('Student'))
            <!-- Student View -->
            @if(isset($message))
            <div class="alert alert-warning">
                <span data-feather="alert-triangle"></span>
                {{ $message }}
            </div>
            @else
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Published Assessments</h5>
                    <div>
                        <span class="badge bg-danger me-2">
                            <span data-feather="alert-circle" style="width: 12px; height: 12px;"></span> Not Attempted
                        </span>
                        <span class="badge bg-warning me-2">In Progress</span>
                        <span class="badge bg-success">Submitted</span>
                    </div>
                </div>
                
                <!-- Search & Filter -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by title..."
                                value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="quiz" {{ ($type ?? '') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="test" {{ ($type ?? '') === 'test' ? 'selected' : '' }}>Test</option>
                                <option value="homework" {{ ($type ?? '') === 'homework' ? 'selected' : '' }}>Homework</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">
                                <span data-feather="search"></span> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Active Assessments (Still Can Answer) - Priority Order -->
                @if(isset($activeAssessments) && $activeAssessments->isNotEmpty())
                <div class="mb-5">
                    <h6 class="mb-3 text-primary">
                        <span data-feather="clock"></span> Active Assessments (Priority Order - Expires Soonest First)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>End Date & Time</th>
                                    <th>Status</th>
                                    <th>Total Marks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAssessments as $assessment)
                            @php
                                $submission = $submissions[$assessment->id] ?? null;
                                $now = now();
                                $hasStarted = $assessment->start_date === null || $now->gte($assessment->start_date);
                                
                                // Determine status (active assessments are not submitted)
                                if ($submission && $submission->started_at && !$submission->submitted_at) {
                                    $status = 'in_progress';
                                    $statusText = 'In Progress';
                                    $statusBadge = 'warning';
                                } elseif (!$hasStarted) {
                                    $status = 'not_started';
                                    $statusText = 'Not Started Yet';
                                    $statusBadge = 'info';
                                } else {
                                    $status = 'not_attempted';
                                    $statusText = 'Not Attempted';
                                    $statusBadge = 'danger';
                                }
                            @endphp
                            <tr class="{{ $status === 'not_attempted' ? 'table-danger' : ($status === 'in_progress' ? 'table-warning' : '') }}">
                                <td>
                                    <strong>{{ $assessment->title }}</strong>
                                    @if($status === 'not_attempted')
                                    <span class="badge bg-danger ms-2" title="Action Required">
                                        <span data-feather="alert-circle" style="width: 12px; height: 12px;"></span>
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }}">
                                        {{ ucfirst($assessment->type) }}
                                    </span>
                                </td>
                                <td>{{ $assessment->subject ? $assessment->subject->name : 'N/A' }}</td>
                                <td>
                                    @if($assessment->end_date)
                                        {{ $assessment->end_date->format('M d, Y h:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                                </td>
                                <td>{{ number_format($assessment->total_marks, 2) }}</td>
                                <td>
                                    <a href="{{ route('assessments.show', $assessment->id) }}"
                                        class="btn btn-sm btn-{{ $status === 'not_attempted' ? 'danger' : ($status === 'in_progress' ? 'warning' : 'outline-info') }}" 
                                        title="View">
                                        <span data-feather="{{ $status === 'not_attempted' ? 'arrow-right-circle' : 'eye' }}"></span> 
                                        {{ $status === 'not_attempted' ? 'Start' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(isset($activeAssessments) && $activeAssessments->isEmpty() && (!isset($submittedAssessments) || $submittedAssessments->isEmpty()) && (!isset($expiredAssessments) || $expiredAssessments->isEmpty()))
                <div class="alert alert-info">
                    <span data-feather="info"></span>
                    No published assessments available.
                </div>
                @endif

                <!-- Submitted Assessments (Separate Table) -->
                @if(isset($submittedAssessments) && $submittedAssessments->isNotEmpty())
                <div class="mb-5">
                    <h6 class="mb-3 text-success">
                        <span data-feather="check-circle"></span> Submitted Assessments
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-success">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Submitted Date & Time</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Total Marks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submittedAssessments as $assessment)
                                @php
                                    $submission = $submissions[$assessment->id] ?? null;
                                    $status = 'submitted';
                                    $statusText = 'Submitted';
                                    $statusBadge = 'success';
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $assessment->title }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }}">
                                            {{ ucfirst($assessment->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $assessment->subject ? $assessment->subject->name : 'N/A' }}</td>
                                    <td>
                                        @if($submission && $submission->submitted_at)
                                            {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @if($submission && $submission->score !== null && $assessment->show_marks)
                                            <strong>{{ number_format($submission->score, 2) }}</strong>
                                            <br><small class="text-muted">({{ number_format(($submission->score / $assessment->total_marks) * 100, 2) }}%)</small>
                                        @elseif($submission && $submission->score === null)
                                            <span class="text-muted">Pending</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($assessment->total_marks, 2) }}</td>
                                    <td>
                                        <a href="{{ route('assessments.show', $assessment->id) }}"
                                            class="btn btn-sm btn-outline-success" 
                                            title="View Submission">
                                            <span data-feather="eye"></span> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Expired Assessments (Separate Table) -->
                @if(isset($expiredAssessments) && $expiredAssessments->isNotEmpty())
                <div class="mb-4">
                    <h6 class="mb-3 text-muted">
                        <span data-feather="x-circle"></span> Expired Assessments (View Only)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-secondary">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>End Date & Time</th>
                                    <th>Status</th>
                                    <th>Total Marks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiredAssessments as $assessment)
                                @php
                                    $submission = $submissions[$assessment->id] ?? null;
                                    $now = now();
                                    
                                    // Determine status for expired
                                    if ($submission && $submission->submitted_at) {
                                        $status = 'submitted';
                                        $statusText = 'Submitted';
                                        $statusBadge = 'success';
                                    } else {
                                        $status = 'expired';
                                        $statusText = 'Expired - Not Submitted';
                                        $statusBadge = 'secondary';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $assessment->title }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }}">
                                            {{ ucfirst($assessment->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $assessment->subject ? $assessment->subject->name : 'N/A' }}</td>
                                    <td>
                                        @if($assessment->end_date)
                                            {{ $assessment->end_date->format('M d, Y h:i A') }}
                                            <br><small class="text-danger">(Expired)</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                                        @if($submission && $submission->score !== null && $status === 'submitted' && $assessment->show_marks)
                                        <br><small class="text-muted">Score: {{ number_format($submission->score, 2) }}/{{ number_format($assessment->total_marks, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($assessment->total_marks, 2) }}</td>
                                    <td>
                                        <a href="{{ route('assessments.show', $assessment->id) }}"
                                            class="btn btn-sm btn-outline-secondary" 
                                            title="View (Read Only)">
                                            <span data-feather="eye"></span> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif
        @else
            <div class="alert alert-danger">
                <span data-feather="alert-circle"></span>
                Access denied. Only teachers and students can view assessments.
            </div>
        @endif
    </div>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
