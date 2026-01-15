@extends('layouts.master')
@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9;">
            <div>
                <h2 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i data-feather="clipboard" style="width: 28px; height: 28px; color: #6366f1;"></i>
                    Create Assessment
                </h2>
                <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Fill in the details below to create a new assessment for your students.</p>
            </div>
            @php
                $backUrl = $preSelectedSubjectId ? route('teacher.subjects.show', $preSelectedSubjectId) : route('assessments.index', ['class_id' => $preSelectedClassId ?? '', 'subject_id' => $preSelectedSubjectId ?? '']);
            @endphp
            <a href="{{ $backUrl }}" style="background: #f1f5f9; color: #475569; padding: 0.625rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; border: 1px solid #e2e8f0;">
                <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                Back
            </a>
        </div>
        <form action="{{ route('assessments.store') }}" method="POST" enctype="multipart/form-data" id="assessmentForm">
            @csrf
            @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <i data-feather="alert-circle" style="width: 20px; height: 20px; color: #ef4444;"></i>
                    <strong style="color: #991b1b;">Please fix the following errors:</strong>
                </div>
                <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Step 1: Choose Assessment Type -->
            <div id="step1" class="assessment-step">
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #6366f1;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: #6366f1; color: white; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">1</span>
                        Choose Assessment Type
                    </h3>
                    <div style="margin-bottom: 1.5rem;">
                        <label for="type" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Assessment Type*</label>
                        <select id="type" name="type" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('type') border-color: #ef4444; @enderror" required onchange="this.style.borderColor = this.value ? '#6366f1' : '#e2e8f0'">
                            <option value="">Select Assessment Type</option>
                            <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz - Multiple choice questions with time limit</option>
                            <option value="homework" {{ old('type') === 'homework' ? 'selected' : '' }}>Homework - File submission assignment</option>
                            <option value="test" {{ old('type') === 'test' ? 'selected' : '' }}>Test - Comprehensive examination</option>
                        </select>
                        @error('type')
                        <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <button type="button" id="nextToStep2" disabled style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            Next Step
                            <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Assessment Details -->
            <div id="step2" class="assessment-step" style="display: none;">
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: #10b981; color: white; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">2</span>
                        Assessment Details
                    </h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label for="title" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Assessment Title*</label>
                        <input type="text" id="title" name="title" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('title') border-color: #ef4444; @enderror"
                            value="{{ old('title') }}" required placeholder="Enter assessment title">
                        @error('title')
                        <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label for="description" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Description <span style="color: #94a3b8; font-weight: 400;">(Optional)</span></label>
                        <textarea id="description" name="description"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; resize: vertical; min-height: 100px; @error('description') border-color: #ef4444; @enderror"
                            rows="4" placeholder="Enter assessment description (optional)">{{ old('description') }}</textarea>
                        @error('description')
                        <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label for="subject_id" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Subject*</label>
                        <select id="subject_id" name="subject_id" 
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('subject_id') border-color: #ef4444; @enderror" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $preSelectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                        <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                        @if($subjects->isEmpty())
                        <small style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">No subjects assigned. Please contact administrator to assign you to subjects.</small>
                        @endif
                    </div>
                    
                    <!-- Hidden field for class_id if needed by backend, but not shown to user -->
                    <input type="hidden" name="class_id" value="{{ old('class_id', $preSelectedClassId ?? '') }}">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label for="start_date" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Start Date & Time</label>
                            <input type="datetime-local" id="start_date" name="start_date"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('start_date') border-color: #ef4444; @enderror"
                                value="{{ old('start_date') }}" min="{{ date('Y-m-d\TH:i') }}">
                            @error('start_date')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">End Date & Time</label>
                            <input type="datetime-local" id="end_date" name="end_date"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('end_date') border-color: #ef4444; @enderror"
                                value="{{ old('end_date') }}" min="{{ date('Y-m-d\TH:i') }}">
                            @error('end_date')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label for="total_marks" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Total Marks*</label>
                            <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" max="1000"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('total_marks') border-color: #ef4444; @enderror"
                                value="{{ old('total_marks', 100) }}" required placeholder="Enter total marks">
                            @error('total_marks')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="timeLimitField" style="display: none;">
                            <label for="time_limit" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Time Limit (minutes)*</label>
                            <input type="number" id="time_limit" name="time_limit" min="1" step="1"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('time_limit') border-color: #ef4444; @enderror"
                                value="{{ old('time_limit') }}" placeholder="Enter time limit">
                            @error('time_limit')
                            <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                            <small style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.25rem; display: block;">Required for quizzes</small>
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; width: 100%;">
                                <input type="hidden" name="is_published" value="0">
                                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                                    <input type="checkbox" id="is_published" name="is_published" value="1" {{
                                        old('is_published') ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                                    <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">Publish Immediately</span>
                                </label>
                                <small style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem; display: block;">Make this assessment visible to students right away</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quiz-specific options -->
                    <div id="quizOptionsSection" style="display: none; margin-bottom: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label for="max_attempts" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; font-size: 0.95rem;">Maximum Attempts</label>
                                <input type="number" id="max_attempts" name="max_attempts" min="1" step="1"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; transition: border-color 0.2s; @error('max_attempts') border-color: #ef4444; @enderror"
                                    value="{{ old('max_attempts') }}" placeholder="Leave empty for unlimited">
                                @error('max_attempts')
                                <span style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                                <small style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.25rem; display: block;">Leave empty for unlimited attempts. System will keep the highest score.</small>
                            </div>
                    
                    <!-- Show Marks option for all assessment types -->
                    <div id="showMarksSection" style="margin-bottom: 1.5rem;">
                        <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                            <input type="hidden" name="show_marks" value="0">
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                                <input type="checkbox" id="show_marks" name="show_marks" value="1" {{
                                    old('show_marks', true) ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                                <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">Show Marks to Students</span>
                            </label>
                            <small style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem; display: block;">If unchecked, students won't see their scores.</small>
                        </div>
                    </div>
                    
                    <div id="step2ButtonsContainer" style="display: flex !important; gap: 1rem; margin-top: 2rem; justify-content: space-between; visibility: visible !important;">
                        <button type="button" id="backToStep1" style="background: #f1f5f9; color: #475569; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s;">
                            <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                            Previous
                        </button>
                        <button type="button" id="nextToStep3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: none; font-weight: 600; display: inline-flex !important; visibility: visible !important; opacity: 1 !important; align-items: center; gap: 0.5rem; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            Continue to Questions/Materials
                            <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Quiz Questions and Materials Section -->
            <div id="quizSection" class="assessment-step" style="display: none;">
                <!-- Quiz Questions Section -->
                <div id="quizQuestionsContainer" style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #f59e0b;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: #f59e0b; color: white; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">3</span>
                        Quiz Questions
                    </h3>
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
                    <div id="materialsSection" style="display: none; background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid #6366f1;">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-feather="upload" style="width: 24px; height: 24px; color: #6366f1;"></i>
                            Upload Materials
                        </h3>
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

                <!-- Submit Button Section - Always visible when Step 3 is shown -->
                <div id="submitButtonSection" style="background: white; border-radius: 0.75rem; padding: 1.5rem; margin-top: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: block !important;">
                    <div style="display: flex; gap: 1rem; justify-content: space-between; align-items: center;">
                        <button type="button" id="backToStep1FromFinal" style="background: #f1f5f9; color: #475569; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s;">
                            <i data-feather="arrow-left" style="width: 18px; height: 18px;"></i>
                            Previous
                        </button>
                        <button type="submit" id="createAssessmentBtn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 2rem; border-radius: 0.5rem; border: none; font-weight: 600; display: inline-flex !important; align-items: center; gap: 0.5rem; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'" aria-label="Create assessment">
                            <i data-feather="save" style="width: 18px; height: 18px;"></i>
                            Create Assessment
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Form input focus styles */
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="datetime-local"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    /* Button hover effects */
    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4) !important;
    }
    
    /* Responsive grid */
    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

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
            
            // Always ensure the "Continue to Questions/Materials" button is visible in Step 2
            const nextToStep3Btn = document.getElementById('nextToStep3');
            if (nextToStep3Btn) {
                nextToStep3Btn.style.display = 'inline-flex';
                // Update button text based on assessment type
                if (selectedType === 'quiz') {
                    nextToStep3Btn.innerHTML = 'Continue to Questions <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                } else if (selectedType === 'homework' || selectedType === 'test') {
                    nextToStep3Btn.innerHTML = 'Continue to Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                } else {
                    nextToStep3Btn.innerHTML = 'Continue to Questions/Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                }
                if (typeof feather !== 'undefined') feather.replace();
            }
            
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
                // Show quiz questions container, hide materials section
                // Keep quizSection visible (it contains the submit button)
                const quizQuestionsContainer = document.getElementById('quizQuestionsContainer');
                if (quizQuestionsContainer) {
                    quizQuestionsContainer.style.display = 'block';
                    // Restore name and required attributes for quiz inputs
                    quizQuestionsContainer.querySelectorAll('input, select, textarea').forEach(input => {
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
                // Hide quiz questions container, show materials section
                // But keep quizSection visible (it contains the submit button)
                const quizQuestionsContainer = document.getElementById('quizQuestionsContainer');
                if (quizQuestionsContainer) {
                    quizQuestionsContainer.style.display = 'none';
                    // Remove name and required attributes from ALL quiz inputs so they won't be submitted
                    const allQuizInputs = quizQuestionsContainer.querySelectorAll('input, select, textarea, [required]');
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
                
                // Ensure submit button section is always visible
                const submitButtonSection = document.getElementById('submitButtonSection');
                if (submitButtonSection) {
                    submitButtonSection.style.display = 'block';
                }
                const createBtn = document.getElementById('createAssessmentBtn');
                if (createBtn) {
                    createBtn.style.display = 'inline-flex';
                }
                
                // Ensure "Continue to Materials" button is visible in Step 2
                const nextToStep3Btn = document.getElementById('nextToStep3');
                if (nextToStep3Btn) {
                    nextToStep3Btn.style.display = 'inline-flex';
                    // Update button text for homework/test
                    nextToStep3Btn.innerHTML = 'Continue to Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                    if (typeof feather !== 'undefined') feather.replace();
                }
            }
        }

        if (nextToStep2Btn) {
            nextToStep2Btn.addEventListener('click', function() {
                if (step1) step1.style.display = 'none';
                if (step2) {
                    step2.style.display = 'block';
                }
                
                // Show appropriate section based on type
                const selectedType = typeSelect ? typeSelect.value : '';
                handleTypeChange(selectedType);
                
                // CRITICAL: Force show the Continue button - multiple attempts to ensure it works
                function forceShowButton() {
                    const nextToStep3Btn = document.getElementById('nextToStep3');
                    const buttonsContainer = document.getElementById('step2ButtonsContainer');
                    const step2 = document.getElementById('step2');
                    
                    // Ensure Step 2 itself is visible
                    if (step2) {
                        step2.style.setProperty('display', 'block', 'important');
                        step2.style.setProperty('visibility', 'visible', 'important');
                        step2.style.setProperty('opacity', '1', 'important');
                        console.log('Step 2 display:', step2.style.display, 'computed:', window.getComputedStyle(step2).display);
                    }
                    
                    // Ensure button container is visible
                    if (buttonsContainer) {
                        buttonsContainer.style.setProperty('display', 'flex', 'important');
                        buttonsContainer.style.setProperty('visibility', 'visible', 'important');
                        buttonsContainer.style.setProperty('opacity', '1', 'important');
                        buttonsContainer.style.setProperty('position', 'relative', 'important');
                        console.log('Container display:', buttonsContainer.style.display, 'computed:', window.getComputedStyle(buttonsContainer).display);
                    }
                    
                    if (nextToStep3Btn) {
                        console.log('Button found, forcing visibility. Type:', selectedType);
                        console.log('Button current display:', nextToStep3Btn.style.display);
                        console.log('Button computed display:', window.getComputedStyle(nextToStep3Btn).display);
                        console.log('Button computed visibility:', window.getComputedStyle(nextToStep3Btn).visibility);
                        console.log('Button parent:', nextToStep3Btn.parentElement);
                        console.log('Button offsetParent:', nextToStep3Btn.offsetParent);
                        
                        // Remove any inline styles that might hide it
                        nextToStep3Btn.removeAttribute('hidden');
                        nextToStep3Btn.removeAttribute('aria-hidden');
                        
                        // Force set visibility with !important via setProperty
                        nextToStep3Btn.style.setProperty('display', 'inline-flex', 'important');
                        nextToStep3Btn.style.setProperty('visibility', 'visible', 'important');
                        nextToStep3Btn.style.setProperty('opacity', '1', 'important');
                        nextToStep3Btn.style.setProperty('position', 'relative', 'important');
                        nextToStep3Btn.style.setProperty('width', 'auto', 'important');
                        nextToStep3Btn.style.setProperty('height', 'auto', 'important');
                        nextToStep3Btn.style.setProperty('min-width', 'auto', 'important');
                        nextToStep3Btn.style.setProperty('min-height', 'auto', 'important');
                        
                        // Update button text based on assessment type
                        if (selectedType === 'quiz') {
                            nextToStep3Btn.innerHTML = 'Continue to Questions <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                        } else if (selectedType === 'homework' || selectedType === 'test') {
                            nextToStep3Btn.innerHTML = 'Continue to Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                        } else {
                            nextToStep3Btn.innerHTML = 'Continue to Questions/Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                        }
                        
                        // Force re-render
                        nextToStep3Btn.offsetHeight; // Trigger reflow
                        
                        const computedStyle = window.getComputedStyle(nextToStep3Btn);
                        console.log('After setting - Button computed display:', computedStyle.display);
                        console.log('After setting - Button computed visibility:', computedStyle.visibility);
                        console.log('After setting - Button computed opacity:', computedStyle.opacity);
                        console.log('After setting - Button width:', computedStyle.width);
                        console.log('After setting - Button height:', computedStyle.height);
                        console.log('After setting - Button position:', computedStyle.position);
                        console.log('After setting - Button top:', computedStyle.top);
                        console.log('After setting - Button left:', computedStyle.left);
                        console.log('After setting - Button offsetWidth:', nextToStep3Btn.offsetWidth);
                        console.log('After setting - Button offsetHeight:', nextToStep3Btn.offsetHeight);
                        console.log('After setting - Button getBoundingClientRect:', nextToStep3Btn.getBoundingClientRect());
                        
                        // Check parent visibility
                        let parent = nextToStep3Btn.parentElement;
                        let level = 0;
                        while (parent && level < 5) {
                            const parentStyle = window.getComputedStyle(parent);
                            console.log(`Parent level ${level} (${parent.tagName}${parent.id ? '#' + parent.id : ''}${parent.className ? '.' + parent.className : ''}):`, {
                                display: parentStyle.display,
                                visibility: parentStyle.visibility,
                                opacity: parentStyle.opacity,
                                height: parentStyle.height,
                                width: parentStyle.width
                            });
                            parent = parent.parentElement;
                            level++;
                        }
                    } else {
                        console.error('Button #nextToStep3 not found in DOM!');
                    }
                }
                
                // Call immediately
                forceShowButton();
                
                // Call after short delays to override any other code
                setTimeout(forceShowButton, 10);
                setTimeout(forceShowButton, 50);
                setTimeout(forceShowButton, 100);
                setTimeout(forceShowButton, 200);
                
                if (typeof feather !== 'undefined') feather.replace();
            });
        }
        
        // Also handle type change if user changes type after clicking next (though this shouldn't happen)
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                handleTypeChange(selectedType);
                
                // Ensure the "Continue to Questions/Materials" button is always visible and update text
                const nextToStep3Btn = document.getElementById('nextToStep3');
                if (nextToStep3Btn) {
                    nextToStep3Btn.style.display = 'inline-flex';
                    // Update button text based on assessment type
                    if (selectedType === 'quiz') {
                        nextToStep3Btn.innerHTML = 'Continue to Questions <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                    } else if (selectedType === 'homework' || selectedType === 'test') {
                        nextToStep3Btn.innerHTML = 'Continue to Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                    } else {
                        nextToStep3Btn.innerHTML = 'Continue to Questions/Materials <i data-feather="arrow-right" style="width: 18px; height: 18px;"></i>';
                    }
                    if (typeof feather !== 'undefined') feather.replace();
                }
            });
        }

        const backToStep1Btn = document.getElementById('backToStep1');
        if (backToStep1Btn) {
            backToStep1Btn.addEventListener('click', function() {
                if (step2) step2.style.display = 'none';
                if (step1) step1.style.display = 'block';
                if (typeof feather !== 'undefined') feather.replace();
            });
        }

        const nextToStep3Btn = document.getElementById('nextToStep3');
        if (nextToStep3Btn) {
            nextToStep3Btn.addEventListener('click', function() {
                if (step2) step2.style.display = 'none';
                if (quizSection) {
                    quizSection.style.display = 'block';
                }
                
                // Ensure appropriate sections are shown based on type
                const selectedType = typeSelect ? typeSelect.value : '';
                
                // Force show materials section if homework/test BEFORE calling handleTypeChange
                if (selectedType === 'homework' || selectedType === 'test') {
                    const materialsSection = document.getElementById('materialsSection');
                    if (materialsSection) {
                        materialsSection.style.display = 'block';
                    }
                }
                
                handleTypeChange(selectedType);
                
                // Always ensure submit button section is visible
                const submitButtonSection = document.getElementById('submitButtonSection');
                if (submitButtonSection) {
                    submitButtonSection.style.display = 'block';
                }
                const createBtn = document.getElementById('createAssessmentBtn');
                if (createBtn) {
                    createBtn.style.display = 'inline-flex';
                }
                
                if (typeof feather !== 'undefined') feather.replace();
            });
        }

        const backToStep1FromFinalBtn = document.getElementById('backToStep1FromFinal');
        if (backToStep1FromFinalBtn) {
            backToStep1FromFinalBtn.addEventListener('click', function() {
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
                    
                    // Extra safety: remove required from hidden quiz questions container
                    const quizQuestionsContainer = document.getElementById('quizQuestionsContainer');
                    if (quizQuestionsContainer && (quizQuestionsContainer.style.display === 'none' || selectedType !== 'quiz')) {
                        const allElements = quizQuestionsContainer.querySelectorAll('input, select, textarea, [required]');
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
                
                // Extra safety: Force remove required from hidden quiz questions container
                const quizQuestionsContainer = document.getElementById('quizQuestionsContainer');
                if (quizQuestionsContainer && (quizQuestionsContainer.style.display === 'none' || selectedType !== 'quiz')) {
                    quizQuestionsContainer.querySelectorAll('*').forEach(element => {
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
                const checkboxes = document.querySelectorAll('.correct-answer-cb[data-required-group]');
                
                // Only validate if there are checkbox questions
                if (checkboxes.length > 0) {
                    checkboxes.forEach(cb => {
                        const groupId = cb.getAttribute('data-required-group');
                        if (!checkboxGroups[groupId]) checkboxGroups[groupId] = [];
                        checkboxGroups[groupId].push(cb);
                    });
                    
                    for (const groupId in checkboxGroups) {
                        const checked = checkboxGroups[groupId].some(cb => cb.checked);
                        if (!checked) {
                            e.preventDefault();
                            alert('Please select at least one correct answer for checkbox questions.');
                            return false;
                        }
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
    
    // Additional safety: Ensure button is always visible when Step 2 is shown
    const observer = new MutationObserver(function(mutations) {
        const step2 = document.getElementById('step2');
        const nextToStep3Btn = document.getElementById('nextToStep3');
        if (step2 && step2.style.display !== 'none' && nextToStep3Btn) {
            nextToStep3Btn.style.display = 'inline-flex';
            nextToStep3Btn.style.visibility = 'visible';
            nextToStep3Btn.style.opacity = '1';
        }
    });
    
    const step2 = document.getElementById('step2');
    if (step2) {
        observer.observe(step2, { attributes: true, attributeFilter: ['style'] });
    }
</script>

<style>
    /* Ensure the Continue button is always visible - override any other styles */
    #nextToStep3 {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
    }
    
    /* When Step 2 is visible, ensure the button is visible */
    #step2[style*="display: block"] #nextToStep3,
    #step2:not([style*="display: none"]) #nextToStep3,
    #step2 #nextToStep3 {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Force visibility for all assessment types */
    .assessment-step #nextToStep3,
    div#step2 #nextToStep3 {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>

<script>
    // Force button visibility on page load and whenever Step 2 is shown
    document.addEventListener('DOMContentLoaded', function() {
        function ensureButtonVisible() {
            const nextToStep3Btn = document.getElementById('nextToStep3');
            if (nextToStep3Btn) {
                nextToStep3Btn.style.setProperty('display', 'inline-flex', 'important');
                nextToStep3Btn.style.setProperty('visibility', 'visible', 'important');
                nextToStep3Btn.style.setProperty('opacity', '1', 'important');
            }
        }
        
        // Run immediately
        ensureButtonVisible();
        
        // Run after a short delay to ensure DOM is ready
        setTimeout(ensureButtonVisible, 100);
        setTimeout(ensureButtonVisible, 500);
        
        // Watch for Step 2 visibility changes
        const step2 = document.getElementById('step2');
        if (step2) {
            const step2Observer = new MutationObserver(function() {
                if (step2.style.display === 'block' || step2.style.display === '') {
                    ensureButtonVisible();
                }
            });
            step2Observer.observe(step2, { 
                attributes: true, 
                attributeFilter: ['style'],
                childList: false,
                subtree: false
            });
        }
        
        // Also watch the button itself
        const nextToStep3Btn = document.getElementById('nextToStep3');
        if (nextToStep3Btn) {
            const buttonObserver = new MutationObserver(function() {
                ensureButtonVisible();
            });
            buttonObserver.observe(nextToStep3Btn, { 
                attributes: true, 
                attributeFilter: ['style', 'class']
            });
        }
    });
</script>
@endsection
