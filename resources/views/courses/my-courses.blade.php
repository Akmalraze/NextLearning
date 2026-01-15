@extends('layouts.master')

@section('content')
<div class="container py-5" style="max-width: 1400px;">
    <div class="text-center mb-5">
        <h1 class="mb-2" style="font-weight: 800; color: #0f172a; font-size: 2.5rem;">My Courses</h1>
        <p class="text-muted mb-0" style="font-size: 1.1rem;">Your enrolled courses - continue learning and access your materials</p>
    </div>

    <form method="GET" action="{{ route('courses.my-courses') }}" class="mb-5">
        <div class="row g-2 justify-content-center">
            <div class="col-md-6">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control form-control-lg"
                       placeholder="Search your courses by name, instructor, or description"
                       style="border-radius: 0.75rem; border: 2px solid #e2e8f0; padding: 0.75rem 1.25rem;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-lg px-4" style="border-radius: 0.75rem;">
                    <i data-feather="search" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i> Search
                </button>
            </div>
        </div>
    </form>

    @if($courses->isEmpty())
        <div class="text-center py-5">
            <div style="background: white; border-radius: 1rem; padding: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <i data-feather="book-open" style="width: 64px; height: 64px; color: #94a3b8; margin-bottom: 1.5rem;"></i>
                <h3 style="color: #1e293b; margin-bottom: 1rem;">You haven't enrolled in any courses yet</h3>
                <p class="text-muted mb-3">Start exploring and join courses that interest you!</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg" style="border-radius: 0.75rem;">
                    <i data-feather="compass" style="width: 18px; height: 18px; margin-right: 0.5rem;"></i>
                    Browse Courses
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-lg-4 col-md-6">
                    <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); height: 100%; display: flex; flex-direction: column; transition: transform 0.3s, box-shadow 0.3s; border-left: 4px solid #10b981;" 
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'">
                        
                        <!-- Course Header -->
                        <div style="margin-bottom: 1rem;">
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; line-height: 1.3;">
                                {{ $course->name }}
                            </h3>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600;">
                                    {{ $course->code }}
                                </span>
                                <span style="background: #f1f5f9; color: #64748b; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500;">
                                    {{ $course->learners()->count() }} {{ $course->learners()->count() === 1 ? 'learner' : 'learners' }}
                                </span>
                                <span style="background: #10b981; color: white; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                    Enrolled
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div style="flex: 1; margin-bottom: 1.5rem;">
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin: 0; min-height: 3rem;">
                                {{ $course->description ? \Illuminate\Support\Str::limit($course->description, 120) : 'No description provided yet.' }}
                            </p>
                        </div>

                        <!-- Instructor Info -->
                        <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                            <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.5px;">
                                Instructor
                            </div>
                            @if($course->educator)
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 1.1rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($course->educator->name, 0, 1)) }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 1rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $course->educator->name }}
                                        </div>
                                        <div style="font-size: 0.875rem; color: #64748b;">
                                            Educator
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div style="font-size: 0.9rem; color: #94a3b8;">
                                    Instructor not assigned
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 0.75rem;">
                            <a href="{{ route('courses.show', $course) }}" style="flex: 1; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; text-align: center; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: transform 0.2s;" 
                               onmouseover="this.style.transform='translateY(-2px)'" 
                               onmouseout="this.style.transform='translateY(0)'">
                                <i data-feather="book-open" style="width: 18px; height: 18px;"></i>
                                Open Course
                            </a>
                            <form method="POST" action="{{ route('courses.unenroll', $course) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to unenroll from {{ $course->name }}? You will lose access to course materials.');">
                                @csrf
                                <button type="submit" style="background: #ef4444; color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: transform 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap;" 
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.background='#dc2626'" 
                                        onmouseout="this.style.transform='translateY(0)'; this.style.background='#ef4444'">
                                    <i data-feather="x-circle" style="width: 18px; height: 18px;"></i>
                                    Unenroll
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $courses->links() }}
        </div>
    @endif
</div>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>
@endsection

