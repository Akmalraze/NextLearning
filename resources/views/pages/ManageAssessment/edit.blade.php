@extends('layouts.master')
@section('content')
<style>
    span.invalid-feedback,
    .invalid-feedback.d-block,
    .invalid-feedback {
        color: #dc3545 !important;
        display: block !important;
    }
    .form-label.text-danger,
    .text-danger {
        color: #dc3545 !important;
    }
    .form-control.border-danger,
    .form-select.border-danger,
    .border-danger {
        border-color: #dc3545 !important;
    }
    .alert-danger .text-danger,
    .alert-danger ul li,
    .alert-danger strong.text-danger {
        color: #dc3545 !important;
    }
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Assessment</h5>
        <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-secondary btn-sm"
            title="Go back to assessment details" aria-label="Go back">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('assessments.update', $assessment->id) }}" method="POST" id="editAssessmentForm">
        @csrf
        @method('PUT')
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong class="text-danger">Please fix the following errors (in order from top to bottom):</strong>
                <ul class="mb-0 mt-2">
                    @php
                        // Define field order as they appear in the form (top to bottom)
                        $fieldOrder = [
                            'title',
                            'description',
                            'type',
                            'total_marks',
                            'time_limit',
                            'max_attempts',
                            'class_id',
                            'subject_id',
                            'start_date',
                            'end_date',
                            'questions',
                            'materials',
                        ];
                        
                        // Build array of all errors with their field names
                        $errorsByField = [];
                        foreach ($errors->keys() as $key) {
                            $baseField = explode('.', $key)[0]; // Get base field name
                            if (!isset($errorsByField[$baseField])) {
                                $errorsByField[$baseField] = [];
                            }
                            // Store the key so we can retrieve errors
                            $errorsByField[$baseField][] = $key;
                        }
                        
                        // Now build ordered error list based on field order
                        $orderedErrors = [];
                        $processedFields = [];
                        
                        // Process each field in the defined order
                        foreach ($fieldOrder as $field) {
                            // Check if this field has any errors
                            if (isset($errorsByField[$field])) {
                                // Get all error keys for this field (including nested)
                                foreach ($errorsByField[$field] as $key) {
                                    // Get all error messages for this key
                                    foreach ($errors->get($key) as $errorMessage) {
                                        $orderedErrors[] = $errorMessage;
                                    }
                                }
                                $processedFields[] = $field;
                            }
                        }
                        
                        // Add any remaining errors for fields not in our order list
                        foreach ($errorsByField as $field => $keys) {
                            if (!in_array($field, $processedFields)) {
                                foreach ($keys as $key) {
                                    foreach ($errors->get($key) as $errorMessage) {
                                        $orderedErrors[] = $errorMessage;
                                    }
                                }
                            }
                        }
                    @endphp
                    @foreach ($orderedErrors as $error)
                    <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="mb-3">
                <label for="title" class="form-label @error('title') text-danger @enderror">Assessment Title*</label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid border-danger @enderror"
                    value="{{ old('title', $assessment->title) }}"
                    placeholder="Enter assessment title" aria-label="Assessment title" title="Assessment title">
                @error('title')
                <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em; display: block !important;">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label @error('description') text-danger @enderror">Description</label>
                <textarea id="description" name="description"
                    class="form-control @error('description') is-invalid border-danger @enderror"
                    rows="4" placeholder="Enter assessment description (optional)" 
                    aria-label="Assessment description" title="Assessment description">{{ old('description', $assessment->description) }}</textarea>
                @error('description')
                <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label @error('type') text-danger @enderror">Assessment Type*</label>
                    <select id="type" name="type" class="form-select @error('type') is-invalid border-danger @enderror"
                        aria-label="Assessment type" title="Select assessment type">
                        <option value="">Select Type</option>
                        <option value="quiz" {{ old('type', $assessment->type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="test" {{ old('type', $assessment->type) === 'test' ? 'selected' : '' }}>Test</option>
                        <option value="homework" {{ old('type', $assessment->type) === 'homework' ? 'selected' : '' }}>Homework</option>
                    </select>
                    @error('type')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_marks" class="form-label @error('total_marks') text-danger @enderror">Total Marks*</label>
                    <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" max="1000"
                        class="form-control @error('total_marks') is-invalid border-danger @enderror"
                        value="{{ old('total_marks', $assessment->total_marks) }}"
                        placeholder="Enter total marks" aria-label="Total marks" title="Total marks for this assessment">
                    @error('total_marks')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
                @if($assessment->type === 'quiz')
                <div class="col-md-6 mb-3">
                    <label for="time_limit" class="form-label @error('time_limit') text-danger @enderror">Time Limit (minutes)*</label>
                    <input type="number" id="time_limit" name="time_limit" min="1" step="1"
                        class="form-control @error('time_limit') is-invalid border-danger @enderror"
                        value="{{ old('time_limit', $assessment->time_limit) }}"
                        placeholder="Enter time limit in minutes" aria-label="Time limit in minutes" title="Time limit in minutes">
                    @error('time_limit')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">Required for quizzes</small>
                </div>
                @endif
            </div>
            @if($assessment->type === 'quiz')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="max_attempts" class="form-label @error('max_attempts') text-danger @enderror">Maximum Attempts</label>
                    <input type="number" id="max_attempts" name="max_attempts" min="1" step="1"
                        class="form-control @error('max_attempts') is-invalid border-danger @enderror"
                        value="{{ old('max_attempts', $assessment->max_attempts) }}" 
                        placeholder="Leave empty for unlimited" aria-label="Maximum attempts" title="Maximum number of attempts allowed">
                    @error('max_attempts')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">Leave empty for unlimited attempts. System will keep the highest score.</small>
                </div>
            </div>
            @endif
            
            <!-- Show Marks option for all assessment types -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input type="hidden" name="show_marks" value="0">
                        <input type="checkbox" class="form-check-input" id="show_marks" name="show_marks" value="1" {{
                            old('show_marks', $assessment->show_marks ?? true) ? 'checked' : '' }}
                            title="Show marks to students" aria-label="Show marks to students">
                        <label class="form-check-label" for="show_marks">
                            Show Marks to Students
                        </label>
                    </div>
                    <small class="text-muted">If unchecked, students won't see their scores.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="class_id" class="form-label @error('class_id') text-danger @enderror">Class*</label>
                    <select id="class_id" name="class_id" class="form-select @error('class_id') is-invalid border-danger @enderror"
                        aria-label="Select class" title="Select class">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $assessment->class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->form_level }} {{ $class->name }} ({{ $class->academic_session }})
                        </option>
                        @endforeach
                    </select>
                    @error('class_id')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label @error('subject_id') text-danger @enderror">Subject*</label>
                    <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid border-danger @enderror"
                        aria-label="Select subject" title="Select subject">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $assessment->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }})
                        </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label @error('start_date') text-danger @enderror">Start Date & Time*</label>
                    <input type="datetime-local" id="start_date" name="start_date"
                        class="form-control @error('start_date') is-invalid border-danger @enderror"
                        value="{{ old('start_date', $assessment->start_date ? $assessment->start_date->format('Y-m-d\TH:i') : '') }}" min="{{ date('Y-m-d\TH:i') }}" aria-label="Start date and time">
                    @error('start_date')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label @error('end_date') text-danger @enderror">End Date & Time*</label>
                    <input type="datetime-local" id="end_date" name="end_date"
                        class="form-control @error('end_date') is-invalid border-danger @enderror"
                        value="{{ old('end_date', $assessment->end_date ? $assessment->end_date->format('Y-m-d\TH:i') : '') }}" min="{{ date('Y-m-d\TH:i') }}" aria-label="End date and time">
                    @error('end_date')
                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{
                            old('is_published', $assessment->is_published) ? 'checked' : '' }}
                            title="Publish assessment" aria-label="Publish assessment">
                        <label class="form-check-label" for="is_published">
                            Publish Assessment (make visible to students)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of card-body -->

        <!-- Quiz Questions Section -->
        @if($assessment->type === 'quiz')
        <hr class="my-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Quiz Questions</h6>
            </div>
            <div class="card-body">
                <div id="questionsContainer">
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
                        $correctAnswer = $question->correct_answer;
                        // Debug: output the correct answer value
                        // For multiple choice, correct_answer is the index (0, 1, 2, etc.)
                        if ($question->question_type === 'checkboxes' && !empty($correctAnswer)) {
                            $correctAnswersArray = is_array($correctAnswer) ? $correctAnswer : explode(',', $correctAnswer);
                        } else {
                            $correctAnswersArray = [];
                        }
                        
                        // Ensure correctAnswer is properly formatted as string for comparison
                        if ($correctAnswer !== null && $question->question_type === 'multiple_choice') {
                            $correctAnswer = (string)$correctAnswer;
                        }
                    @endphp
                    <div class="question-item card mb-3" data-question-index="{{ $index }}" data-question-id="{{ $question->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-question" 
                                    data-question-id="{{ $question->id }}"
                                    aria-label="Remove question" title="Remove question">
                                    <span data-feather="trash-2"></span> Remove
                                </button>
                            </div>
                            <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label @error('questions.' . $index . '.question_type') text-danger @enderror">Question Type*</label>
                                    <select name="questions[{{ $index }}][question_type]" class="form-select question-type-selector @error('questions.' . $index . '.question_type') is-invalid border-danger @enderror" required aria-label="Question type">
                                        <option value="multiple_choice" {{ old('questions.' . $index . '.question_type', $question->question_type) === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="checkboxes" {{ old('questions.' . $index . '.question_type', $question->question_type) === 'checkboxes' ? 'selected' : '' }}>Checkboxes (Multiple Answers)</option>
                                        <option value="short_answer" {{ old('questions.' . $index . '.question_type', $question->question_type) === 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                    </select>
                                    @error('questions.' . $index . '.question_type')
                                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label @error('questions.' . $index . '.marks') text-danger @enderror">Marks*</label>
                                    <input type="number" name="questions[{{ $index }}][marks]" class="form-control @error('questions.' . $index . '.marks') is-invalid border-danger @enderror" step="0.01" min="0" 
                                        value="{{ old('questions.' . $index . '.marks', $question->marks) }}" required aria-label="Question marks" placeholder="Enter marks">
                                    @error('questions.' . $index . '.marks')
                                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label @error('questions.' . $index . '.question') text-danger @enderror">Question*</label>
                                <textarea name="questions[{{ $index }}][question]" class="form-control @error('questions.' . $index . '.question') is-invalid border-danger @enderror" rows="3" required placeholder="Enter your question" aria-label="Question text">{{ old('questions.' . $index . '.question', $question->question) }}</textarea>
                                @error('questions.' . $index . '.question')
                                <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- Options Section (for multiple_choice and checkboxes) -->
                            <div class="options-section" id="options-section-{{ $index }}" style="display: {{ in_array($question->question_type, ['multiple_choice', 'checkboxes']) ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Options*</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-option-btn" data-question-index="{{ $index }}">
                                            <span data-feather="plus"></span> Add Option
                                        </button>
                                    </div>
                                    <div id="options-list-{{ $index }}">
                                        @foreach($options as $optIndex => $optText)
                                        <div class="option-row mb-2" data-option-index="{{ $optIndex }}">
                                            <div class="input-group">
                                                <span class="input-group-text option-label">Option {{ $optIndex + 1 }}</span>
                                                <input type="text" name="questions[{{ $index }}][options][]" class="form-control option-field" 
                                                    value="{{ old('questions.' . $index . '.options.' . $optIndex, $optText) }}" 
                                                    {{ $optIndex < 2 ? 'required' : '' }} placeholder="Enter option text" aria-label="Option input" data-question-index="{{ $index }}">
                                                <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: {{ count($options) > 2 ? 'block' : 'none' }};" aria-label="Remove option" title="Remove option">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">At least 2 options required</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="hidden" name="questions[{{ $index }}][shuffle_options]" value="0">
                                        <input type="checkbox" class="form-check-input" name="questions[{{ $index }}][shuffle_options]" value="1" 
                                            id="shuffle-{{ $index }}" {{ old('questions.' . $index . '.shuffle_options', $question->shuffle_options) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle-{{ $index }}">
                                            Shuffle Answer Options
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Correct Answer Section - Multiple Choice -->
                            <div class="correct-answer-section" id="correct-answer-mc-{{ $index }}" style="display: {{ $question->question_type === 'multiple_choice' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label @error('questions.' . $index . '.correct_answer') text-danger @enderror">Correct Answer*</label>
                                    @php
                                        // Get the correct answer value - handle both old input and database value
                                        $oldCorrectAnswer = old('questions.' . $index . '.correct_answer', $correctAnswer);
                                        // Ensure it's a string for comparison
                                        $correctAnswerValue = $oldCorrectAnswer !== null ? (string)$oldCorrectAnswer : '';
                                    @endphp
                                    <select name="questions[{{ $index }}][correct_answer]" class="form-select correct-answer-mc @error('questions.' . $index . '.correct_answer') is-invalid border-danger @enderror" 
                                        id="correct-answer-select-{{ $index }}" 
                                        data-initial-value="{{ $correctAnswerValue }}"
                                        {{ $question->question_type === 'multiple_choice' ? 'required' : '' }} aria-label="Select correct answer">
                                        <option value="">Select Answer</option>
                                        @foreach($options as $optIdx => $optTxt)
                                        @php
                                            // Compare both as strings - use === for strict comparison
                                            $optIdxStr = (string)$optIdx;
                                            $isSelected = ($correctAnswerValue !== '' && $correctAnswerValue === $optIdxStr);
                                        @endphp
                                        <option value="{{ $optIdx }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $optTxt }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('questions.' . $index . '.correct_answer')
                                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Correct Answer Section - Checkboxes -->
                            <div class="correct-answer-section" id="correct-answer-cb-{{ $index }}" style="display: {{ $question->question_type === 'checkboxes' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label @error('questions.' . $index . '.correct_answers') text-danger @enderror">Correct Answer(s)* (Select all that apply)</label>
                                    <div id="correct-answers-checkboxes-{{ $index }}" data-initial-values="{{ implode(',', array_map('strval', $correctAnswersArray)) }}">
                                        @foreach($options as $optIdx => $optTxt)
                                        @php
                                            $correctAnswersStrArray = array_map('strval', $correctAnswersArray);
                                            $isChecked = in_array((string)$optIdx, $correctAnswersStrArray);
                                        @endphp
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input correct-answer-cb" 
                                                name="questions[{{ $index }}][correct_answers][]" 
                                                value="{{ $optIdx }}" 
                                                id="cb-{{ $index }}-{{ $optIdx }}"
                                                data-required-group="{{ $index }}"
                                                {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cb-{{ $index }}-{{ $optIdx }}">{{ $optTxt }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('questions.' . $index . '.correct_answers')
                                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Correct Answer Section - Short Answer -->
                            <div class="correct-answer-section" id="correct-answer-sa-{{ $index }}" style="display: {{ $question->question_type === 'short_answer' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label @error('questions.' . $index . '.correct_answer') text-danger @enderror">Correct Answer*</label>
                                    <input type="text" name="questions[{{ $index }}][correct_answer]" class="form-control correct-answer-sa @error('questions.' . $index . '.correct_answer') is-invalid border-danger @enderror" 
                                        value="{{ old('questions.' . $index . '.correct_answer', $correctAnswer) }}" 
                                        {{ $question->question_type === 'short_answer' ? 'required' : '' }} placeholder="Enter the correct answer" aria-label="Correct answer">
                                    @error('questions.' . $index . '.correct_answer')
                                    <span class="invalid-feedback d-block" style="color: #dc3545 !important; font-size: 0.875em;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="question-item card mb-3" data-question-index="0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Question 1</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-question" style="display: none;" aria-label="Remove question" title="Remove question">
                                    <span data-feather="trash-2"></span> Remove
                                </button>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Question Type*</label>
                                    <select name="questions[0][question_type]" class="form-select question-type-selector" required aria-label="Question type">
                                        <option value="multiple_choice" selected>Multiple Choice</option>
                                        <option value="checkboxes">Checkboxes (Multiple Answers)</option>
                                        <option value="short_answer">Short Answer</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marks*</label>
                                    <input type="number" name="questions[0][marks]" class="form-control" step="0.01" min="0" value="1" required aria-label="Question marks" placeholder="Enter marks">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Question*</label>
                                <textarea name="questions[0][question]" class="form-control" rows="3" required placeholder="Enter your question" aria-label="Question text"></textarea>
                            </div>
                            <!-- Options Section -->
                            <div class="options-section" id="options-section-0">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Options*</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-option-btn" data-question-index="0">
                                            <span data-feather="plus"></span> Add Option
                                        </button>
                                    </div>
                                    <div id="options-list-0">
                                        <div class="option-row mb-2" data-option-index="0">
                                            <div class="input-group">
                                                <span class="input-group-text option-label">Option 1</span>
                                                <input type="text" name="questions[0][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="0">
                                                <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="option-row mb-2" data-option-index="1">
                                            <div class="input-group">
                                                <span class="input-group-text option-label">Option 2</span>
                                                <input type="text" name="questions[0][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="0">
                                                <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">At least 2 options required</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="hidden" name="questions[0][shuffle_options]" value="0">
                                        <input type="checkbox" class="form-check-input" name="questions[0][shuffle_options]" value="1" id="shuffle-0">
                                        <label class="form-check-label" for="shuffle-0">
                                            Shuffle Answer Options
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Correct Answer - Multiple Choice -->
                            <div class="correct-answer-section" id="correct-answer-mc-0">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer*</label>
                                    <select name="questions[0][correct_answer]" class="form-select correct-answer-mc" id="correct-answer-select-0" required aria-label="Select correct answer">
                                        <option value="">Select Answer</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Correct Answer - Checkboxes -->
                            <div class="correct-answer-section" id="correct-answer-cb-0" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer(s)* (Select all that apply)</label>
                                    <div id="correct-answers-checkboxes-0">
                                        <!-- Dynamic checkboxes will be added here -->
                                    </div>
                                </div>
                            </div>
                            <!-- Correct Answer - Short Answer -->
                            <div class="correct-answer-section" id="correct-answer-sa-0" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer*</label>
                                    <input type="text" name="questions[0][correct_answer]" class="form-control correct-answer-sa" placeholder="Enter the correct answer" aria-label="Correct answer">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addQuestion">
                    <span data-feather="plus"></span> Add Another Question
                </button>
            </div>
        </div>
        @endif

        <!-- Materials Section for Test/Homework -->
        @if(in_array($assessment->type, ['test', 'homework']))
        <hr class="my-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Materials</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal"
                    title="Upload Material" aria-label="Upload Material">
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
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info"
                                    title="Download material" aria-label="Download material">
                                    <span data-feather="download"></span> Download
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-material-btn"
                                    data-material-id="{{ $material->id }}"
                                    data-assessment-id="{{ $assessment->id }}"
                                    title="Delete material" aria-label="Delete material">
                                    <span data-feather="trash-2"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No materials uploaded yet. Click "Upload Material" to add materials for this assessment.</p>
                @endforelse
            </div>
        </div>
        @endif

        <div class="card-footer">
            <button type="submit" class="btn btn-success" title="Update Assessment" aria-label="Update Assessment">
                <span data-feather="save"></span> Update Assessment
            </button>
            <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-outline-secondary" title="Cancel and go back" aria-label="Cancel editing">Cancel</a>
        </div>
    </form>
    
    <!-- Add Question Modal (outside main form to avoid nested forms) -->
    @if($assessment->type === 'quiz')
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('assessments.questions.store', $assessment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="question" class="form-label">Question*</label>
                            <textarea id="question" name="question" class="form-control" rows="3" required
                                placeholder="Enter your question" aria-label="Question text" title="Question text"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="option_a" class="form-label">Option A*</label>
                                <input type="text" id="option_a" name="option_a" class="form-control" required
                                    placeholder="Enter option A" aria-label="Option A" title="Option A">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_b" class="form-label">Option B*</label>
                                <input type="text" id="option_b" name="option_b" class="form-control" required
                                    placeholder="Enter option B" aria-label="Option B" title="Option B">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="option_c" class="form-label">Option C*</label>
                                <input type="text" id="option_c" name="option_c" class="form-control" required
                                    placeholder="Enter option C" aria-label="Option C" title="Option C">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_d" class="form-label">Option D*</label>
                                <input type="text" id="option_d" name="option_d" class="form-control" required
                                    placeholder="Enter option D" aria-label="Option D" title="Option D">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer*</label>
                                <select id="correct_answer" name="correct_answer" class="form-select" required
                                    aria-label="Correct answer" title="Select correct answer">
                                    <option value="">Select Answer</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="marks" class="form-label">Marks*</label>
                                <input type="number" id="marks" name="marks" step="0.01" min="0" class="form-control" value="1" required
                                    placeholder="Enter marks" aria-label="Question marks" title="Marks for this question">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cancel" aria-label="Cancel">Cancel</button>
                        <button type="submit" class="btn btn-primary" title="Add Question" aria-label="Add Question">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Upload Material Modal (outside main form to avoid nested forms) -->
    @if(in_array($assessment->type, ['test', 'homework']))
    <div class="modal fade" id="uploadMaterialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('assessments.materials.store', $assessment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">File*</label>
                            <input type="file" id="file" name="file" class="form-control" required
                                aria-label="Upload file" title="Select file to upload">
                            <small class="text-muted">Max file size: 10MB</small>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3"
                                placeholder="Enter material description (optional)" aria-label="Material description" title="Material description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cancel" aria-label="Cancel">Cancel</button>
                        <button type="submit" class="btn btn-primary" title="Upload Material" aria-label="Upload Material">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
<script>
    if (typeof feather !== 'undefined') feather.replace();
    
    document.addEventListener('DOMContentLoaded', function() {
        let questionIndex = {{ $assessment->questions->count() }};
        let isInitialLoad = true; // Flag to prevent updates on initial load
        
        // Function to update correct answer options based on current options
        function updateCorrectAnswerOptions(questionIndex) {
            // Don't update on initial page load - server already set the values
            if (isInitialLoad) {
                return;
            }
            const questionItem = document.querySelector(`[data-question-index="${questionIndex}"]`);
            if (!questionItem) return;
            
            const questionType = questionItem.querySelector('.question-type-selector')?.value || 'multiple_choice';
            const optionsList = document.getElementById('options-list-' + questionIndex);
            if (!optionsList) return;
            
            const optionRows = optionsList.querySelectorAll('.option-row');
            const optionFields = optionsList.querySelectorAll('.option-field');
            const mcSelect = questionItem.querySelector('#correct-answer-select-' + questionIndex);
            const cbContainer = questionItem.querySelector('#correct-answers-checkboxes-' + questionIndex);
            
            if (questionType === 'multiple_choice' && mcSelect) {
                // Preserve the currently selected value - use data attribute if available (initial load), otherwise use current value
                const currentSelectedValue = mcSelect.dataset.initialValue || mcSelect.value;
                
                mcSelect.innerHTML = '<option value="">Select Answer</option>';
                optionRows.forEach((row, index) => {
                    const field = row.querySelector('.option-field');
                    const option = document.createElement('option');
                    option.value = index;
                    const optionText = field && field.value.trim() ? field.value.trim() : `Option ${index + 1}`;
                    option.textContent = optionText.length > 50 ? optionText.substring(0, 50) + '...' : optionText;
                    mcSelect.appendChild(option);
                });
                
                // Restore the selected value if it still exists
                if (currentSelectedValue && currentSelectedValue !== '' && mcSelect.querySelector(`option[value="${currentSelectedValue}"]`)) {
                    mcSelect.value = currentSelectedValue;
                }
                // Clear the data attribute after first use
                if (mcSelect.dataset.initialValue) {
                    delete mcSelect.dataset.initialValue;
                }
            } else if (questionType === 'checkboxes' && cbContainer) {
                // Preserve currently checked values - use data attribute if available (initial load), otherwise use current checked boxes
                let checkedValues = [];
                if (cbContainer.dataset.initialValues) {
                    checkedValues = cbContainer.dataset.initialValues.split(',').filter(v => v !== '');
                    // Clear the data attribute after first use
                    delete cbContainer.dataset.initialValues;
                } else {
                    checkedValues = Array.from(cbContainer.querySelectorAll('.correct-answer-cb:checked')).map(cb => cb.value);
                }
                
                cbContainer.innerHTML = '';
                optionRows.forEach((row, index) => {
                    const field = row.querySelector('.option-field');
                    const checkboxId = `cb-${questionIndex}-${index}`;
                    const checkboxDiv = document.createElement('div');
                    checkboxDiv.className = 'form-check';
                    const optionText = field && field.value.trim() ? field.value.trim() : `Option ${index + 1}`;
                    const isChecked = checkedValues.includes(String(index));
                    checkboxDiv.innerHTML = `
                        <input type="checkbox" class="form-check-input correct-answer-cb" 
                               name="questions[${questionIndex}][correct_answers][]" 
                               value="${index}" 
                               id="${checkboxId}"
                               data-required-group="${questionIndex}"
                               ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="${checkboxId}">${optionText.length > 50 ? optionText.substring(0, 50) + '...' : optionText}</label>
                    `;
                    cbContainer.appendChild(checkboxDiv);
                });
            }
        }
        
        // Function to handle question type change
        function handleQuestionTypeChange(selectElement) {
            const questionItem = selectElement.closest('.question-item');
            const questionIndex = questionItem.dataset.questionIndex;
            const questionType = selectElement.value;
            
            const optionsSection = questionItem.querySelector('.options-section');
            const shuffleCheckbox = questionItem.querySelector('input[name*="[shuffle_options]"]');
            const mcSection = questionItem.querySelector('#correct-answer-mc-' + questionIndex);
            const cbSection = questionItem.querySelector('#correct-answer-cb-' + questionIndex);
            const saSection = questionItem.querySelector('#correct-answer-sa-' + questionIndex);
            const optionFields = questionItem.querySelectorAll('.option-field');
            const mcSelect = questionItem.querySelector('.correct-answer-mc');
            const saInput = questionItem.querySelector('.correct-answer-sa');
            
            if (questionType === 'short_answer') {
                optionsSection.style.display = 'none';
                shuffleCheckbox.closest('.form-check').style.display = 'none';
                mcSection.style.display = 'none';
                cbSection.style.display = 'none';
                saSection.style.display = 'block';
                optionFields.forEach(field => { field.removeAttribute('required'); });
                if (mcSelect) { mcSelect.removeAttribute('required'); mcSelect.value = ''; }
                if (saInput) saInput.setAttribute('required', 'required');
            } else if (questionType === 'checkboxes') {
                optionsSection.style.display = 'block';
                shuffleCheckbox.closest('.form-check').style.display = 'block';
                mcSection.style.display = 'none';
                saSection.style.display = 'none';
                cbSection.style.display = 'block';
                const optionFieldsInSection = questionItem.querySelectorAll('#options-list-' + questionIndex + ' .option-field');
                optionFieldsInSection.forEach(field => field.setAttribute('required', 'required'));
                if (mcSelect) { mcSelect.removeAttribute('required'); mcSelect.value = ''; }
                if (saInput) { saInput.removeAttribute('required'); saInput.value = ''; }
                setTimeout(function() {
                    updateCorrectAnswerOptions(questionIndex);
                }, 50);
            } else {
                optionsSection.style.display = 'block';
                shuffleCheckbox.closest('.form-check').style.display = 'block';
                mcSection.style.display = 'block';
                cbSection.style.display = 'none';
                saSection.style.display = 'none';
                optionFields.forEach(field => field.setAttribute('required', 'required'));
                if (mcSelect) mcSelect.setAttribute('required', 'required');
                if (saInput) { saInput.removeAttribute('required'); saInput.value = ''; }
                updateCorrectAnswerOptions(questionIndex);
            }
        }
        
        // Function to add an option
        function addOption(questionIndex) {
            const optionsList = document.getElementById('options-list-' + questionIndex);
            if (!optionsList) return;
            
            const optionRows = optionsList.querySelectorAll('.option-row');
            const optionIndex = optionRows.length;
            
            const optionRow = document.createElement('div');
            optionRow.className = 'option-row mb-2';
            optionRow.dataset.optionIndex = optionIndex;
            optionRow.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text option-label">Option ${optionIndex + 1}</span>
                    <input type="text" name="questions[${questionIndex}][options][]" 
                           class="form-control option-field" 
                           required 
                           placeholder="Enter option text"
                           aria-label="Option input"
                           data-question-index="${questionIndex}">
                    <button type="button" class="btn btn-outline-danger remove-option-btn" aria-label="Remove option" title="Remove option">
                        <span data-feather="trash-2"></span>
                    </button>
                </div>
            `;
            
            optionsList.appendChild(optionRow);
            
            if (optionRows.length + 1 > 2) {
                optionsList.querySelectorAll('.remove-option-btn').forEach(btn => btn.style.display = 'block');
            }
            
            updateCorrectAnswerOptions(questionIndex);
            
            const newInput = optionRow.querySelector('.option-field');
            newInput.addEventListener('input', function() {
                updateCorrectAnswerOptions(questionIndex);
                updateOptionLabels(questionIndex);
            });
            
            if (typeof feather !== 'undefined') feather.replace();
        }
        
        // Function to remove an option
        function removeOption(button) {
            const optionRow = button.closest('.option-row');
            const questionItem = optionRow.closest('.question-item');
            const questionIndex = questionItem.dataset.questionIndex;
            const optionsList = document.getElementById('options-list-' + questionIndex);
            
            optionRow.remove();
            
            updateOptionLabels(questionIndex);
            updateCorrectAnswerOptions(questionIndex);
            
            const remainingOptions = optionsList.querySelectorAll('.option-row');
            if (remainingOptions.length <= 2) {
                optionsList.querySelectorAll('.remove-option-btn').forEach(btn => btn.style.display = 'none');
            }
        }
        
        // Function to update option labels
        function updateOptionLabels(questionIndex) {
            const optionsList = document.getElementById('options-list-' + questionIndex);
            if (!optionsList) return;
            
            const optionRows = optionsList.querySelectorAll('.option-row');
            optionRows.forEach((row, index) => {
                const label = row.querySelector('.option-label');
                if (label) label.textContent = `Option ${index + 1}`;
            });
        }
        
        // Initialize option event listeners on page load
        function initializeQuestionOptions(questionIndex) {
            const questionItem = document.querySelector(`[data-question-index="${questionIndex}"]`);
            if (!questionItem) return;
            
            const optionsList = document.getElementById('options-list-' + questionIndex);
            if (!optionsList) return;
            
            optionsList.querySelectorAll('.option-field').forEach(field => {
                field.addEventListener('input', function() {
                    updateCorrectAnswerOptions(questionIndex);
                    updateOptionLabels(questionIndex);
                });
            });
            
            // DO NOT call updateCorrectAnswerOptions on initial load
            // The server already populated the correct answers, and calling this would clear them
            // Only update when options are changed dynamically by the user
        }
        
        // Initialize all existing questions - but don't rebuild dropdowns on initial load
        // Only add event listeners, skip the updateCorrectAnswerOptions call
        document.querySelectorAll('.question-item').forEach((item, index) => {
            const questionIndex = item.dataset.questionIndex;
            const optionsList = document.getElementById('options-list-' + questionIndex);
            
            if (!optionsList) return;
            
            // Just add event listeners for option changes, don't rebuild dropdowns
            // This preserves the server-set correct answer values
            optionsList.querySelectorAll('.option-field').forEach(field => {
                field.addEventListener('input', function() {
                    // Only update when user actually changes an option
                    updateCorrectAnswerOptions(questionIndex);
                    updateOptionLabels(questionIndex);
                });
            });
        });
        
        // Set flag to false after initialization is complete
        // This allows updates when user makes changes after page load
        setTimeout(function() {
            isInitialLoad = false;
        }, 500);
        
        
        // Function to calculate and display sum of question marks
        function updateTotalMarksValidation() {
            const totalMarksInput = document.getElementById('total_marks');
            const questionsContainer = document.getElementById('questionsContainer');
            
            if (!totalMarksInput || !questionsContainer) return;
            
            let sumOfMarks = 0;
            // Only count visible questions (not marked for deletion)
            const visibleQuestions = questionsContainer.querySelectorAll('.question-item:not([style*="display: none"])');
            
            visibleQuestions.forEach(questionItem => {
                const markInput = questionItem.querySelector('input[name*="[marks]"]');
                if (markInput) {
                    const value = parseFloat(markInput.value) || 0;
                    sumOfMarks += value;
                }
            });
            
            // Round to 2 decimal places
            sumOfMarks = Math.round(sumOfMarks * 100) / 100;
            const totalMarks = parseFloat(totalMarksInput.value) || 0;
            
            // Remove existing validation message
            const existingMsg = document.getElementById('total-marks-validation');
            if (existingMsg) {
                existingMsg.remove();
            }
            
            // Show validation message if they don't match
            if (totalMarks > 0 && Math.abs(sumOfMarks - totalMarks) > 0.01) {
                const validationMsg = document.createElement('div');
                validationMsg.id = 'total-marks-validation';
                validationMsg.className = 'text-danger mt-1';
                validationMsg.style.fontSize = '0.875em';
                validationMsg.textContent = `Warning: Total marks (${totalMarks}) does not match sum of question marks (${sumOfMarks.toFixed(2)}).`;
                
                // Insert after total_marks input
                totalMarksInput.parentElement.appendChild(validationMsg);
                
                // Add border-danger class
                totalMarksInput.classList.add('border-danger');
            } else {
                // Remove border-danger if they match
                totalMarksInput.classList.remove('border-danger');
            }
        }
        
        // Add event listeners for total marks and question marks
        const totalMarksInput = document.getElementById('total_marks');
        if (totalMarksInput) {
            totalMarksInput.addEventListener('input', updateTotalMarksValidation);
            totalMarksInput.addEventListener('blur', updateTotalMarksValidation);
        }
        
        // Monitor question marks changes
        document.addEventListener('input', function(e) {
            if (e.target && e.target.name && e.target.name.includes('[marks]')) {
                updateTotalMarksValidation();
            }
        });
        
        // Update validation when questions are added/removed
        const originalRemoveQuestion = document.querySelectorAll('.remove-question');
        
        // Initial validation check
        setTimeout(updateTotalMarksValidation, 500);
        
        // Add Question
        // Add event listeners for question type selectors after initial load
        setTimeout(function() {
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('question-type-selector')) {
                    handleQuestionTypeChange(e.target);
                }
            });
        }, 600);
        
        // Add event listeners for add/remove option buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-option-btn')) {
                const btn = e.target.closest('.add-option-btn');
                const questionIndex = btn.dataset.questionIndex;
                addOption(questionIndex);
            }
            
            if (e.target.closest('.remove-option-btn')) {
                const btn = e.target.closest('.remove-option-btn');
                const questionItem = btn.closest('.question-item');
                const questionIndex = questionItem.dataset.questionIndex;
                const optionsList = document.getElementById('options-list-' + questionIndex);
                if (optionsList) {
                    const remainingOptions = optionsList.querySelectorAll('.option-row');
                    if (remainingOptions.length > 2) {
                        removeOption(btn);
                    } else {
                        alert('At least 2 options are required.');
                    }
                }
            }
        });
        
        // Function to get the next available question index
        function getNextQuestionIndex() {
            const container = document.getElementById('questionsContainer');
            if (!container) return 0;
            
            // Get all existing question items (including hidden ones that are marked for deletion)
            const allQuestions = container.querySelectorAll('.question-item');
            if (allQuestions.length === 0) return 0;
            
            // Find the highest index currently in use
            let maxIndex = -1;
            allQuestions.forEach(item => {
                const index = parseInt(item.dataset.questionIndex) || 0;
                if (index > maxIndex) {
                    maxIndex = index;
                }
            });
            
            // Return the next index (highest + 1)
            return maxIndex + 1;
        }
        
        // Add Question
        const addQuestionBtn = document.getElementById('addQuestion');
        if (addQuestionBtn) {
            addQuestionBtn.addEventListener('click', function() {
                const container = document.getElementById('questionsContainer');
                // Calculate the next available index based on existing questions
                const nextIndex = getNextQuestionIndex();
                const questionHtml = `
                    <div class="question-item card mb-3" data-question-index="${nextIndex}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Question ${nextIndex + 1}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-question" aria-label="Remove question" title="Remove question">
                                    <span data-feather="trash-2"></span> Remove
                                </button>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Question Type*</label>
                                    <select name="questions[${nextIndex}][question_type]" class="form-select question-type-selector" required aria-label="Question type">
                                        <option value="multiple_choice" selected>Multiple Choice</option>
                                        <option value="checkboxes">Checkboxes (Multiple Answers)</option>
                                        <option value="short_answer">Short Answer</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marks*</label>
                                    <input type="number" name="questions[${nextIndex}][marks]" class="form-control" step="0.01" min="0" value="1" required aria-label="Question marks" placeholder="Enter marks">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Question*</label>
                                <textarea name="questions[${nextIndex}][question]" class="form-control" rows="3" required placeholder="Enter your question" aria-label="Question text"></textarea>
                            </div>
                            <div class="options-section" id="options-section-${nextIndex}">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Options*</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary add-option-btn" data-question-index="${nextIndex}">
                                            <span data-feather="plus"></span> Add Option
                                        </button>
                                    </div>
                                    <div id="options-list-${nextIndex}">
                                        <div class="option-row mb-2" data-option-index="0">
                                            <div class="input-group">
                                                <span class="input-group-text option-label">Option 1</span>
                                                <input type="text" name="questions[${nextIndex}][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="${nextIndex}">
                                                <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="option-row mb-2" data-option-index="1">
                                            <div class="input-group">
                                                <span class="input-group-text option-label">Option 2</span>
                                                <input type="text" name="questions[${nextIndex}][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="${nextIndex}">
                                                <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                                    <span data-feather="trash-2"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">At least 2 options required</small>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="hidden" name="questions[${nextIndex}][shuffle_options]" value="0">
                                        <input type="checkbox" class="form-check-input" name="questions[${nextIndex}][shuffle_options]" value="1" id="shuffle-${nextIndex}">
                                        <label class="form-check-label" for="shuffle-${nextIndex}">
                                            Shuffle Answer Options
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="correct-answer-section" id="correct-answer-mc-${nextIndex}">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer*</label>
                                    <select name="questions[${nextIndex}][correct_answer]" class="form-select correct-answer-mc" id="correct-answer-select-${nextIndex}" required aria-label="Select correct answer">
                                        <option value="">Select Answer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="correct-answer-section" id="correct-answer-cb-${nextIndex}" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer(s)* (Select all that apply)</label>
                                    <div id="correct-answers-checkboxes-${nextIndex}">
                                    </div>
                                </div>
                            </div>
                            <div class="correct-answer-section" id="correct-answer-sa-${nextIndex}" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer*</label>
                                    <input type="text" name="questions[${nextIndex}][correct_answer]" class="form-control correct-answer-sa" placeholder="Enter the correct answer" aria-label="Correct answer">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', questionHtml);
                
                initializeQuestionOptions(nextIndex);
                
                // Update question numbers for all questions to ensure sequential numbering
                updateQuestionNumbers();
                
                if (container.children.length > 1) {
                    document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'block');
                }
                
                if (typeof feather !== 'undefined') feather.replace();
            });
        }
        
        // Function to update question numbers sequentially
        function updateQuestionNumbers() {
            const container = document.getElementById('questionsContainer');
            if (!container) return;
            
            // Get all visible question items (excluding hidden ones marked for deletion)
            const visibleQuestions = Array.from(container.querySelectorAll('.question-item')).filter(item => 
                item.style.display !== 'none'
            );
            
            // Update question numbers sequentially
            visibleQuestions.forEach((item, index) => {
                const h6 = item.querySelector('h6');
                if (h6) {
                    h6.textContent = `Question ${index + 1}`;
                }
            });
        }
        
        // Remove Question
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-question')) {
                const questionItem = e.target.closest('.question-item');
                const questionId = questionItem.dataset.questionId;
                
                if (questionId) {
                    // Existing question - add hidden input to mark for deletion
                    if (confirm('Remove this question from the form? (It will be deleted when you save the assessment)')) {
                        questionItem.style.display = 'none';
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'questions_to_delete[]';
                        hiddenInput.value = questionId;
                        document.getElementById('editAssessmentForm').appendChild(hiddenInput);
                        
                        // Update question numbers after hiding
                        updateQuestionNumbers();
                    }
                } else {
                    // New question - just remove it
                    questionItem.remove();
                    
                    // Update question numbers after removal
                    updateQuestionNumbers();
                    
                    const container = document.getElementById('questionsContainer');
                    if (container.children.length <= 1) {
                        document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'none');
                    }
                }
                
                // Update total marks validation after removing question
                setTimeout(updateTotalMarksValidation, 100);
            }
        });
        
        // Delete material buttons
        document.querySelectorAll('.delete-material-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Delete this material?')) return;
                
                const materialId = this.getAttribute('data-material-id');
                const assessmentId = this.getAttribute('data-assessment-id');
                
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.getElementById('editAssessmentForm')?.querySelector('input[name="_token"]')?.value;
                
                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.action = `/assessments/${assessmentId}/materials/${materialId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                deleteForm.appendChild(methodInput);
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = token;
                deleteForm.appendChild(tokenInput);
                
                document.body.appendChild(deleteForm);
                deleteForm.submit();
            });
        });
        
        // Show remove buttons if more than one question
        const container = document.getElementById('questionsContainer');
        if (container && container.children.length > 1) {
            document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'block');
        }
        
        // Form validation for checkbox questions
        const editForm = document.getElementById('editAssessmentForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const checkboxGroups = {};
                // Only get checkboxes from visible questions that are actually checkbox type
                const questionItems = document.querySelectorAll('.question-item:not([style*="display: none"])');
                
                questionItems.forEach(questionItem => {
                    const questionTypeSelector = questionItem.querySelector('.question-type-selector');
                    if (questionTypeSelector && questionTypeSelector.value === 'checkboxes') {
                        const questionIndex = questionItem.dataset.questionIndex;
                        const checkboxes = questionItem.querySelectorAll('.correct-answer-cb[data-required-group="' + questionIndex + '"]');
                        
                        if (checkboxes.length > 0) {
                            if (!checkboxGroups[questionIndex]) checkboxGroups[questionIndex] = [];
                            checkboxes.forEach(cb => checkboxGroups[questionIndex].push(cb));
                        }
                    }
                });
                
                // Validate each checkbox question group
                for (const groupId in checkboxGroups) {
                    const checked = checkboxGroups[groupId].some(cb => cb.checked);
                    if (!checked) {
                        e.preventDefault();
                        alert('Please select at least one correct answer for checkbox questions.');
                        return false;
                    }
                }
            });
        }
    });

    // Show errors on page load - errors should always be visible after form submission
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            // Errors exist, so form was submitted - show them and scroll to first error
            const errorAlert = document.querySelector('.alert-danger');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const firstInvalidField = document.querySelector('.is-invalid, .border-danger');
                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                    }
                }, 100);
            }
        @endif
    });
</script>
@endsection
