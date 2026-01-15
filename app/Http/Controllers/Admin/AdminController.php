<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classes;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Educator Dashboard Data
        if ($user->hasRole('Educator')) {
            return $this->teacherDashboard($user);
        }

        // Learner Dashboard Data
        if ($user->hasRole('Learner')) {
            return $this->studentDashboard($user);
        }

        return view('component.dashboard.index');
    }

    /**
     * Redirect user to their role-specific dashboard
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->hasRole('Educator')) {
            return redirect()->route('teacher.index');
        }

        if ($user->hasRole('Learner')) {
            return redirect()->route('student.index');
        }

        // Fallback to educator route
        return redirect()->route('teacher.index');
    }

    private function teacherDashboard($user)
    {
        // Get classes where user is homeroom teacher
        $teacherClasses = Classes::where('homeroom_teacher_id', $user->id)->get();

        // Calculate total students
        $totalStudents = 0;
        foreach ($teacherClasses as $class) {
            $totalStudents += $class->activeStudents()->count();
        }

        // Get courses created by this educator
        $myCourses = Subjects::where('educator_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $totalCourses = Subjects::where('educator_id', $user->id)->count();
        $publishedCourses = Subjects::where('educator_id', $user->id)
            ->where('is_published', true)
            ->count();
        
        // Calculate total learners enrolled in educator's courses
        $totalEnrolledLearners = DB::table('course_enrollments')
            ->join('subjects', 'course_enrollments.subject_id', '=', 'subjects.id')
            ->where('subjects.educator_id', $user->id)
            ->where('course_enrollments.status', 'active')
            ->distinct('course_enrollments.learner_id')
            ->count('course_enrollments.learner_id');

        // Teacher stats
        $teacherStats = [
            'totalStudents' => $totalStudents,
            'totalClasses' => $teacherClasses->count(),
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'totalEnrolledLearners' => $totalEnrolledLearners,
        ];

        return view('component.dashboard.index', compact('teacherStats', 'teacherClasses', 'myCourses'));
    }

    private function studentDashboard($user)
    {
        // Get student's enrolled class
        $activeClass = $user->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id')
            ->wherePivot('status', 'active')
            ->first();

        // Get enrolled courses (from course_enrollments)
        $enrolledCourses = $user->enrolledCourses()
            ->where('is_active', true)
            ->where('is_published', true)
            ->with('educator')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $totalEnrolledCourses = $user->enrolledCourses()
            ->where('is_active', true)
            ->where('is_published', true)
            ->count();

        // Student stats
        $studentStats = [
            'coursesEnrolled' => $totalEnrolledCourses,
            'className' => $activeClass ? ($activeClass->form_level . ' ' . ($activeClass->name ?? $activeClass->class_name)) : 'Not Assigned',
        ];

        return view('component.dashboard.index', compact('studentStats', 'enrolledCourses', 'activeClass'));
    }
}
