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
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
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
                            <label for="type" class="form-label">Assessment Type*</label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="homework" {{ old('type') === 'homework' ? 'selected' : '' }}>Homework</option>
                                <option value="test" {{ old('type') === 'test' ? 'selected' : '' }}>Test</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
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
                        <label for="title" class="form-label">Assessment Title*</label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" required>
                        @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="4">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Class*</label>
                            <select id="class_id" name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $preSelectedClassId ?? '') == $class->id ? 'selected' : '' }}>
                                    {{ $class->form_level }} {{ $class->name }} ({{ $class->academic_session }})
                                </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if($classes->isEmpty())
                            <small class="text-danger">No classes assigned. Please contact administrator to assign you to classes.</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Subject*</label>
                            <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $preSelectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if($subjects->isEmpty())
                            <small class="text-danger">No subjects assigned. Please contact administrator to assign you to subjects.</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}">
                            @error('start_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" name="end_date"
                                class="form-control @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}">
                            @error('end_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" id="due_date" name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}">
                            @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total_marks" class="form-label">Total Marks*</label>
                            <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" max="1000"
                                class="form-control @error('total_marks') is-invalid @enderror"
                                value="{{ old('total_marks', 100) }}" required>
                            @error('total_marks')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
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

                    <!-- Quiz Questions Section -->
                    <div id="quizSection" style="display: none;">
                        <hr class="my-4">
                        <h6 class="mb-3">Quiz Questions</h6>
                        <div id="questionsContainer">
                            <div class="question-item card mb-3" data-question-index="0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Question 1</h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-question" style="display: none;">
                                            <span data-feather="trash-2"></span> Remove
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Question*</label>
                                        <textarea name="questions[0][question]" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Option A*</label>
                                            <input type="text" name="questions[0][option_a]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Option B*</label>
                                            <input type="text" name="questions[0][option_b]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Option C*</label>
                                            <input type="text" name="questions[0][option_c]" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Option D*</label>
                                            <input type="text" name="questions[0][option_d]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Correct Answer*</label>
                                            <select name="questions[0][correct_answer]" class="form-select" required>
                                                <option value="">Select Answer</option>
                                                <option value="a">A</option>
                                                <option value="b">B</option>
                                                <option value="c">C</option>
                                                <option value="d">D</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Marks*</label>
                                            <input type="number" name="questions[0][marks]" class="form-control" step="0.01" min="0" value="1" required>
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
                                        <button type="button" class="btn btn-sm btn-danger remove-material" style="display: none;">
                                            <span data-feather="trash-2"></span> Remove
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">File*</label>
                                        <input type="file" name="materials[0][file]" class="form-control" required>
                                        <small class="text-muted">Max file size: 10MB</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="materials[0][description]" class="form-control" rows="2"></textarea>
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
                        <button type="submit" class="btn btn-success">
                            <span data-feather="save"></span> Create Assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
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

    typeSelect.addEventListener('change', function() {
        nextToStep2Btn.disabled = !this.value;
    });

    nextToStep2Btn.addEventListener('click', function() {
        step1.style.display = 'none';
        step2.style.display = 'block';
        
        // Show appropriate section based on type
        const selectedType = typeSelect.value;
        if (selectedType === 'quiz') {
            quizSection.style.display = 'block';
            materialsSection.style.display = 'none';
        } else if (selectedType === 'homework' || selectedType === 'test') {
            quizSection.style.display = 'none';
            materialsSection.style.display = 'block';
        }
        
        if (typeof feather !== 'undefined') feather.replace();
    });

    document.getElementById('backToStep1').addEventListener('click', function() {
        step2.style.display = 'none';
        step1.style.display = 'block';
        if (typeof feather !== 'undefined') feather.replace();
    });

    // Add Question
    document.getElementById('addQuestion').addEventListener('click', function() {
        const container = document.getElementById('questionsContainer');
        const questionHtml = `
            <div class="question-item card mb-3" data-question-index="${questionIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Question ${questionIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-question">
                            <span data-feather="trash-2"></span> Remove
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question*</label>
                        <textarea name="questions[${questionIndex}][question]" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Option A*</label>
                            <input type="text" name="questions[${questionIndex}][option_a]" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Option B*</label>
                            <input type="text" name="questions[${questionIndex}][option_b]" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Option C*</label>
                            <input type="text" name="questions[${questionIndex}][option_c]" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Option D*</label>
                            <input type="text" name="questions[${questionIndex}][option_d]" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Correct Answer*</label>
                            <select name="questions[${questionIndex}][correct_answer]" class="form-select" required>
                                <option value="">Select Answer</option>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Marks*</label>
                            <input type="number" name="questions[${questionIndex}][marks]" class="form-control" step="0.01" min="0" value="1" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', questionHtml);
        questionIndex++;
        
        // Show remove buttons if more than one question
        if (container.children.length > 1) {
            document.querySelectorAll('.remove-question').forEach(btn => btn.style.display = 'block');
        }
        
        if (typeof feather !== 'undefined') feather.replace();
    });

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
    document.getElementById('addMaterial').addEventListener('click', function() {
        const container = document.getElementById('materialsContainer');
        const materialHtml = `
            <div class="material-item card mb-3" data-material-index="${materialIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Material ${materialIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-material">
                            <span data-feather="trash-2"></span> Remove
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File*</label>
                        <input type="file" name="materials[${materialIndex}][file]" class="form-control" required>
                        <small class="text-muted">Max file size: 10MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="materials[${materialIndex}][description]" class="form-control" rows="2"></textarea>
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
</script>
@endsection
