<?php

namespace App\Http\Controllers;

use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Public course catalog: list all published courses with educator info.
     */
    public function index(Request $request)
    {
        $query = Subjects::with(['educator', 'modules', 'learners'])
            ->where('is_active', true)
            ->where('is_published', true)
            ->orderBy('name');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('educator', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $courses = $query->paginate(12)->withQueryString();

        return view('courses.index', compact('courses', 'search'));
    }

    /**
     * Course detail page: show course info, educator, section titles, and materials.
     */
    public function show(Subjects $subject)
    {
        $subject->load([
            'educator', 
            'sectionTitles.materials',
            'modules.materials' // Keep for backward compatibility
        ]);

        $user = Auth::user();
        $isEnrolled = false;

        if ($user && $user->hasRole('Learner')) {
            $isEnrolled = $subject->learners()
                ->where('users.id', $user->id)
                ->exists();
        }

        return view('courses.show', [
            'course' => $subject,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    /**
     * Learner enrolls in a course.
     */
    public function enroll(Subjects $subject)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Learner')) {
            abort(403, 'Only learners can join courses.');
        }

        if (!$subject->is_active || !$subject->is_published) {
            abort(403, 'This course is not available for enrollment.');
        }

        $subject->learners()->syncWithoutDetaching([
            $user->id => ['status' => 'active'],
        ]);

        return redirect()
            ->route('courses.show', $subject)
            ->with('success', 'You have joined this course!');
    }

    /**
     * My Courses: show only courses the learner has enrolled in.
     */
    public function myCourses(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Learner')) {
            abort(403, 'Only learners can view their enrolled courses.');
        }

        $search = $request->get('q');

        // Get enrolled courses
        $query = $user->enrolledCourses()
            ->with(['educator', 'sectionTitles.materials', 'modules.materials'])
            ->where('is_active', true)
            ->where('is_published', true)
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('educator', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $courses = $query->paginate(12)->withQueryString();

        return view('courses.my-courses', compact('courses', 'search'));
    }

    /**
     * Learner unenrolls from a course.
     */
    public function unenroll(Subjects $subject)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Learner')) {
            abort(403, 'Only learners can unenroll from courses.');
        }

        // Check if user is actually enrolled
        $isEnrolled = $subject->learners()
            ->where('users.id', $user->id)
            ->wherePivot('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            flash()->addError('You are not enrolled in this course.');
            return redirect()->route('courses.my-courses');
        }

        // Remove enrollment
        $subject->learners()->detach($user->id);

        flash()->addSuccess('You have successfully unenrolled from ' . $subject->name . '.');
        return redirect()->route('courses.my-courses');
    }
}


