@extends('layouts.master')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1" style="font-weight: 700; color:#0f172a;">My Courses</h2>
        <p class="mb-0 text-muted">Create and manage the courses you publish for learners.</p>
    </div>
    <div>
        <a href="{{ route('teacher.subjects.create') }}" class="btn btn-primary">
            <span data-feather="plus" style="width:18px;height:18px;margin-right:4px;"></span> New Course
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="modern-card" style="margin-top:0; margin-bottom:1.5rem;">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by course name or level..."
                value="{{ $search ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ ($isActive ?? '' )==='1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($isActive ?? '' )==='0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-3 text-md-end">
            <button type="submit" class="btn btn-outline-secondary">
                <span data-feather="search" style="width:18px;height:18px;margin-right:4px;"></span> Filter
            </button>
        </div>
    </form>
</div>

@if($subjects->isEmpty())
    <div class="modern-card text-center">
        <i data-feather="inbox" style="width:48px;height:48px; margin-bottom:1rem; color:#cbd5f5;"></i>
        <p class="mb-2">You haven't created any courses yet.</p>
        <a href="{{ route('teacher.subjects.create') }}" class="btn btn-primary btn-sm">
            <span data-feather="plus" style="width:16px;height:16px;margin-right:4px;"></span> Create your first course
        </a>
    </div>
@else
    <div class="row g-4">
        @foreach($subjects as $subject)
            @php
                $isActive = $subject->is_active;
                $isPublished = $subject->is_published ?? false;
                $level = $subject->code ?? $subject->subjects_code;
                $description = $subject->description ?? '';
                $learnerCount = isset($subject->learners_count) ? $subject->learners_count : ($subject->learners->count() ?? 0);
            @endphp
            <div class="col-md-4">
                <a href="{{ route('teacher.subjects.show', $subject->id) }}" style="text-decoration:none; color:inherit;">
                <div class="modern-card" style="margin-top:0; padding:0; overflow:hidden; cursor:pointer;">
                    <!-- Image / header area -->
                    <div style="height:140px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 40%, #22c55e 100%); position:relative;">
                        <div style="position:absolute; inset:0; background: radial-gradient(circle at top left, rgba(255,255,255,0.25), transparent 55%);"></div>
                        <div style="position:absolute; bottom:12px; left:16px; right:16px; display:flex; justify-content:space-between; align-items:flex-end;">
                            <div>
                                <div style="font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:rgba(255,255,255,0.85); margin-bottom:4px;">
                                    {{ $level ?: 'No level' }}
                                </div>
                                <h3 style="font-size:1.1rem; font-weight:700; color:white; margin:0;">
                                    {{ $subject->name ?? $subject->subjects_name }}
                                </h3>
                            </div>
                            <div style="text-align:right;">
                                @if($isPublished)
                                    <span class="badge bg-success" style="font-size:0.7rem;">Published</span>
                                @else
                                    <span class="badge bg-secondary" style="font-size:0.7rem;">Draft</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="padding:1.25rem;">
                        <p class="modern-card-text" style="min-height:3rem; margin-bottom:0.75rem;">
                            {{ \Illuminate\Support\Str::limit($description, 100) ?: 'No description added yet.' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div style="font-size:0.8rem; color:#64748b;">
                                <i data-feather="users" style="width:14px;height:14px;margin-right:4px;"></i>
                                {{ $learnerCount }} learners
                            </div>
                            <div>
                                @if($isActive)
                                    <span class="badge bg-success" style="font-size:0.75rem;">Active</span>
                                @else
                                    <span class="badge bg-secondary" style="font-size:0.75rem;">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('teacher.subjects.show', $subject->id) }}" style="font-size:0.8rem; color:#4f46e5; font-weight:600; text-decoration: none;">
                                <i data-feather="settings" style="width:14px;height:14px;margin-right:4px;"></i>
                                Manage Course
                            </a>
                            @if($isActive)
                                <form method="POST" action="{{ route('teacher.subjects.toggle-publish', $subject->id) }}" style="display: inline;" onsubmit="return confirm('{{ $isPublished ? 'Unpublish' : 'Publish' }} this course?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $isPublished ? 'btn-warning' : 'btn-success' }}" style="font-size:0.75rem; padding: 0.25rem 0.75rem;">
                                        <i data-feather="{{ $isPublished ? 'eye-off' : 'eye' }}" style="width:12px;height:12px;margin-right:4px;"></i>
                                        {{ $isPublished ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted" style="font-size:0.75rem;">Activate to publish</span>
                            @endif
                        </div>
                    </div>
                </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $subjects->links('pagination::bootstrap-5') }}
    </div>
@endif

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection
