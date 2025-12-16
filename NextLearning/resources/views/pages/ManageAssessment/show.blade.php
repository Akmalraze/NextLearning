@extends('layouts.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assessment Details</h5>
        <div>
            @if(auth()->user()->hasRole('Teacher') && $assessment->teacher_id === auth()->id())
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
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="40%">Start Date:</th>
                            <td>{{ $assessment->start_date ? $assessment->start_date->format('F d, Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $assessment->end_date ? $assessment->end_date->format('F d, Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Due Date:</th>
                            <td>{{ $assessment->due_date ? $assessment->due_date->format('F d, Y') : 'Not set' }}</td>
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

        @if(auth()->user()->hasRole('Teacher') && $assessment->teacher_id === auth()->id())
        <!-- Instructions for Teachers -->
        <div class="alert alert-info mt-4">
            <h6 class="alert-heading">
                <span data-feather="info"></span> Manage Assessment Content
            </h6>
            @if($assessment->type === 'quiz')
            <p class="mb-0">Click the <strong>"Add Question"</strong> button below to create multiple-choice questions (A, B, C, D) for this quiz.</p>
            @else
            <p class="mb-0">Click the <strong>"Upload Material"</strong> button below to upload files (up to 10MB) for this {{ $assessment->type }}.</p>
            @endif
        </div>

        <!-- Quiz Questions Section -->
        @if($assessment->type === 'quiz')
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Questions</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <span data-feather="plus"></span> Add Question
                </button>
            </div>
            <div class="card-body">
                @forelse($assessment->questions as $index => $question)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">Question {{ $index + 1 }} ({{ number_format($question->marks, 2) }} marks)</h6>
                            <form action="{{ route('assessments.questions.destroy', [$assessment->id, $question->id]) }}" method="POST" 
                                  style="display:inline;" onsubmit="return confirm('Delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <span data-feather="trash-2"></span>
                                </button>
                            </form>
                        </div>
                        <p class="mb-3">{{ $question->question }}</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'a' ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $question->correct_answer === 'a' ? 'text-success fw-bold' : '' }}">
                                        A. {{ $question->option_a }}
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'b' ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $question->correct_answer === 'b' ? 'text-success fw-bold' : '' }}">
                                        B. {{ $question->option_b }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'c' ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $question->correct_answer === 'c' ? 'text-success fw-bold' : '' }}">
                                        C. {{ $question->option_c }}
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'd' ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $question->correct_answer === 'd' ? 'text-success fw-bold' : '' }}">
                                        D. {{ $question->option_d }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No questions added yet. Click "Add Question" to create questions for this quiz.</p>
                @endforelse
            </div>
        </div>

        <!-- Add Question Modal -->
        <div class="modal fade" id="addQuestionModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('assessments.questions.store', $assessment->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="question" class="form-label">Question*</label>
                                <textarea id="question" name="question" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="option_a" class="form-label">Option A*</label>
                                    <input type="text" id="option_a" name="option_a" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="option_b" class="form-label">Option B*</label>
                                    <input type="text" id="option_b" name="option_b" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="option_c" class="form-label">Option C*</label>
                                    <input type="text" id="option_c" name="option_c" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="option_d" class="form-label">Option D*</label>
                                    <input type="text" id="option_d" name="option_d" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="correct_answer" class="form-label">Correct Answer*</label>
                                    <select id="correct_answer" name="correct_answer" class="form-select" required>
                                        <option value="">Select Answer</option>
                                        <option value="a">A</option>
                                        <option value="b">B</option>
                                        <option value="c">C</option>
                                        <option value="d">D</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="marks" class="form-label">Marks*</label>
                                    <input type="number" id="marks" name="marks" step="0.01" min="0" class="form-control" value="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Materials Section for Test/Homework -->
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
                                <form action="{{ route('assessments.materials.destroy', [$assessment->id, $material->id]) }}" method="POST" 
                                      style="display:inline;" onsubmit="return confirm('Delete this material?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <span data-feather="trash-2"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No materials uploaded yet. Click "Upload Material" to add materials for this assessment.</p>
                @endforelse
            </div>
        </div>

        <!-- Upload Material Modal -->
        <div class="modal fade" id="uploadMaterialModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('assessments.materials.store', $assessment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Material</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file" class="form-label">File*</label>
                                <input type="file" id="file" name="file" class="form-control" required>
                                <small class="text-muted">Max file size: 10MB</small>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endif

        @if(auth()->user()->hasRole('Teacher') && $assessment->teacher_id === auth()->id())
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
