{{-- Student Dashboard Component --}}

<!-- Welcome Section -->
<div class="mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 1rem; padding: 2rem; color: white; box-shadow: 0 10px 15px rgba(0,0,0,0.1);">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-2" style="font-weight: 700;">Welcome back, {{ auth()->user()->name }}!</h3>
            <p class="mb-0" style="opacity: 0.9;">Browse courses, choose your educator, and start learning.</p>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ route('courses.my-courses') }}" class="btn btn-light btn-sm" style="font-weight: 600; color: #4f46e5;">
                    <i data-feather="book" style="width: 16px; height: 16px; margin-right: 4px;"></i>
                    My Courses
                </a>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-sm" style="font-weight: 600;">
                    <i data-feather="compass" style="width: 16px; height: 16px; margin-right: 4px;"></i>
                    Browse Courses
                </a>
            </div>
        </div>
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 1rem; display: flex; align-items: center; justify-content: center;">
            <i data-feather="graduation-cap" style="width: 40px; height: 40px; color: white;"></i>
        </div>
    </div>
</div>

<!-- Class Info Banner -->
@if(isset($activeClass))
<div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 1rem; padding: 1.5rem; color: white; margin-bottom: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 style="margin: 0 0 0.5rem 0; font-weight: 700; display: flex; align-items: center;">
                <i data-feather="book-open" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
                {{ $studentStats['className'] ?? 'Not Assigned' }}
            </h5>
            <small style="opacity: 0.9;">Academic Session: {{ $activeClass->academic_session ?? 'N/A' }}</small>
        </div>
        <div>
            @if($activeClass->homeroomTeacher)
            <div style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.5rem;">
                <small style="display: flex; align-items: center;">
                    <i data-feather="user" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i>
                    Homeroom: {{ $activeClass->homeroomTeacher->name }}
                </small>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Key Metric Cards -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 4px solid #6366f1; height: 100%;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">Courses Enrolled</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $studentStats['coursesEnrolled'] ?? 0 }}</div>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="book" style="width: 28px; height: 28px; color: white;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrolled Courses Section -->
<div style="background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0; font-weight: 700; color: #1e293b;">
            <i data-feather="book" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
            My Enrolled Courses
        </h5>
        <a href="{{ route('courses.my-courses') }}" style="color: #6366f1; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            View All <i data-feather="arrow-right" style="width: 16px; height: 16px; margin-left: 0.25rem;"></i>
        </a>
    </div>
    <div style="padding: 1.5rem;">
        @if(isset($enrolledCourses) && $enrolledCourses->count() > 0)
        <div class="row">
            @foreach($enrolledCourses as $course)
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background: white; border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 1.5rem; height: 100%; transition: all 0.3s; border-left: 4px solid #10b981;">
                    <h6 style="font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center;">
                        <i data-feather="book" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                        {{ $course->name }}
                    </h6>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                        <span style="background: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                            {{ $course->code }}
                        </span>
                        <span style="background: #e0e7ff; color: #6366f1; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i data-feather="check-circle" style="width: 12px; height: 12px;"></i>
                            Enrolled
                        </span>
                    </div>
                    @if($course->description)
                    <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.5;">
                        {{ Str::limit($course->description, 80) }}
                    </p>
                    @endif
                    @if($course->educator)
                    <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center;">
                        <i data-feather="user" style="width: 14px; height: 14px; margin-right: 0.5rem;"></i>
                        {{ $course->educator->name }}
                    </p>
                    @endif
                    <a href="{{ route('courses.show', $course) }}" style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; transition: all 0.3s; width: 100%; text-align: center;">
                        <i data-feather="book-open" style="width: 14px; height: 14px; margin-right: 0.25rem;"></i> Open Course
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 3rem; color: #64748b;">
            <i data-feather="inbox" style="width: 64px; height: 64px; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p style="margin: 0 0 1rem 0; font-size: 1.125rem;">No courses enrolled yet</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary" style="border-radius: 0.5rem;">
                <i data-feather="compass" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i>
                Browse Courses
            </a>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
