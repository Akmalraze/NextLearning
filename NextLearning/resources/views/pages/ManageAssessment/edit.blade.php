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
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal"
                    title="Add Question" aria-label="Add Question">
                    <span data-feather="plus"></span> Add Question
                </button>
            </div>
            <div class="card-body">
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
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">Question {{ $index + 1 }} ({{ number_format($question->marks, 2) }} marks)</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn"
                                data-question-id="{{ $question->id }}" 
                                data-assessment-id="{{ $assessment->id }}"
                                title="Delete question" aria-label="Delete question">
                                <span data-feather="trash-2"></span>
                            </button>
                        </div>
                        <p class="mb-3">{{ $question->question }}</p>
                        @if($question->question_type === 'short_answer')
                            <p class="text-muted"><em>Short Answer Question</em></p>
                        @else
                            <div class="mb-2">
                                @foreach($options as $optIndex => $optText)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="{{ $question->question_type === 'checkboxes' ? 'checkbox' : 'radio' }}" disabled 
                                           {{ (is_array($question->correct_answer) ? in_array($optIndex, explode(',', $question->correct_answer)) : (string)$question->correct_answer === (string)$optIndex) ? 'checked' : '' }}
                                           aria-label="Option {{ $optIndex + 1 }}: {{ $optText }}" title="Option {{ $optIndex + 1 }}">
                                    <label class="form-check-label {{ (is_array($question->correct_answer) ? in_array($optIndex, explode(',', $question->correct_answer)) : (string)$question->correct_answer === (string)$optIndex) ? 'text-success fw-bold' : '' }}">
                                        Option {{ $optIndex + 1 }}: {{ $optText }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No questions added yet. Click "Add Question" to create questions for this quiz.</p>
                @endforelse
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
    
    // Handle delete question via AJAX (to avoid nested forms)
    document.addEventListener('DOMContentLoaded', function() {
        // Delete question buttons
        document.querySelectorAll('.delete-question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Delete this question?')) return;
                
                const questionId = this.getAttribute('data-question-id');
                const assessmentId = this.getAttribute('data-assessment-id');
                const button = this;
                
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.getElementById('editAssessmentForm')?.querySelector('input[name="_token"]')?.value;
                
                // Create a form and submit it to handle DELETE properly
                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.action = `/assessments/${assessmentId}/questions/${questionId}`;
                
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
        
        // Delete material buttons
        document.querySelectorAll('.delete-material-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Delete this material?')) return;
                
                const materialId = this.getAttribute('data-material-id');
                const assessmentId = this.getAttribute('data-assessment-id');
                const button = this;
                
                const token2 = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.getElementById('editAssessmentForm')?.querySelector('input[name="_token"]')?.value;
                
                // Create a form and submit it to handle DELETE properly
                const deleteForm2 = document.createElement('form');
                deleteForm2.method = 'POST';
                deleteForm2.action = `/assessments/${assessmentId}/materials/${materialId}`;
                
                const methodInput2 = document.createElement('input');
                methodInput2.type = 'hidden';
                methodInput2.name = '_method';
                methodInput2.value = 'DELETE';
                deleteForm2.appendChild(methodInput2);
                
                const tokenInput2 = document.createElement('input');
                tokenInput2.type = 'hidden';
                tokenInput2.name = '_token';
                tokenInput2.value = token2;
                deleteForm2.appendChild(tokenInput2);
                
                document.body.appendChild(deleteForm2);
                deleteForm2.submit();
            });
        });
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
