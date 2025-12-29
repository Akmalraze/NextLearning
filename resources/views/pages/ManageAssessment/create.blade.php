@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Create Assessment</h5>
        <a href="{{ route('assessments.index', ['class_id' => $preSelectedClassId ?? '', 'subject_id' => $preSelectedSubjectId ?? '']) }}" class="btn btn-secondary btn-sm">
            <span data-feather="arrow-left"></span> Back
        </a>
    </div>
    <form action="{{ route('assessments.store') }}" method="POST" enctype="multipart/form-data" id="assessmentForm">
        @csrf
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong class="text-danger">Please fix the following errors (in order from top to bottom):</strong>
                <ul class="mb-0 mt-2">
                    @php
                        // Define field order as they appear in the form (top to bottom)
                        // This order MUST match the form field order
                        $fieldOrder = [
                            'type',           // Step 1 - Assessment Type (first)
                            'title',          // Step 2 - Assessment Title (second)
                            'description',    // Step 2 - Description
                            'class_id',       // Step 2 - Class
                            'subject_id',     // Step 2 - Subject
                            'start_date',     // Step 2 - Start Date
                            'end_date',       // Step 2 - End Date
                            'total_marks',    // Step 2 - Total Marks
                            'time_limit',     // Step 2 - Time Limit (quiz)
                            'max_attempts',   // Step 2 - Max Attempts (quiz)
                            'questions',      // Step 2 - Questions (quiz) - handle nested
                            'materials',      // Step 2 - Materials (homework/test) - LAST
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

            <!-- Step 1: Choose Assessment Type -->
            <div id="step1" class="assessment-step">
                <div class="mb-4">
                    <h6 class="mb-3">Step 1: Choose Assessment Type</h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="type" class="form-label @error('type') text-danger @enderror">Assessment Type*</label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid border-danger @enderror">
                                <option value="">Select Type</option>
                                <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="homework" {{ old('type') === 'homework' ? 'selected' : '' }}>Homework</option>
                                <option value="test" {{ old('type') === 'test' ? 'selected' : '' }}>Test</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="nextToStep2" disabled>
                            Next <span data-feather="arrow-right"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Assessment Details -->
            <div id="step2" class="assessment-step" style="display: none;">
                <div class="mb-4">
                    <h6 class="mb-3">Step 2: Assessment Details</h6>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label @error('title') text-danger @enderror">Assessment Title*</label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid border-danger @enderror"
                            value="{{ old('title') }}" aria-label="Assessment title" placeholder="Enter assessment title">
                        @error('title')
                        <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label @error('description') text-danger @enderror">Description</label>
                        <textarea id="description" name="description"
                            class="form-control @error('description') is-invalid border-danger @enderror"
                            rows="4" placeholder="Enter assessment description (optional)" aria-label="Assessment description">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label @error('class_id') text-danger @enderror">Class*</label>
                            <select id="class_id" name="class_id" class="form-select @error('class_id') is-invalid border-danger @enderror">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $preSelectedClassId ?? '') == $class->id ? 'selected' : '' }}>
                                    {{ $class->form_level }} {{ $class->name }} ({{ $class->academic_session }})
                                </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                            @if($classes->isEmpty())
                            <small class="text-danger">No classes assigned. Please contact administrator to assign you to classes.</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label @error('subject_id') text-danger @enderror">Subject*</label>
                            <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid border-danger @enderror">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $preSelectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                            @if($subjects->isEmpty())
                            <small class="text-danger">No subjects assigned. Please contact administrator to assign you to subjects.</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label @error('start_date') text-danger @enderror">Start Date & Time*</label>
                            <input type="datetime-local" id="start_date" name="start_date"
                                class="form-control @error('start_date') is-invalid border-danger @enderror"
                                value="{{ old('start_date') }}" min="{{ date('Y-m-d\TH:i') }}" aria-label="Start date and time">
                            @error('start_date')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label @error('end_date') text-danger @enderror">End Date & Time*</label>
                            <input type="datetime-local" id="end_date" name="end_date"
                                class="form-control @error('end_date') is-invalid border-danger @enderror"
                                value="{{ old('end_date') }}" min="{{ date('Y-m-d\TH:i') }}" aria-label="End date and time">
                            @error('end_date')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="total_marks" class="form-label @error('total_marks') text-danger @enderror">Total Marks*</label>
                            <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" max="1000"
                                class="form-control @error('total_marks') is-invalid border-danger @enderror"
                                value="{{ old('total_marks', 100) }}" aria-label="Total marks" placeholder="Enter total marks">
                            @error('total_marks')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="timeLimitField" style="display: none;">
                            <label for="time_limit" class="form-label @error('time_limit') text-danger @enderror">Time Limit (minutes)*</label>
                            <input type="number" id="time_limit" name="time_limit" min="1" step="1"
                                class="form-control @error('time_limit') is-invalid border-danger @enderror"
                                value="{{ old('time_limit') }}" aria-label="Time limit in minutes" placeholder="Enter time limit">
                            @error('time_limit')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Required for quizzes</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check mt-4">
                                <input type="hidden" name="is_published" value="0">
                                <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{
                                    old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publish Assessment (make visible to students)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz-specific options -->
                    <div class="row" id="quizOptionsSection" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="max_attempts" class="form-label">Maximum Attempts</label>
                            <input type="number" id="max_attempts" name="max_attempts" min="1" step="1"
                                class="form-control @error('max_attempts') is-invalid @enderror"
                                value="{{ old('max_attempts') }}" aria-label="Maximum attempts" placeholder="Leave empty for unlimited">
                            @error('max_attempts')
                            <span class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.875em;">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Leave empty for unlimited attempts. System will keep the highest score.</small>
                        </div>
                    </div>
                    
                    <!-- Show Marks option for all assessment types -->
                    <div class="row" id="showMarksSection">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="show_marks" value="0">
                                <input type="checkbox" class="form-check-input" id="show_marks" name="show_marks" value="1" {{
                                    old('show_marks', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_marks">
                                    Show Marks to Students
                                </label>
                            </div>
                            <small class="text-muted">If unchecked, students won't see their scores.</small>
                        </div>
                    </div>

                    <!-- Quiz Questions Section -->
                    <div id="quizSection" style="display: none;">
                        <hr class="my-4">
                        <h6 class="mb-3">Quiz Questions</h6>
                        <div id="questionsContainer">
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
                                    <!-- Options Section (for multiple_choice and checkboxes) -->
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
                                                        <input type="text" name="questions[0][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input">
                                                        <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                                            <span data-feather="trash-2"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="option-row mb-2" data-option-index="1">
                                                    <div class="input-group">
                                                        <span class="input-group-text option-label">Option 2</span>
                                                        <input type="text" name="questions[0][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input">
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
                                    <!-- Correct Answer Section - Multiple Choice -->
                                    <div class="correct-answer-section" id="correct-answer-mc-0">
                                        <div class="mb-3">
                                            <label class="form-label">Correct Answer*</label>
                                            <select name="questions[0][correct_answer]" class="form-select correct-answer-mc" id="correct-answer-select-0" required aria-label="Select correct answer">
                                                <option value="">Select Answer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Correct Answer Section - Checkboxes -->
                                    <div class="correct-answer-section" id="correct-answer-cb-0" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Correct Answer(s)* (Select all that apply)</label>
                                            <div id="correct-answers-checkboxes-0">
                                                <!-- Dynamic checkboxes will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Correct Answer Section - Short Answer -->
                                    <div class="correct-answer-section" id="correct-answer-sa-0" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Correct Answer*</label>
                                            <input type="text" name="questions[0][correct_answer]" class="form-control correct-answer-sa" placeholder="Enter the correct answer" aria-label="Correct answer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addQuestion">
                            <span data-feather="plus"></span> Add Another Question
                        </button>
                    </div>

                    <!-- Materials Section for Homework/Test -->
                    <div id="materialsSection" style="display: none;">
                        <hr class="my-4">
                        <h6 class="mb-3">Upload Materials</h6>
                        <div id="materialsContainer">
                            <div class="material-item card mb-3" data-material-index="0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Material 1</h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-material" style="display: none;" aria-label="Remove material" title="Remove material">
                                            <span data-feather="trash-2"></span> Remove
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">File*</label>
                                        <input type="file" name="materials[0][file]" class="form-control material-file-input" required aria-label="Upload material file">
                                        <small class="text-muted">Max file size: 10MB</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="materials[0][description]" class="form-control" rows="2" aria-label="Material description" placeholder="Enter material description (optional)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addMaterial">
                            <span data-feather="plus"></span> Add Another Material
                        </button>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="backToStep1">
                            <span data-feather="arrow-left"></span> Back
                        </button>
                        <button type="submit" class="btn btn-success" aria-label="Create assessment">
                            <span data-feather="save"></span> Create Assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') feather.replace();

        let questionIndex = 1;
        let materialIndex = 1;

        // Step 1: Type selection
        const typeSelect = document.getElementById('type');
        const nextToStep2Btn = document.getElementById('nextToStep2');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const quizSection = document.getElementById('quizSection');
        const materialsSection = document.getElementById('materialsSection');
        
        // Initial cleanup: Remove required attributes from initially hidden sections
        // This prevents browser validation errors before user selects a type
        if (quizSection && quizSection.style.display === 'none') {
            quizSection.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.hasAttribute('required')) {
                    input.setAttribute('data-required', 'true');
                    input.removeAttribute('required');
                }
                if (input.hasAttribute('name') && input.getAttribute('name').includes('questions')) {
                    input.setAttribute('data-original-name', input.getAttribute('name'));
                    input.removeAttribute('name');
                }
            });
        }
        if (materialsSection && materialsSection.style.display === 'none') {
            materialsSection.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.hasAttribute('required')) {
                    input.setAttribute('data-required', 'true');
                    input.removeAttribute('required');
                }
                if (input.hasAttribute('name') && input.getAttribute('name').includes('materials')) {
                    input.setAttribute('data-original-name', input.getAttribute('name'));
                    input.removeAttribute('name');
                }
            });
        }

        if (typeSelect && nextToStep2Btn) {
            typeSelect.addEventListener('change', function() {
                nextToStep2Btn.disabled = !this.value;
            });
        }

        // Function to handle type change and update form fields
        function handleTypeChange(selectedType) {
            const timeLimitField = document.getElementById('timeLimitField');
            const timeLimitInput = document.getElementById('time_limit');
            const quizOptionsSection = document.getElementById('quizOptionsSection');
            
            if (selectedType === 'quiz') {
                // Show time limit field for quiz
                if (timeLimitField) {
                    timeLimitField.style.display = 'block';
                }
                if (timeLimitInput) {
                    timeLimitInput.setAttribute('required', 'required');
                }
                // Show quiz options section
                if (quizOptionsSection) {
                    quizOptionsSection.style.display = 'flex';
                }
                // Show quiz section, hide materials section
                if (quizSection) {
                    quizSection.style.display = 'block';
                    // Restore name and required attributes for quiz inputs
                    quizSection.querySelectorAll('input, select, textarea').forEach(input => {
                        if (input.hasAttribute('data-original-name')) {
                            input.setAttribute('name', input.getAttribute('data-original-name'));
                            input.removeAttribute('data-original-name');
                        }
                        // Restore required attributes where needed
                        if (input.hasAttribute('data-required')) {
                            input.setAttribute('required', 'required');
                            input.removeAttribute('data-required');
                        }
                    });
                }
                if (materialsSection) {
                    materialsSection.style.display = 'none';
                    // Remove name and required attributes from material inputs so they won't be submitted
                    materialsSection.querySelectorAll('input, select, textarea').forEach(input => {
                        if (input.hasAttribute('required')) {
                            input.setAttribute('data-required', 'true');
                            input.removeAttribute('required');
                        }
                        if (input.hasAttribute('name')) {
                            input.setAttribute('data-original-name', input.getAttribute('name'));
                            input.removeAttribute('name');
                        }
                    });
                }
            } else if (selectedType === 'homework' || selectedType === 'test') {
                // Hide quiz section, show materials section
                if (quizSection) {
                    quizSection.style.display = 'none';
                    // Remove name and required attributes from ALL quiz inputs so they won't be submitted
                    // Use more comprehensive selectors to catch all elements
                    const allQuizInputs = quizSection.querySelectorAll('input, select, textarea, [required]');
                    allQuizInputs.forEach(input => {
                        // Remove required attribute - this is critical!
                        input.removeAttribute('required');
                        input.removeAttribute('data-required');
                        // Remove name attribute so the field won't be submitted
                        if (input.hasAttribute('name') && input.getAttribute('name').includes('questions')) {
                            if (!input.hasAttribute('data-original-name')) {
                                input.setAttribute('data-original-name', input.getAttribute('name'));
                            }
                            input.removeAttribute('name');
                        }
                    });
                }
                // Hide quiz options section
                if (quizOptionsSection) {
                    quizOptionsSection.style.display = 'none';
                }
                if (materialsSection) {
                    materialsSection.style.display = 'block';
                    // Restore name and required attributes for material inputs
                    materialsSection.querySelectorAll('input, select, textarea').forEach(input => {
                        if (input.hasAttribute('data-original-name')) {
                            input.setAttribute('name', input.getAttribute('data-original-name'));
                            input.removeAttribute('data-original-name');
                        }
                        // Restore required attributes where needed (file inputs)
                        if (input.hasAttribute('data-required') || input.classList.contains('material-file-input')) {
                            input.setAttribute('required', 'required');
                            input.removeAttribute('data-required');
                        }
                    });
                }
            }
        }

        if (nextToStep2Btn) {
            nextToStep2Btn.addEventListener('click', function() {
                // Hide error alert when navigating forward from step 1 (starting fresh)
                // This prevents old errors from showing when user starts over
                const errorAlert = document.querySelector('.alert-danger');
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
                
                // Also remove error styling from fields when starting fresh
                document.querySelectorAll('.is-invalid, .border-danger').forEach(function(field) {
                    field.classList.remove('is-invalid', 'border-danger');
                });
                document.querySelectorAll('.form-label.text-danger').forEach(function(label) {
                    label.classList.remove('text-danger');
                });
                // Hide all individual field error messages (force hide even with d-block class)
                document.querySelectorAll('.invalid-feedback').forEach(function(errorMsg) {
                    errorMsg.style.setProperty('display', 'none', 'important');
                    errorMsg.style.visibility = 'hidden';
                });
                
                if (step1) step1.style.display = 'none';
                if (step2) step2.style.display = 'block';
                
                // Show appropriate section based on type
                const selectedType = typeSelect ? typeSelect.value : '';
                handleTypeChange(selectedType);
                
                if (typeof feather !== 'undefined') feather.replace();
            });
        }
        
        // Also handle type change if user changes type after clicking next (though this shouldn't happen)
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                handleTypeChange(selectedType);
            });
        }

        const backToStep1Btn = document.getElementById('backToStep1');
        if (backToStep1Btn) {
            backToStep1Btn.addEventListener('click', function() {
                // Hide error alert when going back to step 1
                const errorAlert = document.querySelector('.alert-danger');
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
                
                // Hide all individual field error messages (they have d-block class, so force hide)
                document.querySelectorAll('.invalid-feedback').forEach(function(errorMsg) {
                    errorMsg.style.setProperty('display', 'none', 'important');
                    errorMsg.style.visibility = 'hidden';
                });
                
                // Remove error styling from all fields
                document.querySelectorAll('.is-invalid, .border-danger').forEach(function(field) {
                    field.classList.remove('is-invalid', 'border-danger');
                });
                
                // Remove red color from labels
                document.querySelectorAll('.form-label.text-danger').forEach(function(label) {
                    label.classList.remove('text-danger');
                });
                
                if (step2) step2.style.display = 'none';
                if (step1) step1.style.display = 'block';
                if (typeof feather !== 'undefined') feather.replace();
            });
        }

        // Function to update correct answer options based on current options
        function updateCorrectAnswerOptions(questionIndex) {
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
                // Clear and rebuild multiple choice dropdown - show all options regardless of whether they have values
                mcSelect.innerHTML = '<option value="">Select Answer</option>';
                optionRows.forEach((row, index) => {
                    const field = row.querySelector('.option-field');
                    const option = document.createElement('option');
                    option.value = index;
                    // Show option text if available, otherwise show "Option X"
                    const optionText = field && field.value.trim() ? field.value.trim() : `Option ${index + 1}`;
                    option.textContent = optionText.length > 50 ? optionText.substring(0, 50) + '...' : optionText;
                    mcSelect.appendChild(option);
                });
            } else if (questionType === 'checkboxes' && cbContainer) {
                // Clear and rebuild checkbox options - show all options
                cbContainer.innerHTML = '';
                optionRows.forEach((row, index) => {
                    const field = row.querySelector('.option-field');
                    const checkboxId = `cb-${questionIndex}-${index}`;
                    const checkboxDiv = document.createElement('div');
                    checkboxDiv.className = 'form-check';
                    const optionText = field && field.value.trim() ? field.value.trim() : `Option ${index + 1}`;
                    checkboxDiv.innerHTML = `
                        <input type="checkbox" class="form-check-input correct-answer-cb" 
                               name="questions[${questionIndex}][correct_answers][]" 
                               value="${index}" 
                               id="${checkboxId}"
                               data-required-group="${questionIndex}">
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
        
        // Show remove buttons if more than 2 options
        if (optionRows.length + 1 > 2) {
            optionsList.querySelectorAll('.remove-option-btn').forEach(btn => btn.style.display = 'block');
        }
        
        // Update correct answer options
        updateCorrectAnswerOptions(questionIndex);
        
        // Add event listener for input change
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
        
        // Update labels and correct answer options
        updateOptionLabels(questionIndex);
        updateCorrectAnswerOptions(questionIndex);
        
        // Hide remove buttons if only 2 or fewer options
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
        
        // Add event listeners to existing option inputs
        optionsList.querySelectorAll('.option-field').forEach(field => {
            field.addEventListener('input', function() {
                updateCorrectAnswerOptions(questionIndex);
                updateOptionLabels(questionIndex);
            });
        });
        
        // Update correct answer options initially - use setTimeout to ensure DOM is ready
            setTimeout(function() {
                updateCorrectAnswerOptions(questionIndex);
            }, 100);
        }
        
        // Form validation before submission - only validate if checkbox questions exist
        const assessmentForm = document.getElementById('assessmentForm');
        if (assessmentForm) {
            // Handle submit button click BEFORE form validation
            const submitButton = assessmentForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.addEventListener('click', function(e) {
                    // Remove required attributes from hidden sections BEFORE browser validation
                    const typeSelect = document.getElementById('type');
                    const selectedType = typeSelect ? typeSelect.value : '';
                    
                    // Force handle type change to clean up fields
                    if (selectedType) {
                        handleTypeChange(selectedType);
                    }
                    
                    // Extra safety: remove required from hidden quiz section
                    if (quizSection && (quizSection.style.display === 'none' || selectedType !== 'quiz')) {
                        const allElements = quizSection.querySelectorAll('input, select, textarea, [required]');
                        allElements.forEach(element => {
                            element.removeAttribute('required');
                            if (element.hasAttribute('name') && element.getAttribute('name').includes('questions')) {
                                if (!element.hasAttribute('data-original-name')) {
                                    element.setAttribute('data-original-name', element.getAttribute('name'));
                                }
                                element.removeAttribute('name');
                            }
                        });
                    }
                    
                    // Extra safety: remove required from hidden materials section
                    if (materialsSection && (materialsSection.style.display === 'none' || selectedType === 'quiz')) {
                        const allElements = materialsSection.querySelectorAll('input, select, textarea, [required]');
                        allElements.forEach(element => {
                            element.removeAttribute('required');
                            if (element.hasAttribute('name') && element.getAttribute('name').includes('materials')) {
                                if (!element.hasAttribute('data-original-name')) {
                                    element.setAttribute('data-original-name', element.getAttribute('name'));
                                }
                                element.removeAttribute('name');
                            }
                        });
                    }
                }, true); // Use capture phase to run before default behavior
            }
            
            assessmentForm.addEventListener('submit', function(e) {
                // Double-check to ensure hidden sections don't have required attributes
                // This is a safety check in case something was missed
                const materialsSection = document.getElementById('materialsSection');
                const quizSection = document.getElementById('quizSection');
                const typeSelect = document.getElementById('type');
                const selectedType = typeSelect ? typeSelect.value : '';
                
                // Apply type change handling one more time before submission
                handleTypeChange(selectedType);
                
                // Extra safety: Force remove required from hidden quiz section
                if (quizSection && (quizSection.style.display === 'none' || selectedType !== 'quiz')) {
                    quizSection.querySelectorAll('*').forEach(element => {
                        element.removeAttribute('required');
                        if (element.hasAttribute('name') && element.getAttribute('name').includes('questions')) {
                            if (!element.hasAttribute('data-original-name')) {
                                element.setAttribute('data-original-name', element.getAttribute('name'));
                            }
                            element.removeAttribute('name');
                        }
                    });
                }
                
                // Validate checkbox questions - ensure at least one correct answer is selected
                const checkboxGroups = {};
                // Only get checkboxes from visible questions that are actually checkbox type
                const questionItems = document.querySelectorAll('.question-item');
                
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
        
        // Add event listeners for existing question type selectors
        document.addEventListener('change', function(e) {
        if (e.target.classList.contains('question-type-selector')) {
                handleQuestionTypeChange(e.target);
            }
        });
        
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
        
        // Initialize first question on page load
        initializeQuestionOptions(0);
        
        // Function to calculate and display sum of question marks
        function updateTotalMarksValidation() {
            const totalMarksInput = document.getElementById('total_marks');
            const questionsContainer = document.getElementById('questionsContainer');
            
            if (!totalMarksInput || !questionsContainer) return;
            
            let sumOfMarks = 0;
            const markInputs = questionsContainer.querySelectorAll('input[name*="[marks]"]');
            
            markInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                sumOfMarks += value;
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
        
        // Initial validation check
        setTimeout(updateTotalMarksValidation, 500);

        // Add Question
        const addQuestionBtn = document.getElementById('addQuestion');
        if (addQuestionBtn) {
            addQuestionBtn.addEventListener('click', function() {
        const container = document.getElementById('questionsContainer');
        const questionHtml = `
            <div class="question-item card mb-3" data-question-index="${questionIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Question ${questionIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-question" aria-label="Remove question" title="Remove question">
                            <span data-feather="trash-2"></span> Remove
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Question Type*</label>
                            <select name="questions[${questionIndex}][question_type]" class="form-select question-type-selector" required aria-label="Question type">
                                <option value="multiple_choice" selected>Multiple Choice</option>
                                <option value="checkboxes">Checkboxes (Multiple Answers)</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Marks*</label>
                            <input type="number" name="questions[${questionIndex}][marks]" class="form-control" step="0.01" min="0" value="1" required aria-label="Question marks" placeholder="Enter marks">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question*</label>
                        <textarea name="questions[${questionIndex}][question]" class="form-control" rows="3" required placeholder="Enter your question" aria-label="Question text"></textarea>
                    </div>
                    <!-- Options Section -->
                    <div class="options-section" id="options-section-${questionIndex}">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Options*</label>
                                <button type="button" class="btn btn-sm btn-outline-primary add-option-btn" data-question-index="${questionIndex}">
                                    <span data-feather="plus"></span> Add Option
                                </button>
                            </div>
                            <div id="options-list-${questionIndex}">
                                <div class="option-row mb-2" data-option-index="0">
                                    <div class="input-group">
                                        <span class="input-group-text option-label">Option 1</span>
                                        <input type="text" name="questions[${questionIndex}][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="${questionIndex}">
                                        <button type="button" class="btn btn-outline-danger remove-option-btn" style="display: none;" aria-label="Remove option" title="Remove option">
                                            <span data-feather="trash-2"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="option-row mb-2" data-option-index="1">
                                    <div class="input-group">
                                        <span class="input-group-text option-label">Option 2</span>
                                        <input type="text" name="questions[${questionIndex}][options][]" class="form-control option-field" required placeholder="Enter option text" aria-label="Option input" data-question-index="${questionIndex}">
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
                                <input type="hidden" name="questions[${questionIndex}][shuffle_options]" value="0">
                                <input type="checkbox" class="form-check-input" name="questions[${questionIndex}][shuffle_options]" value="1" id="shuffle-${questionIndex}">
                                <label class="form-check-label" for="shuffle-${questionIndex}">
                                    Shuffle Answer Options
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Correct Answer - Multiple Choice -->
                    <div class="correct-answer-section" id="correct-answer-mc-${questionIndex}">
                        <div class="mb-3">
                            <label class="form-label">Correct Answer*</label>
                            <select name="questions[${questionIndex}][correct_answer]" class="form-select correct-answer-mc" id="correct-answer-select-${questionIndex}" required aria-label="Select correct answer">
                                <option value="">Select Answer</option>
                            </select>
                        </div>
                    </div>
                    <!-- Correct Answer - Checkboxes -->
                    <div class="correct-answer-section" id="correct-answer-cb-${questionIndex}" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Correct Answer(s)* (Select all that apply)</label>
                            <div id="correct-answers-checkboxes-${questionIndex}">
                                <!-- Dynamic checkboxes will be added here -->
                            </div>
                        </div>
                    </div>
                    <!-- Correct Answer - Short Answer -->
                    <div class="correct-answer-section" id="correct-answer-sa-${questionIndex}" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Correct Answer*</label>
                            <input type="text" name="questions[${questionIndex}][correct_answer]" class="form-control correct-answer-sa" placeholder="Enter the correct answer" aria-label="Correct answer">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', questionHtml);
        
        // Initialize the new question's options
        initializeQuestionOptions(questionIndex);
        
        questionIndex++;
        
        // Show remove buttons if more than one question
        if (container.children.length > 1) {
            document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'block');
        }
        
        // Update total marks validation after adding question
        setTimeout(updateTotalMarksValidation, 100);
        
        if (typeof feather !== 'undefined') feather.replace();
            });
        }

        // Remove Question
        document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            const questionItem = e.target.closest('.question-item');
            questionItem.remove();
            
            // Update question numbers and hide remove buttons if only one left
            const container = document.getElementById('questionsContainer');
            Array.from(container.children).forEach((item, index) => {
                item.querySelector('h6').textContent = `Question ${index + 1}`;
            });
            
            if (container.children.length <= 1) {
                document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'none');
                }
            
            // Update total marks validation after removing question
            setTimeout(updateTotalMarksValidation, 100);
            }
        });

        // Add Material
        const addMaterialBtn = document.getElementById('addMaterial');
        if (addMaterialBtn) {
            addMaterialBtn.addEventListener('click', function() {
        const container = document.getElementById('materialsContainer');
        const materialHtml = `
            <div class="material-item card mb-3" data-material-index="${materialIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Material ${materialIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-material" aria-label="Remove material" title="Remove material">
                            <span data-feather="trash-2"></span> Remove
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File*</label>
                        <input type="file" name="materials[${materialIndex}][file]" class="form-control material-file-input" required aria-label="Upload material file">
                        <small class="text-muted">Max file size: 10MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="materials[${materialIndex}][description]" class="form-control" rows="2" aria-label="Material description" placeholder="Enter material description (optional)"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', materialHtml);
        materialIndex++;
        
        // Show remove buttons if more than one material
        if (container.children.length > 1) {
            document.querySelectorAll('.remove-material').forEach(btn => btn.style.display = 'block');
        }
        
                if (typeof feather !== 'undefined') feather.replace();
            });
        }

        // Remove Material
        document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-material')) {
            const materialItem = e.target.closest('.material-item');
            materialItem.remove();
            
            // Update material numbers and hide remove buttons if only one left
            const container = document.getElementById('materialsContainer');
            Array.from(container.children).forEach((item, index) => {
                item.querySelector('h6').textContent = `Material ${index + 1}`;
            });
            
            if (container.children.length <= 1) {
                document.querySelectorAll('.remove-material').forEach(btn => btn.style.display = 'none');
            }
        }
    });
    }); // End of DOMContentLoaded

    // Show errors on page load only if coming from a failed form submission
    // Hide errors if user is on step 1 (type not selected or step1 is visible)
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            const errorAlert = document.querySelector('.alert-danger');
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const typeSelect = document.getElementById('type');
            
            if (errorAlert && step1 && step2) {
                // Check if type is selected - if not, user is on step 1, so hide errors
                const selectedType = typeSelect ? typeSelect.value : '';
                
                // If no type is selected, user is on step 1 - hide all errors
                if (!selectedType || selectedType === '') {
                    errorAlert.style.display = 'none';
                    // Also hide all error styling
                    document.querySelectorAll('.is-invalid, .border-danger').forEach(field => {
                        field.classList.remove('is-invalid', 'border-danger');
                    });
                    document.querySelectorAll('.form-label.text-danger').forEach(label => {
                        label.classList.remove('text-danger');
                    });
                    // Hide all individual field error messages (force hide even with d-block class)
                    document.querySelectorAll('.invalid-feedback').forEach(function(errorMsg) {
                        errorMsg.style.setProperty('display', 'none', 'important');
                        errorMsg.style.visibility = 'hidden';
                    });
                } else {
                    // Type is selected, form was submitted with errors - show step 2 and errors
                    step1.style.display = 'none';
                    step2.style.display = 'block';
                    const nextToStep2Btn = document.getElementById('nextToStep2');
                    if (nextToStep2Btn) {
                        nextToStep2Btn.disabled = false;
                    }
                    
                    setTimeout(() => {
                        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        const firstInvalidField = document.querySelector('.is-invalid, .border-danger');
                        if (firstInvalidField) {
                            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalidField.focus();
                        }
                    }, 100);
                }
            }
        @endif
    });
</script>
@endsection
