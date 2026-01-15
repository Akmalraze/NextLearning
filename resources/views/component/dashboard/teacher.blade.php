{{-- Teacher Dashboard Component --}}

<!-- Welcome Section -->
<div class="mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 1rem; padding: 2rem; color: white; box-shadow: 0 10px 15px rgba(0,0,0,0.1);">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-2" style="font-weight: 700;">Welcome back, {{ auth()->user()->name }}!</h3>
            <p class="mb-0" style="opacity: 0.9;">Create and publish courses, upload resources, and manage your learners.</p>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ route('teacher.subjects.create') }}" class="btn btn-light btn-sm" style="font-weight: 600; color: #4f46e5;">
                    <i data-feather="plus-circle" style="width: 16px; height: 16px; margin-right: 4px;"></i>
                    Create new course
                </a>
                <a href="{{ route('teacher.subjects.index') }}" class="btn btn-outline-light btn-sm" style="font-weight: 600;">
                    <i data-feather="book-open" style="width: 16px; height: 16px; margin-right: 4px;"></i>
                    Manage my courses
                </a>
            </div>
        </div>
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
            <i data-feather="book-open" style="width: 40px; height: 40px; color: white;"></i>
        </div>
    </div>
</div>

<!-- Key Metric Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #3b82f6; height: 100%; transition: transform 0.3s;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">My Courses</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $teacherStats['totalCourses'] ?? 0 }}</div>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="book" style="width: 28px; height: 28px; color: white;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #8b5cf6; height: 100%; transition: transform 0.3s;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Published</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $teacherStats['publishedCourses'] ?? 0 }}</div>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="eye" style="width: 28px; height: 28px; color: white;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #f59e0b; height: 100%; transition: transform 0.3s;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Enrolled Learners</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $teacherStats['totalEnrolledLearners'] ?? 0 }}</div>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="users" style="width: 28px; height: 28px; color: white;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Courses Section -->
<div style="background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0; font-weight: 700; color: #1e293b;">
            <i data-feather="book" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
            My Courses
        </h5>
        <a href="{{ route('teacher.subjects.index') }}" style="color: #6366f1; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            View All <i data-feather="arrow-right" style="width: 16px; height: 16px; margin-left: 0.25rem;"></i>
        </a>
    </div>
    <div style="padding: 1.5rem;">
        @if(isset($myCourses) && $myCourses->count() > 0)
        <div class="row">
            @foreach($myCourses as $course)
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background: white; border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 1.5rem; height: 100%; transition: all 0.3s; border-left: 4px solid {{ $course->is_published ? '#10b981' : '#f59e0b' }};">
                    <h6 style="font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center;">
                        <i data-feather="book" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        {{ $course->name }}
                    </h6>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                        <span style="background: #6366f1; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                            {{ $course->code }}
                        </span>
                        @if($course->is_published)
                        <span style="background: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                            Published
                        </span>
                        @else
                        <span style="background: #f59e0b; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                            Draft
                        </span>
                        @endif
                    </div>
                    <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center;">
                        <i data-feather="users" style="width: 14px; height: 14px; margin-right: 0.5rem;"></i>
                        {{ $course->learners()->count() }} learners enrolled
                    </p>
                    <a href="{{ route('teacher.subjects.show', $course->id) }}"
                        style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; transition: all 0.3s; width: 100%; text-align: center;">
                        <i data-feather="settings" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i> Manage Course
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 3rem; color: #64748b;">
            <i data-feather="inbox" style="width: 64px; height: 64px; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p style="margin: 0 0 1rem 0; font-size: 1.125rem;">No courses created yet</p>
            <a href="{{ route('teacher.subjects.create') }}" class="btn btn-primary" style="border-radius: 0.5rem;">
                <i data-feather="plus-circle" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                Create Your First Course
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Class Management Section -->
@if(isset($teacherClasses) && $teacherClasses->count() > 0)
<div style="background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h5 style="margin: 0; font-weight: 700; color: #1e293b;">
            <i data-feather="book-open" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
            My Classes
        </h5>
    </div>
    <div style="padding: 1.5rem;">
        <div class="row">
            @foreach($teacherClasses as $class)
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background: white; border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 1.5rem; height: 100%; transition: all 0.3s; border-left: 4px solid {{ ['#6366f1', '#10b981', '#3b82f6', '#f59e0b', '#ef4444'][$loop->index % 5] }};">
                    <h6 style="font-weight: 700; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center;">
                        <i data-feather="book-open" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        {{ $class->form_level ?? '' }} {{ $class->name ?? $class->class_name }}
                    </h6>
                    <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.75rem; display: flex; align-items: center;">
                        <i data-feather="calendar" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                        {{ $class->academic_session ?? 'N/A' }}
                    </p>
                    <div style="margin-bottom: 1rem;">
                        <span style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center;">
                            <i data-feather="users" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i>
                            {{ $class->activeStudents()->count() }} Students
                        </span>
                    </div>
                    <a href="{{ route('teacher.classes.show', $class->id) }}"
                        style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; transition: all 0.3s;">
                        <i data-feather="eye" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i> View Class
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
