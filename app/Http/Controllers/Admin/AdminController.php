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

        // Admin Dashboard Data
        if ($user->hasRole('Admin')) {
            return $this->adminDashboard();
        }

        // Teacher Dashboard Data
        if ($user->hasRole('Teacher')) {
            return $this->teacherDashboard($user);
        }

        // Student Dashboard Data
        if ($user->hasRole('Student')) {
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

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.index');
        }

        if ($user->hasRole('Teacher')) {
            return redirect()->route('teacher.index');
        }

        if ($user->hasRole('Student')) {
            return redirect()->route('student.index');
        }

        // Fallback to admin route
        return redirect()->route('admin.index');
    }

    private function adminDashboard()
    {
        // Get user counts by role using explicit queries
        $studentCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'Student');
        })->count();

        $teacherCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teacher');
        })->count();

        $adminCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
        })->count();

        $totalUsers = User::count();

        // Get active classes count
        $activeClasses = Classes::count();

        // Get active subjects count
        $activeSubjects = Subjects::where('is_active', true)->count();

        // Get enrollment distribution by form level
        $enrollmentByForm = Classes::select('form_level', DB::raw('count(*) as class_count'))
            ->groupBy('form_level')
            ->pluck('class_count', 'form_level')
            ->toArray();

        // Get recent users (last 5 registered)
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        // Prepare stats array
        $stats = [
            'totalUsers' => $totalUsers,
            'students' => $studentCount,
            'teachers' => $teacherCount,
            'admins' => $adminCount,
            'activeClasses' => $activeClasses,
            'activeSubjects' => $activeSubjects,
        ];

        return view('component.dashboard.index', compact('stats', 'enrollmentByForm', 'recentUsers'));
    }

    private function teacherDashboard($user)
    {
        // Simple teacher dashboard - show classes where user is homeroom teacher
        $teacherClasses = Classes::where('homeroom_teacher_id', $user->id)->get();

        // Calculate total students
        $totalStudents = 0;
        foreach ($teacherClasses as $class) {
            $totalStudents += $class->activeStudents()->count();
        }

        // Teacher stats
        $teacherStats = [
            'totalStudents' => $totalStudents,
            'totalClasses' => $teacherClasses->count(),
            'totalSubjects' => 0, // Simplified for now
        ];

        return view('component.dashboard.index', compact('teacherStats', 'teacherClasses'));
    }

    private function studentDashboard($user)
    {
        // Get student's enrolled class
        $activeClass = $user->belongsToMany(Classes::class, 'class_students', 'student_id', 'class_id')
            ->wherePivot('status', 'active')
            ->first();

        // Get subjects for the student's class
        $enrolledSubjects = collect([]);
        if ($activeClass) {
            $subjects = $activeClass->subjects;
            $enrolledSubjects = $subjects ? collect($subjects) : collect([]);
        }

        // Student stats
        $studentStats = [
            'subjectsEnrolled' => $enrolledSubjects->count(),
            'className' => $activeClass ? ($activeClass->form_level . ' ' . ($activeClass->name ?? $activeClass->class_name)) : 'Not Assigned',
        ];

        return view('component.dashboard.index', compact('studentStats', 'enrolledSubjects', 'activeClass'));
    }
}
