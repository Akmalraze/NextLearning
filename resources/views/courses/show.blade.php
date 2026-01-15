@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="modern-card" style="margin-top: 0;">
                <div class="modern-card-header d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="modern-card-title mb-1" style="font-size: 1.5rem;">
                            {{ $course->name }}
                        </h1>
                        <span class="badge bg-primary me-2">{{ $course->code }}</span>
                        @if($course->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <div style="font-size: 0.85rem; color: #64748b;">
                            {{ $course->learners()->count() }} learners enrolled
                        </div>
                    </div>
                </div>

                <p class="modern-card-text mt-3">
                    {{ $course->description ?: 'No description has been provided for this course yet.' }}
                </p>

                <hr>

                <h2 class="modern-card-title mb-3" style="font-size: 1.1rem;">Course Content</h2>

                @if($course->sectionTitles->isEmpty() && $course->modules->isEmpty())
                    <p class="text-muted mb-0">No course content has been added yet.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        {{-- Display Section Titles with Materials --}}
                        @foreach($course->sectionTitles as $sectionTitle)
                            <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #6366f1;">
                                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">
                                    {{ $sectionTitle->title }}
                                </h3>
                                @if($sectionTitle->description)
                                    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem;">
                                        {{ $sectionTitle->description }}
                                    </p>
                                @endif
                                
                                @if($sectionTitle->materials->isNotEmpty())
                                    <div style="margin-top: 1rem; padding-left: 1rem; border-left: 2px solid #e2e8f0;">
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Materials ({{ $sectionTitle->materials->count() }})
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                            @foreach($sectionTitle->materials as $material)
                                                <div style="background: white; padding: 1rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0;">
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <i data-feather="file-text" style="width: 20px; height: 20px; color: #6366f1;"></i>
                                                        <div>
                                                            <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                                                {{ $material->materials_name }}
                                                            </div>
                                                            @if($material->materials_notes)
                                                                <div style="font-size: 0.85rem; color: #64748b;">
                                                                    {{ $material->materials_notes }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($material->file_path && $isEnrolled)
                                                        <a href="{{ route('materials.show', ['filename' => basename($material->file_path)]) }}" 
                                                           target="_blank" 
                                                           style="background: #6366f1; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                            <i data-feather="download" style="width: 16px; height: 16px;"></i>
                                                            Open
                                                        </a>
                                                    @elseif($material->file_path && !$isEnrolled)
                                                        <span style="background: #e2e8f0; color: #64748b; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: not-allowed;">
                                                            <i data-feather="lock" style="width: 16px; height: 16px;"></i>
                                                            Join to Access
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem; font-style: italic;">
                                        No materials in this section yet.
                                    </p>
                                @endif
                            </div>
                        @endforeach

                        {{-- Display Legacy Modules (for backward compatibility) --}}
                        @foreach($course->modules as $module)
                            <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.25rem; border-left: 4px solid #8b5cf6;">
                                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">
                                    {{ $module->modules_name }}
                                </h3>
                                @if($module->modules_description)
                                    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem;">
                                        {{ $module->modules_description }}
                                    </p>
                                @endif
                                
                                @if($module->materials->isNotEmpty())
                                    <div style="margin-top: 1rem; padding-left: 1rem; border-left: 2px solid #e2e8f0;">
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Materials ({{ $module->materials->count() }})
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                            @foreach($module->materials as $material)
                                                <div style="background: white; padding: 1rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid #e2e8f0;">
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <i data-feather="file-text" style="width: 20px; height: 20px; color: #8b5cf6;"></i>
                                                        <div>
                                                            <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                                                {{ $material->materials_name }}
                                                            </div>
                                                            @if($material->materials_notes)
                                                                <div style="font-size: 0.85rem; color: #64748b;">
                                                                    {{ $material->materials_notes }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($material->file_path && $isEnrolled)
                                                        <a href="{{ route('materials.show', ['filename' => basename($material->file_path)]) }}" 
                                                           target="_blank" 
                                                           style="background: #8b5cf6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                            <i data-feather="download" style="width: 16px; height: 16px;"></i>
                                                            Open
                                                        </a>
                                                    @elseif($material->file_path && !$isEnrolled)
                                                        <span style="background: #e2e8f0; color: #64748b; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; cursor: not-allowed;">
                                                            <i data-feather="lock" style="width: 16px; height: 16px;"></i>
                                                            Join to Access
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="modern-card" style="margin-top: 0;">
                <h2 class="modern-card-title mb-3" style="font-size: 1.1rem;">About the Educator</h2>

                @if($course->educator)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div style="width: 48px; height: 48px; border-radius: 999px; background: #e0e7ff; display:flex;align-items:center;justify-content:center;font-weight:700;color:#4f46e5;font-size:1.2rem;">
                                {{ strtoupper(substr($course->educator->name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a;">
                                {{ $course->educator->name }}
                            </div>
                            <div style="font-size: 0.9rem; color: #64748b;">
                                Educator
                            </div>
                        </div>
                    </div>
                    <p class="modern-card-text">
                        {{-- If you later add educator bio/profile fields, show them here --}}
                        This educator is part of the NextLearning community. Learners can join this course and learn directly from them.
                    </p>
                @else
                    <p class="text-muted mb-0">Educator information is not available for this course yet.</p>
                @endif

                <hr>

                @auth
                    @if(auth()->user()->hasRole('Learner'))
                        @if($isEnrolled)
                            <div class="alert alert-success mb-0">
                                You are enrolled in this course.
                            </div>
                        @else
                            <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    Join this course
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            You are logged in as an educator. Switch to a learner account to join this course.
                        </div>
                    @endif
                @else
                    <div class="alert alert-info mb-0">
                        Please <a href="{{ route('login') }}">log in</a> or <a href="{{ route('register') }}">register</a> as a learner to join this course.
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection


