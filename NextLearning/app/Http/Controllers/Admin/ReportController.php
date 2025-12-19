<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classes;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    /**
     * Admin Report: 
     * - User & Role Distribution
     * - Teacher Workload
     * - Class Subject Assignment
     */
    public function adminReport()
    {
        // ----- 1️⃣ User & Role Distribution -----
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins   = User::role('admin')->count();

        // ----- 2️⃣ Teacher Workload -----
        $teachers = User::role('teacher')->get();
        $workload = $teachers->map(function($teacher) {
            $classes = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->distinct('class_id')
                ->count('class_id');

            $subjects = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->distinct('subject_id')
                ->count('subject_id');

            $totalAssignments = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->count();

            return [
                'teacher' => $teacher->name,
                'classes' => $classes,
                'subjects' => $subjects,
                'totalAssignments' => $totalAssignments
            ];
        });

        // ----- 3️⃣ Class Subject Assignment -----
        $classesData = Classes::all();
        $classSubjectAssignment = $classesData->map(function($class) {
    $subjects = DB::table('subject_class_teacher')
        ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
        ->join('users', 'subject_class_teacher.teacher_id', '=', 'users.id')
        ->where('subject_class_teacher.class_id', $class->id)
        ->select('subjects.name as subject_name', 'users.name as teacher_name')
        ->get();

    return [
        'class_name' => $class->name, // <-- use correct column
        'subjects' => $subjects
    ];
});


        // Pass all data to the Blade view
        return view('pages.ManageReport.adminreport', compact(
            'totalStudents',
            'totalTeachers',
            'totalAdmins',
            'workload',
            'classSubjectAssignment'
        ));
    }
}
