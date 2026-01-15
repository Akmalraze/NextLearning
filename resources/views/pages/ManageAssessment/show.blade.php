@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assessment Details</h5>
        <div>
            @if(auth()->user()->hasRole('Educator') && $assessment->teacher_id === auth()->id())
            <a href="{{ route('assessments.edit', $assessment->id) }}" class="btn btn-warning btn-sm">
                <span data-feather="edit-2"></span> Edit
            </a>
            @endif
            <a href="{{ route('assessments.index', ['class_id' => $assessment->class_id, 'subject_id' => $assessment->subject_id]) }}" class="btn btn-secondary btn-sm">
                <span data-feather="arrow-left"></span> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <h4>{{ $assessment->title }}</h4>
                @if($assessment->description)
                <p class="text-muted">{{ $assessment->description }}</p>
                @endif
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-{{ $assessment->type === 'quiz' ? 'info' : ($assessment->type === 'test' ? 'warning' : 'success') }} fs-6">
                    {{ ucfirst($assessment->type) }}
                </span>
                @if($assessment->is_published)
                <span class="badge bg-success fs-6">Published</span>
                @else
                <span class="badge bg-secondary fs-6">Draft</span>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="40%">Class:</th>
                            <td>{{ $assessment->class ? $assessment->class->form_level . ' ' . $assessment->class->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>{{ $assessment->subject ? $assessment->subject->name . ' (' . $assessment->subject->code . ')' : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Teacher:</th>
                            <td>{{ $assessment->teacher ? $assessment->teacher->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Marks:</th>
                            <td>{{ number_format($assessment->total_marks, 2) }}</td>
                        </tr>
                        @if($assessment->type === 'quiz' && $assessment->time_limit)
                        <tr>
                            <th>Time Limit:</th>
                            <td>{{ $assessment->time_limit }} minutes</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="40%">Start Date & Time:</th>
                            <td>{{ $assessment->start_date ? $assessment->start_date->format('F d, Y h:i A') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>End Date & Time:</th>
                            <td>{{ $assessment->end_date ? $assessment->end_date->format('F d, Y h:i A') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $assessment->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($assessment->is_published)
                                <span class="badge bg-success">Published - Visible to students</span>
                                @else
                                <span class="badge bg-secondary">Draft - Not visible to students</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->hasRole('Educator') && $assessment->teacher_id === auth()->id())
        <!-- Teacher Actions -->
        <div class="alert alert-info mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
            <h6 class="alert-heading">
                        <span data-feather="info"></span> Assessment Details
            </h6>
                    <p class="mb-0">To manage questions or materials, click the <strong>"Edit"</strong> button above.</p>
                </div>
                <a href="{{ route('assessments.submissions', $assessment->id) }}" class="btn btn-primary">
                    <span data-feather="users"></span> View Student Submissions
                </a>
            </div>
        </div>

        <!-- Quiz Questions Display (Read-only for teachers in details view) -->
        @if($assessment->type === 'quiz')
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Questions</h6>
            </div>
            <div class="card-body">
                @forelse($assessment->questions as $index => $question)
                @php
                    $options = $question->options ?? [];
                    if (empty($options) || !is_array($options)) {
                        $options = array_values(array_filter([
                            $question->option_a ?? null,
                            $question->option_b ?? null,
                            $question->option_c ?? null,
                            $question->option_d ?? null,
                        ], function ($opt) { return $opt !== null && $opt !== ''; }));
                    }
                    $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : explode(',', $question->correct_answer);
                @endphp
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="mb-2">Question {{ $index + 1 }} ({{ number_format($question->marks, 2) }} marks)</h6>
                        <p class="mb-3">{{ $question->question }}</p>
                        @if($question->question_type === 'short_answer')
                            <p class="text-muted"><em>Short Answer Question</em></p>
                            @if($question->correct_answer)
                            <p class="text-success"><strong>Sample Answer:</strong> {{ $question->correct_answer }}</p>
                            @endif
                        @else
                            <div class="mb-2">
                                @foreach($options as $optIndex => $optText)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="{{ $question->question_type === 'checkboxes' ? 'checkbox' : 'radio' }}" disabled 
                                           {{ in_array((string)$optIndex, array_map('strval', $correctAnswers)) ? 'checked' : '' }}>
                                    <label class="form-check-label {{ in_array((string)$optIndex, array_map('strval', $correctAnswers)) ? 'text-success fw-bold' : '' }}">
                                        Option {{ $optIndex + 1 }}: {{ $optText }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No questions added yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        <!-- Materials Section for Test/Homework (Teacher view) -->
        @if(in_array($assessment->type, ['test', 'homework']))
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Materials</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
                    <span data-feather="upload"></span> Upload Material
                </button>
            </div>
            <div class="card-body">
                @forelse($assessment->materials as $material)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <span data-feather="file"></span>
                                    {{ $material->file_name }}
                                </h6>
                                @if($material->description)
                                <p class="text-muted mb-1">{{ $material->description }}</p>
                                @endif
                                <small class="text-muted">
                                    Size: {{ number_format($material->file_size / 1024, 2) }} KB | 
                                    Uploaded: {{ $material->created_at->format('M d, Y') }}
                                </small>
                            </div>
                            <div>
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <span data-feather="download"></span> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No materials uploaded yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        @elseif(auth()->user()->hasRole('Learner'))
        <!-- Learner view -->
        @php
            $timeCheck = $timeCheck ?? ['within' => true, 'reason' => null, 'message' => null];
            $isWithinTime = $timeCheck['within'] ?? true;
        @endphp
        
        @if($assessment->type === 'quiz')
        @php
            $inProgressSubmission = $allSubmissions->whereNotNull('started_at')->whereNull('submitted_at')->first();
            $hasStarted = $inProgressSubmission !== null;
            $isSubmitted = $inProgressSubmission === null && $allSubmissions->whereNotNull('submitted_at')->isNotEmpty();
            $timeLimit = $assessment->time_limit;
        @endphp
        
        @if(!$isWithinTime)
        <!-- Time restriction message -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Quiz Not Available</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ $timeCheck['reason'] === 'not_started' ? 'info' : 'warning' }}">
                    <p class="mb-0">{{ $timeCheck['message'] }}</p>
                </div>
            </div>
        </div>
        @elseif(!$hasStarted && !$isSubmitted)
        <!-- Start Quiz Button -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Ready to Start?</h6>
            </div>
            <div class="card-body text-center">
                @if($timeLimit)
                <p class="mb-3">This quiz has a time limit of <strong>{{ $timeLimit }} minutes</strong>.</p>
                @endif
                @if($assessment->end_date)
                <p class="mb-3">You can take this quiz until <strong>{{ $assessment->end_date->format('F d, Y h:i A') }}</strong>.</p>
                @endif
                <p class="mb-4">Once you click "Start Quiz", the timer will begin. Make sure you are ready before starting.</p>
                <form action="{{ route('assessments.startQuiz', $assessment->id) }}" method="POST">
                        @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        Start Quiz
                    </button>
                </form>
            </div>
        </div>
        @elseif($hasStarted && !$isSubmitted && $isWithinTime)
        <!-- Quiz attempt form with timer -->
        <div class="row mt-4">
            <!-- Question Navigator Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h6 class="mb-0">Question Navigator</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Progress: <span id="questionProgress">0/0</span></small>
                        </div>
                        <div class="d-flex flex-wrap gap-2" id="questionNavigator">
                            @foreach($assessment->questions as $index => $question)
                            <button type="button" 
                                    class="btn btn-sm question-nav-btn {{ $index === 0 ? 'btn-primary' : 'btn-outline-secondary' }}" 
                                    data-question-index="{{ $index }}"
                                    id="nav-btn-{{ $index }}">
                                {{ $index + 1 }}
                            </button>
                            @endforeach
                        </div>
                        <hr>
                        <div class="small">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-success me-2" style="width: 20px; height: 20px;"></span>
                                <span>Answered</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2" style="width: 20px; height: 20px; opacity: 0.5;"></span>
                                <span>Not Answered</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Question Area -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <span id="questionCounter">Question 1 of {{ $assessment->questions->count() }}</span>
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-warning" id="pauseResumeBtn">
                                <span data-feather="pause"></span> <span id="pauseResumeText">Pause</span>
                            </button>
                            @if($timeLimit)
                            <div class="badge bg-{{ $timeLimit > 5 ? 'info' : 'danger' }} fs-6" id="quizTimer">
                                <span data-feather="clock"></span> <span id="timerDisplay">--:--</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" id="quizContent">
                        <div id="pausedMessage" class="alert alert-warning text-center" style="display: none;">
                            <h5><span data-feather="pause-circle"></span> Quiz Paused</h5>
                            <p class="mb-0">The timer is still running. Click "Resume" to continue answering questions.</p>
                        </div>
                        <div id="questionsContainer">
                            <form action="{{ route('assessments.submitQuiz', $assessment->id) }}" method="POST" id="quizForm">
                                <input type="hidden" name="submission_id" value="{{ $inProgressSubmission->id }}">
                                @csrf
                                @forelse($assessment->questions as $index => $question)
                                @php
                                    $options = $question->options;
                                    if (!$options || !is_array($options) || count($options) === 0) {
                                        $options = array_values(array_filter([
                                            $question->option_a,
                                            $question->option_b,
                                            $question->option_c,
                                            $question->option_d,
                                        ], function ($opt) { return $opt !== null && $opt !== ''; }));
                                    }
                                @endphp
                                <div class="question-card" data-question-index="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="mb-2">Question {{ $index + 1 }} ({{ number_format($question->marks, 2) }} marks)</h6>
                                            <p class="mb-3">{{ $question->question }}</p>

                                            @if($question->question_type === 'short_answer')
                                                <div class="mb-2">
                                                    <input type="text"
                                                           name="answers[{{ $question->id }}]"
                                                           class="form-control answer-input"
                                                           data-question-id="{{ $question->id }}"
                                                           placeholder="Enter your answer">
                                                </div>
                                            @elseif($question->question_type === 'checkboxes')
                                                @foreach($options as $optIndex => $optText)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input answer-input"
                                                           type="checkbox"
                                                           name="answers[{{ $question->id }}][]"
                                                           value="{{ $optIndex }}"
                                                           data-question-id="{{ $question->id }}"
                                                           id="q{{ $question->id }}_opt{{ $optIndex }}">
                                                    <label class="form-check-label" for="q{{ $question->id }}_opt{{ $optIndex }}">
                                                        {{ $optText }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            @else
                                                @foreach($options as $optIndex => $optText)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input answer-input"
                                                           type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $optIndex }}"
                                                           data-question-id="{{ $question->id }}"
                                                           id="q{{ $question->id }}_opt{{ $optIndex }}">
                                                    <label class="form-check-label" for="q{{ $question->id }}_opt{{ $optIndex }}">
                                                        {{ $optText }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted text-center">No questions available for this quiz.</p>
                                @endforelse

                                @if($assessment->questions->count() > 0)
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <button type="button" class="btn btn-secondary" id="prevQuestionBtn" style="display: none;">
                                        <span data-feather="arrow-left"></span> Previous
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary" id="nextQuestionBtn">
                                            Next <span data-feather="arrow-right"></span>
                                        </button>
                                        <button type="submit" class="btn btn-secondary" id="submitQuizBtn" style="display: none;" disabled title="Please answer all questions before submitting">
                                            <span data-feather="check"></span> Submit Quiz
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($hasStarted && !$isSubmitted && !$isWithinTime)
        <!-- Quiz started but time expired -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Quiz Time Expired</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p class="mb-0">{{ $timeCheck['message'] }}</p>
                </div>
            </div>
        </div>
        @elseif($isSubmitted)
        <!-- Quiz submitted - show attempts -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Quiz Attempts</h6>
                @if($canAttemptAgain && $isWithinTime)
                <form action="{{ route('assessments.startQuiz', $assessment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <span data-feather="play"></span> Start New Attempt
                    </button>
                </form>
                @endif
            </div>
            <div class="card-body">
                @php
                    $submittedSubmissions = $allSubmissions->whereNotNull('submitted_at')->sortByDesc('attempt_number');
                    $maxAttemptsDisplay = $assessment->max_attempts === null ? 'Unlimited' : $assessment->max_attempts;
                @endphp
                
                <div class="alert alert-info">
                    <p class="mb-0">
                        <strong>Maximum Attempts:</strong> {{ $maxAttemptsDisplay }} | 
                        <strong>Your Attempts:</strong> {{ $submittedSubmissions->count() }}
                        @if($assessment->max_attempts !== null)
                        / {{ $assessment->max_attempts }}
                        @endif
                    </p>
                    @if($highestScore !== null && $assessment->show_marks)
                    <p class="mb-0 mt-2">
                        <strong>Highest Score:</strong> {{ number_format($highestScore, 2) }} / {{ number_format($assessment->total_marks, 2) }}
                        ({{ number_format(($highestScore / $assessment->total_marks) * 100, 2) }}%)
                    </p>
                    @endif
                </div>

                @if($assessment->show_marks)
                <h6 class="mb-3">Your Attempts:</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Attempt #</th>
                                <th>Score</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submittedSubmissions as $attempt)
                            <tr class="{{ $attempt->score == $highestScore && $highestScore !== null ? 'table-success' : '' }}">
                                <td>Attempt {{ $attempt->attempt_number }}</td>
                                <td>
                                    @if($attempt->score !== null)
                                    <strong>{{ number_format($attempt->score, 2) }} / {{ number_format($assessment->total_marks, 2) }}</strong>
                                    <br><small class="text-muted">{{ number_format(($attempt->score / $assessment->total_marks) * 100, 2) }}%</small>
                                    @if($attempt->score == $highestScore && $highestScore !== null)
                                    <span class="badge bg-success">Highest</span>
                                    @endif
                                    @else
                                    <span class="text-muted">Not graded</span>
                                    @endif
                                </td>
                                <td>{{ $attempt->submitted_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-warning">
                    <p class="mb-0">Marks are hidden by the teacher. You will not be able to see your scores.</p>
                </div>
                @endif

                @if(!$canAttemptAgain && $assessment->max_attempts !== null)
                <div class="alert alert-warning mt-3">
                    <p class="mb-0">You have reached the maximum number of attempts for this quiz.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        @if($hasStarted && !$isSubmitted && $isWithinTime && $inProgressSubmission)
        <script>
            (function() {
                const quizForm = document.getElementById('quizForm');
                const submitBtn = document.getElementById('submitQuizBtn');
                const pauseResumeBtn = document.getElementById('pauseResumeBtn');
                const pauseResumeText = document.getElementById('pauseResumeText');
                const questionsContainer = document.getElementById('questionsContainer');
                const pausedMessage = document.getElementById('pausedMessage');
                const prevBtn = document.getElementById('prevQuestionBtn');
                const nextBtn = document.getElementById('nextQuestionBtn');
                const questionCounter = document.getElementById('questionCounter');
                const questionProgress = document.getElementById('questionProgress');
                
                const submissionId = {{ $inProgressSubmission->id }};
                const totalQuestions = {{ $assessment->questions->count() }};
                let currentQuestionIndex = 0;
                let isPaused = false;
                
                // Storage key for answers
                const storageKey = `quiz_answers_${submissionId}`;
                
                // Load saved answers from localStorage
                function loadAnswers() {
                    const saved = localStorage.getItem(storageKey);
                    if (saved) {
                        try {
                            const answers = JSON.parse(saved);
                            Object.keys(answers).forEach(questionId => {
                                const value = answers[questionId];
                                const inputs = document.querySelectorAll(`[name*="[${questionId}]"][data-question-id="${questionId}"]`);
                                
                                inputs.forEach(input => {
                                    if (input.type === 'checkbox') {
                                        input.checked = Array.isArray(value) && value.includes(input.value);
                                    } else if (input.type === 'radio') {
                                        input.checked = input.value == value;
                                    } else {
                                        input.value = value || '';
                                    }
                                });
                            });
                        } catch (e) {
                            console.error('Error loading answers:', e);
                        }
                    }
                    updateQuestionStatus();
                }
                
                // Save answers to localStorage
                function saveAnswers() {
                    const answers = {};
                    const allInputs = document.querySelectorAll('.answer-input');
                    
                    allInputs.forEach(input => {
                        const questionId = input.getAttribute('data-question-id');
                        if (!questionId) return;
                        
                        if (input.type === 'checkbox') {
                            if (!answers[questionId]) answers[questionId] = [];
                            if (input.checked) {
                                answers[questionId].push(input.value);
                            }
                        } else if (input.type === 'radio') {
                            if (input.checked) {
                                answers[questionId] = input.value;
                            }
                        } else {
                            answers[questionId] = input.value;
                        }
                    });
                    
                    localStorage.setItem(storageKey, JSON.stringify(answers));
                    updateQuestionStatus();
                }
                
                // Check if all questions are answered
                function checkAllQuestionsAnswered() {
                    const saved = localStorage.getItem(storageKey);
                    let answers = {};
                    if (saved) {
                        try {
                            answers = JSON.parse(saved);
                        } catch (e) {
                            return false;
                        }
                    }
                    
                    const questionCards = document.querySelectorAll('.question-card');
                    
                    for (let i = 0; i < questionCards.length; i++) {
                        const card = questionCards[i];
                        const questionId = card.querySelector('[data-question-id]')?.getAttribute('data-question-id');
                        if (!questionId) {
                            return false;
                        }
                        
                        const value = answers[questionId];
                        
                        // Check if question has an answer
                        if (!value) {
                            return false;
                        }
                        
                        // For checkboxes, check if array has items
                        if (Array.isArray(value)) {
                            if (value.length === 0) {
                                return false;
                            }
                        } else {
                            // For radio or text inputs, check if value is not empty
                            if (value === '' || value === null || value === undefined) {
                                return false;
                            }
                        }
                    }
                    
                    return true;
                }
                
                // Update question navigator and progress
                function updateQuestionStatus() {
                    const saved = localStorage.getItem(storageKey);
                    let answers = {};
                    if (saved) {
                        try {
                            answers = JSON.parse(saved);
                        } catch (e) {
                            answers = {};
                        }
                    }
                    
                    let answeredCount = 0;
                    const questionCards = document.querySelectorAll('.question-card');
                    
                    questionCards.forEach((card, index) => {
                        const questionId = card.querySelector('[data-question-id]')?.getAttribute('data-question-id');
                        const navBtn = document.getElementById(`nav-btn-${index}`);
                        
                        if (!navBtn) return;
                        
                        // Check if question is answered
                        let isAnswered = false;
                        if (questionId && answers[questionId]) {
                            const value = answers[questionId];
                            isAnswered = Array.isArray(value) ? value.length > 0 : value !== '' && value !== null;
                        }
                        
                        if (isAnswered) {
                            answeredCount++;
                        }
                        
                        // Don't change current question's primary state
                        if (index === currentQuestionIndex) {
                            navBtn.classList.remove('btn-success', 'btn-outline-secondary');
                            navBtn.classList.add('btn-primary');
                        } else {
                            // Update non-current questions
                            if (isAnswered) {
                                navBtn.classList.remove('btn-outline-secondary', 'btn-primary');
                                navBtn.classList.add('btn-success');
                            } else {
                                navBtn.classList.remove('btn-success', 'btn-primary');
                                navBtn.classList.add('btn-outline-secondary');
                            }
                        }
                    });
                    
                    questionProgress.textContent = `${answeredCount}/${totalQuestions}`;
                    
                    // Update submit button state
                    const allAnswered = checkAllQuestionsAnswered();
                    if (submitBtn) {
                        if (allAnswered) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-secondary');
                            submitBtn.classList.add('btn-primary');
                            submitBtn.title = 'Submit Quiz';
                        } else {
                            submitBtn.disabled = true;
                            submitBtn.classList.remove('btn-primary');
                            submitBtn.classList.add('btn-secondary');
                            submitBtn.title = `Please answer all questions (${answeredCount}/${totalQuestions} answered)`;
                        }
                    }
                }
                
                // Show specific question
                function showQuestion(index) {
                    if (index < 0 || index >= totalQuestions) {
                        console.error('Invalid question index:', index);
                        return;
                    }
                    
                    // Hide all questions
                    const allCards = document.querySelectorAll('.question-card');
                    if (allCards.length === 0) {
                        console.error('No question cards found');
                        return;
                    }
                    
                    allCards.forEach(card => {
                        card.style.display = 'none';
                    });
                    
                    // Show current question
                    const currentCard = document.querySelector(`.question-card[data-question-index="${index}"]`);
                    if (currentCard) {
                        currentCard.style.display = 'block';
                    } else {
                        console.error('Question card not found for index:', index);
                    }
                    
                    // Update prev/next buttons
                    if (prevBtn) {
                        prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
                    }
                    if (nextBtn) {
                        nextBtn.style.display = index === totalQuestions - 1 ? 'none' : 'inline-block';
                    }
                    if (submitBtn) {
                        submitBtn.style.display = index === totalQuestions - 1 ? 'inline-block' : 'none';
                    }
                    
                    // Update counter
                    if (questionCounter) {
                        questionCounter.textContent = `Question ${index + 1} of ${totalQuestions}`;
                    }
                    
                    // Set current index before updating status
                    currentQuestionIndex = index;
                    
                    // Update question status to show answered indicators
                    updateQuestionStatus();
                }
                
                // Navigation handlers
                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        if (currentQuestionIndex > 0) {
                            saveAnswers();
                            showQuestion(currentQuestionIndex - 1);
                        }
                    });
                }
                
                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        if (currentQuestionIndex < totalQuestions - 1) {
                            saveAnswers();
                            showQuestion(currentQuestionIndex + 1);
                        }
                    });
                }
                
                // Question navigator button clicks
                document.querySelectorAll('.question-nav-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-question-index'));
                        saveAnswers();
                        showQuestion(index);
                    });
                });
                
                // Save answers on input change
                document.querySelectorAll('.answer-input').forEach(input => {
                    input.addEventListener('change', function() {
                        saveAnswers();
                    });
                    input.addEventListener('input', function() {
                        if (this.type === 'text') {
                            saveAnswers();
                        }
                    });
                });
                
                // Pause/Resume functionality
                if (pauseResumeBtn) {
                    pauseResumeBtn.addEventListener('click', function() {
                        isPaused = !isPaused;
                        
                        if (isPaused) {
                            // Pause: Hide questions, show paused message
                            questionsContainer.style.display = 'none';
                            pausedMessage.style.display = 'block';
                            pauseResumeText.textContent = 'Resume';
                            pauseResumeBtn.classList.remove('btn-warning');
                            pauseResumeBtn.classList.add('btn-success');
                            
                            // Change icon (if feather is available)
                            const icon = pauseResumeBtn.querySelector('[data-feather]');
                            if (icon && typeof feather !== 'undefined') {
                                icon.setAttribute('data-feather', 'play');
                                feather.replace();
                            }
                        } else {
                            // Resume: Show questions, hide paused message
                            questionsContainer.style.display = 'block';
                            pausedMessage.style.display = 'none';
                            pauseResumeText.textContent = 'Pause';
                            pauseResumeBtn.classList.remove('btn-success');
                            pauseResumeBtn.classList.add('btn-warning');
                            
                            // Change icon (if feather is available)
                            const icon = pauseResumeBtn.querySelector('[data-feather]');
                            if (icon && typeof feather !== 'undefined') {
                                icon.setAttribute('data-feather', 'pause');
                                feather.replace();
                            }
                        }
                    });
                }
                
                // Flag to bypass validation (for auto-submit on timer expiry)
                let bypassValidation = false;
                
                // Before form submit, ensure all answers are in the form
                if (quizForm) {
                    quizForm.addEventListener('submit', function(e) {
                        // First, save current answers
                        saveAnswers();
                        
                        // Check if all questions are answered (skip if bypassing validation)
                        if (!bypassValidation && !checkAllQuestionsAnswered()) {
                            e.preventDefault();
                            alert('Please answer all questions before submitting the quiz.');
                            
                            // Find first unanswered question and navigate to it
                            const savedForCheck = localStorage.getItem(storageKey);
                            let answersForCheck = {};
                            if (savedForCheck) {
                                try {
                                    answersForCheck = JSON.parse(savedForCheck);
                                } catch (e) {
                                    answersForCheck = {};
                                }
                            }
                            
                            const questionCards = document.querySelectorAll('.question-card');
                            for (let i = 0; i < questionCards.length; i++) {
                                const card = questionCards[i];
                                const questionId = card.querySelector('[data-question-id]')?.getAttribute('data-question-id');
                                if (!questionId) {
                                    showQuestion(i);
                                    return;
                                }
                                
                                const value = answersForCheck[questionId];
                                if (!value || (Array.isArray(value) && value.length === 0) || (!Array.isArray(value) && (value === '' || value === null))) {
                                    showQuestion(i);
                                    return;
                                }
                            }
                            return;
                        }
                        
                        // Reset bypass flag
                        bypassValidation = false;
                        
                        // IMPORTANT: Show all question cards before loading answers
                        // This ensures all form inputs are accessible for form submission
                        const allQuestionCards = document.querySelectorAll('.question-card');
                        allQuestionCards.forEach(card => {
                            card.style.display = 'block';
                        });
                        
                        // Load answers from localStorage into form BEFORE submission
                        // This ensures all answers are in the form, even if question cards were hidden
                        const saved = localStorage.getItem(storageKey);
                        if (saved) {
                            try {
                                const answers = JSON.parse(saved);
                                Object.keys(answers).forEach(questionId => {
                                    const value = answers[questionId];
                                    // Use exact name pattern matching for reliable selection
                                    const namePattern = `answers[${questionId}]`;
                                    
                                    // Handle radio buttons (multiple choice)
                                    const radioInputs = document.querySelectorAll(`input[type="radio"][name="${namePattern}"]`);
                                    if (radioInputs.length > 0) {
                                        radioInputs.forEach(input => {
                                            // Use strict comparison with string conversion
                                            if (String(input.value) === String(value)) {
                                                input.checked = true;
                                            } else {
                                                input.checked = false;
                                            }
                                        });
                                    }
                                    
                                    // Handle checkboxes (multiple answers)
                                    const checkboxInputs = document.querySelectorAll(`input[type="checkbox"][name="${namePattern}[]"]`);
                                    if (checkboxInputs.length > 0) {
                                        const valueArray = Array.isArray(value) ? value : [value];
                                        checkboxInputs.forEach(input => {
                                            input.checked = valueArray.includes(input.value);
                                        });
                                    }
                                    
                                    // Handle text inputs (short answer)
                                    const textInputs = document.querySelectorAll(`input[type="text"][name="${namePattern}"], textarea[name="${namePattern}"]`);
                                    if (textInputs.length > 0) {
                                        textInputs.forEach(input => {
                                            input.value = value || '';
                                        });
                                    }
                                });
                            } catch (e) {
                                console.error('Error loading answers for submit:', e);
                            }
                        }
                        
                        // Clear localStorage after submit
                        localStorage.removeItem(storageKey);
                        
                        // Form will submit naturally with all answers now in the form
                    });
                }
                
                // Initialize - ensure DOM is ready and questions exist
                function initializeQuiz() {
                    const questionCards = document.querySelectorAll('.question-card');
                    if (questionCards.length === 0) {
                        console.error('No question cards found - cannot initialize quiz navigation');
                        return;
                    }
                    
                    console.log('Initializing quiz with', questionCards.length, 'questions');
                    loadAnswers();
                    showQuestion(0);
                }
                
                // Wait for DOM to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initializeQuiz);
                } else {
                    // DOM already ready, use setTimeout to ensure rendering is complete
                    setTimeout(initializeQuiz, 50);
                }
                
                @if($timeLimit)
                // Timer functionality (only if time limit is set)
                const startedAt = '{{ $inProgressSubmission->started_at->timestamp }}';
                const timeLimitMinutes = {{ $timeLimit }};
                const timeLimitSeconds = timeLimitMinutes * 60;
                const startTime = parseInt(startedAt) * 1000; // Convert to milliseconds
                const endTime = startTime + (timeLimitSeconds * 1000);
                const timerDisplay = document.getElementById('timerDisplay');
                
                function updateTimer() {
                    const now = Date.now();
                    const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
                    
                    if (remaining <= 0) {
                        timerDisplay.textContent = '00:00';
                        timerDisplay.parentElement.classList.remove('bg-info');
                        timerDisplay.parentElement.classList.add('bg-danger');
                        
                        // Auto-submit the form (bypass validation when time expires)
                        if (quizForm && submitBtn) {
                            submitBtn.disabled = true;
                            // Save answers before auto-submit
                            saveAnswers();
                            // Bypass validation for auto-submit
                            bypassValidation = true;
                            quizForm.submit();
                        }
                        return;
                    }
                    
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    timerDisplay.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                    
                    // Change color when less than 5 minutes remain
                    if (remaining < 300 && !timerDisplay.parentElement.classList.contains('bg-danger')) {
                        timerDisplay.parentElement.classList.remove('bg-info');
                        timerDisplay.parentElement.classList.add('bg-danger');
                    }
                    
                    setTimeout(updateTimer, 1000);
                }
                
                updateTimer();
                @endif
            })();
        </script>
        @endif
        @elseif(in_array($assessment->type, ['test', 'homework']))
        @php
            $hasSubmitted = $submission && $submission->submitted_at;
        @endphp
        
        @if(!$isWithinTime && $timeCheck['reason'] === 'ended')
        <!-- Time restriction message -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Submission Closed</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p class="mb-0">{{ $timeCheck['message'] }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Materials list (read-only for students) -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Materials</h6>
            </div>
            <div class="card-body">
                @forelse($assessment->materials as $material)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <span data-feather="file"></span>
                                    {{ $material->file_name }}
                                </h6>
                                @if($material->description)
                                <p class="text-muted mb-1">{{ $material->description }}</p>
                                @endif
                                <small class="text-muted">
                                    Size: {{ number_format($material->file_size / 1024, 2) }} KB | 
                                    Uploaded: {{ $material->created_at->format('M d, Y') }}
                                </small>
                            </div>
                            <div>
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <span data-feather="download"></span> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No materials uploaded yet.</p>
                @endforelse
            </div>
        </div>

        @if($hasSubmitted)
        <!-- Already submitted message -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Answer Submitted</h6>
                @php
                    $canRemove = $timeCheck['within'] && $timeCheck['reason'] !== 'ended';
                @endphp
                @if($canRemove)
                <form action="{{ route('assessments.removeSubmission', $assessment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove your submission? You will be able to resubmit again.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <span data-feather="trash-2"></span> Remove Submission
                    </button>
                </form>
                @endif
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <p class="mb-0">You have already submitted your answer for this {{ $assessment->type }}.</p>
                    <p class="mb-0 mt-2">Submitted at: {{ $submission->submitted_at->format('F d, Y h:i A') }}</p>
                    @if($submission->answer_file_path)
                    <p class="mb-0 mt-2">
                        <a href="{{ asset('storage/' . $submission->answer_file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <span data-feather="download"></span> Download Your Submission
                        </a>
                    </p>
                    @endif
                    @if(!$canRemove)
                    <p class="mb-0 mt-2"><small class="text-muted">Submission cannot be removed after the deadline.</small></p>
                    @endif
                </div>
            </div>
        </div>
        @elseif($isWithinTime)
        <!-- Student answer upload form -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Submit Your Answer</h6>
            </div>
            <div class="card-body">
                @if($assessment->end_date)
                <div class="alert alert-info mb-3">
                    <p class="mb-0">Submission deadline: <strong>{{ $assessment->end_date->format('F d, Y h:i A') }}</strong></p>
                </div>
                @endif
                <form action="{{ route('assessments.submitHomework', $assessment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="answer_file" class="form-label">Answer File*</label>
                        <input type="file" id="answer_file" name="answer_file" class="form-control" required>
                        <small class="text-muted">Max file size: 10MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Answer</button>
                    </form>
            </div>
        </div>
        @elseif(!$isWithinTime && $timeCheck['reason'] === 'not_started')
        <!-- Not started yet message -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Submission Not Available</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p class="mb-0">{{ $timeCheck['message'] }}</p>
                </div>
            </div>
        </div>
        @endif
        @endif

        @endif

        @if(auth()->user()->hasRole('Educator') && $assessment->teacher_id === auth()->id())
        <div class="mt-4">
            <form action="{{ route('assessments.destroy', $assessment->id) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <span data-feather="trash-2"></span> Delete Assessment
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
