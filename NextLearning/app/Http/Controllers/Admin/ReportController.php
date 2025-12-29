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
<<<<<<< HEAD
     * Admin Report: 
=======
     * =========================
     * ADMIN REPORT
     * =========================
>>>>>>> parent of 42a1092 (teacher report update)
     * - User & Role Distribution
     * - Teacher Workload
     * - Class Subject Assignment
     */
    public function adminReport()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        // ----- 1️⃣ User & Role Distribution -----
=======
>>>>>>> parent of 71ddf72 (update)
=======
        // 1️⃣ User & Role Distribution
>>>>>>> parent of 42a1092 (teacher report update)
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins   = User::role('admin')->count();

<<<<<<< HEAD
<<<<<<< HEAD
        // ----- 2️⃣ Teacher Workload -----
        $teachers = User::role('teacher')->get();
        $workload = $teachers->map(function($teacher) {
=======
=======
        // 2️⃣ Teacher Workload
>>>>>>> parent of 42a1092 (teacher report update)
        $teachers = User::role('teacher')->get();
        $workload = $teachers->map(function ($teacher) {
>>>>>>> parent of 71ddf72 (update)
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
<<<<<<< HEAD
                'totalAssignments' => $totalAssignments
=======
                'totalAssignments' => $totalAssignments,
            ];
        });

        // 3️⃣ Class Subject Assignment
        $classesData = Classes::all();
        $classSubjectAssignment = $classesData->map(function ($class) {
            $subjects = DB::table('subject_class_teacher')
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->join('users', 'subject_class_teacher.teacher_id', '=', 'users.id')
                ->where('subject_class_teacher.class_id', $class->id)
                ->select(
                    'subjects.name as subject_name',
                    'users.name as teacher_name'
                )
                ->get();

            return [
                'class_name' => $class->name,
                'subjects'   => $subjects,
>>>>>>> parent of 71ddf72 (update)
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
<<<<<<< HEAD
=======

    /**
     * =========================
     * TEACHER REPORT
     * =========================
     * - Own workload only
     * - Classes & subjects taught
     */
    public function teacherReport()
    {
        $teacher = auth()->user();

        // Teacher workload summary
        $classesCount = DB::table('subject_class_teacher')
            ->where('teacher_id', $teacher->id)
            ->distinct('class_id')
            ->count('class_id');

        $subjectsCount = DB::table('subject_class_teacher')
            ->where('teacher_id', $teacher->id)
            ->distinct('subject_id')
            ->count('subject_id');

        // Classes + subjects taught by this teacher
        $assignments = DB::table('subject_class_teacher')
            ->join('classes', 'subject_class_teacher.class_id', '=', 'classes.id')
            ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
            ->where('subject_class_teacher.teacher_id', $teacher->id)
            ->select(
                'classes.name as class_name',
                'subjects.name as subject_name'
            )
            ->get()
            ->groupBy('class_name');

        return view('pages.ManageReport.teacherreport', compact(
            'teacher',
            'classesCount',
            'subjectsCount',
            'assignments'
        ));
    }
<<<<<<< HEAD

    return view('pages.ManageReport.teacherreport', compact(
        'teacher',
        'classReports',
        'allSessions',
        'selectedSession'
    ));
}

/**
 * Export teacher report as CSV
 */
public function teacherReportExport(Request $request)
{
    $teacher = auth()->user();
    $type = $request->input('type', 'progress'); // 'progress' or 'students'
    $selectedSession = $request->input('session');

    $classesQuery = DB::table('subject_class_teacher')
        ->where('teacher_id', $teacher->id)
        ->join('classes', 'subject_class_teacher.class_id', '=', 'classes.id')
        ->select(
            'classes.id as class_id',
            'classes.name as class_name',
            'classes.form_level',
            'classes.academic_session'
        )
        ->distinct();

    if ($selectedSession) {
        $classesQuery->where('classes.academic_session', $selectedSession);
    }

    $classes = $classesQuery->get();
    $csvData = [];

    foreach ($classes as $class) {
        $students = DB::table('class_students')
            ->join('users', 'class_students.student_id', '=', 'users.id')
            ->where('class_students.class_id', $class->class_id)
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        if ($type === 'progress') {
            $subjects = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->where('class_id', $class->class_id)
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->select('subjects.id as subject_id', 'subjects.name as subject_name')
                ->get();

            foreach ($subjects as $subject) {
                $totalAssessments = DB::table('assessments')
                    ->where('subject_id', $subject->subject_id)
                    ->where('class_id', $class->class_id)
                    ->count();

                $completedSubmissions = DB::table('assessment_submissions')
                    ->join('assessments', 'assessment_submissions.assessment_id', '=', 'assessments.id')
                    ->where('assessments.subject_id', $subject->subject_id)
                    ->where('assessments.class_id', $class->class_id)
                    ->whereNotNull('assessment_submissions.submitted_at')
                    ->count();

                $totalPossible = $students->count() * $totalAssessments;
                $progress = $totalPossible > 0 ? round(($completedSubmissions / $totalPossible) * 100) : 0;

                $csvData[] = [
                    'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->class_name,
                    'Academic Session' => $class->academic_session,
                    'Subject' => $subject->subject_name,
                    'Total Assessments' => $totalAssessments,
                    'Completed Submissions' => $completedSubmissions,
                    'Progress %' => $progress,
                ];
            }

        } elseif ($type === 'students') {
            foreach ($students as $student) {
                $csvData[] = [
                    'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->class_name,
                    'Academic Session' => $class->academic_session,
                    'Student ID' => $student->id,
                    'Student Name' => $student->name,
                    'Student Email' => $student->email,
                ];
            }

            // if no students
            if ($students->isEmpty()) {
                $csvData[] = [
                    'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->class_name,
                    'Academic Session' => $class->academic_session,
                    'Student ID' => '',
                    'Student Name' => '',
                    'Student Email' => '',
                ];
            }
        }
    }

    $filename = 'teacher_report_' . $type . '_' . now()->format('Ymd_His') . '.csv';
    $handle = fopen('php://memory', 'w');

    if (!empty($csvData)) {
        fputcsv($handle, array_keys($csvData[0]));
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
    }

    rewind($handle);
    return response()->streamDownload(function () use ($handle) {
        fpassthru($handle);
    }, $filename);
}

>>>>>>> parent of 71ddf72 (update)
=======
>>>>>>> parent of 42a1092 (teacher report update)
}
