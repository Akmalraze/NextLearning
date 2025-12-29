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
=======
     * =========================
     * ADMIN REPORT
     * =========================
     */
    public function adminReport(Request $request)
    {
        $search = strtolower($request->input('search', ''));

>>>>>>> parent of 694f6f0 (update)
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins   = User::role('admin')->count();

<<<<<<< HEAD
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
=======
        // Teacher Workload
        $teachers = User::role('teacher')->get();
        $workload = $teachers->map(function ($teacher) use ($search) {
>>>>>>> parent of 694f6f0 (update)
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

<<<<<<< HEAD
=======
            if ($search && !str_contains(strtolower($teacher->name), $search)) {
                return null;
            }

>>>>>>> parent of 694f6f0 (update)
            return [
                'teacher' => $teacher->name,
                'classes' => $classes,
                'subjects' => $subjects,
<<<<<<< HEAD
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
=======
                'totalAssignments' => $totalAssignments,
            ];
        })->filter();

        // Class Subject Assignment
        $classesData = Classes::all();
        $classSubjectAssignment = $classesData->map(function ($class) use ($search) {
            $subjects = DB::table('subject_class_teacher')
                ->leftJoin('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->leftJoin('users', 'subject_class_teacher.teacher_id', '=', 'users.id')
>>>>>>> parent of 694f6f0 (update)
                ->where('subject_class_teacher.class_id', $class->id)
                ->select(
                    'subjects.name as subject_name',
                    'users.name as teacher_name'
                )
                ->get();

<<<<<<< HEAD
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
=======
            if ($search) {
                $subjects = $subjects->filter(function ($sub) use ($search, $class) {
                    return str_contains(strtolower($class->name), $search) ||
                           str_contains(strtolower($sub->subject_name), $search) ||
                           str_contains(strtolower($sub->teacher_name ?? ''), $search);
                });
            }

            return [
                'class_name' => $class->name,
                'subjects'   => $subjects,
            ];
        });

>>>>>>> parent of 694f6f0 (update)
        return view('pages.ManageReport.adminreport', compact(
            'totalStudents',
            'totalTeachers',
            'totalAdmins',
            'workload',
            'classSubjectAssignment'
        ));
    }
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======

    /**
     * =========================
     * ADMIN REPORT EXPORT (CSV)
     * =========================
     */
    public function adminReportExport(Request $request)
    {
        $type = $request->input('type'); // workload | class-subject
        $search = strtolower($request->input('search', ''));
        $csvData = [];

        // Workload
        if ($type === 'workload') {
            $teachers = User::role('teacher')->get();
            foreach ($teachers as $teacher) {
                if ($search && !str_contains(strtolower($teacher->name), $search)) continue;

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

                $csvData[] = [
                    'Teacher Name' => $teacher->name,
                    'Classes Assigned' => $classes,
                    'Subjects Assigned' => $subjects,
                    'Total Assignments' => $totalAssignments,
                ];
            }
        }

        // Class-Subject
        if ($type === 'class-subject') {
            $classes = Classes::all();
            foreach ($classes as $class) {
                $subjects = DB::table('subject_class_teacher')
                    ->leftJoin('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                    ->leftJoin('users', 'subject_class_teacher.teacher_id', '=', 'users.id')
                    ->where('subject_class_teacher.class_id', $class->id)
                    ->select('subjects.name as subject_name', 'users.name as teacher_name')
                    ->get();

                if ($subjects->isEmpty()) {
                    if ($search && !str_contains(strtolower($class->name), $search)) continue;
                    $csvData[] = [
                        'Class' => $class->name,
                        'Subject' => '',
                        'Teacher' => '',
                        'Status' => 'No subjects assigned',
                    ];
                }

                foreach ($subjects as $subject) {
                    if ($search && !(
                        str_contains(strtolower($class->name), $search) ||
                        str_contains(strtolower($subject->subject_name), $search) ||
                        str_contains(strtolower($subject->teacher_name ?? ''), $search)
                    )) continue;

                    $csvData[] = [
                        'Class' => $class->name,
                        'Subject' => $subject->subject_name,
                        'Teacher' => $subject->teacher_name ?? '',
                        'Status' => $subject->teacher_name ? 'Assigned' : 'Unassigned',
                    ];
                }
            }
        }

        $filename = 'admin_report_' . $type . '_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://memory', 'w');

        if (!empty($csvData)) {
            fputcsv($handle, array_keys($csvData[0]));
            foreach ($csvData as $row) fputcsv($handle, $row);
        }

        rewind($handle);
        return response()->streamDownload(fn() => fpassthru($handle), $filename);
    }
>>>>>>> parent of 694f6f0 (update)

    /**
     * =========================
     * TEACHER REPORT
     * =========================
<<<<<<< HEAD
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
=======
     */
    public function teacherReport(Request $request)
    {
        $teacher = auth()->user();
        $selectedSession = $request->input('session');
        $search = strtolower($request->input('search', ''));

        // All sessions
        $allSessions = DB::table('classes')->distinct()->pluck('academic_session');

        // Classes assigned
        $classesQuery = DB::table('subject_class_teacher')
            ->where('teacher_id', $teacher->id)
            ->join('classes', 'subject_class_teacher.class_id', '=', 'classes.id')
            ->select('classes.id as class_id', 'classes.name as class_name', 'classes.form_level', 'classes.academic_session')
            ->distinct();

        if ($selectedSession) {
            $classesQuery->where('classes.academic_session', $selectedSession);
        }

        $classes = $classesQuery->get();
        $classReports = [];

        foreach ($classes as $class) {
            // Students only in subjects teacher teaches
            $studentsQuery = DB::table('class_students')
                ->join('users', 'class_students.student_id', '=', 'users.id')
                ->join('subject_class_teacher as sct', function($join) use ($teacher, $class) {
                    $join->on('class_students.class_id', '=', 'sct.class_id')
                         ->where('sct.teacher_id', $teacher->id)
                         ->where('sct.class_id', $class->class_id);
                })
                ->select('users.id', 'users.name', 'users.email')
                ->distinct();

            if ($search) $studentsQuery->whereRaw('LOWER(users.name) LIKE ?', ["%$search%"]);

            $students = $studentsQuery->get();

            // Subjects teacher teaches
            $subjectsQuery = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->where('class_id', $class->class_id)
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->select('subjects.id as subject_id', 'subjects.name as subject_name');

            if ($search) $subjectsQuery->whereRaw('LOWER(subjects.name) LIKE ?', ["%$search%"]);

            $subjects = $subjectsQuery->get();

            $subjectsArray = [];
            if ($subjects->isEmpty()) {
                $subjectsArray[] = [
                    'subject_name' => 'No subjects assigned',
                    'total' => 0,
                    'completed' => 0,
                    'progress' => 0
                ];
            } else {
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

                    $subjectsArray[] = [
                        'subject_name' => $subject->subject_name,
                        'total' => $totalAssessments,
                        'completed' => $completedSubmissions,
                        'progress' => $progress
                    ];
                }
            }

            $classReports[] = [
                'class_name' => 'Form ' . $class->form_level . ' - ' . $class->class_name,
                'subjects' => $subjectsArray,
                'students' => $students,
                'academic_session' => $class->academic_session
            ];
        }

        return view('pages.ManageReport.teacherreport', compact('teacher','classReports','allSessions','selectedSession'));
    }

    /**
     * =========================
     * TEACHER REPORT EXPORT (CSV)
     * =========================
     */
    public function teacherReportExport(Request $request)
    {
        $teacher = auth()->user();
        $type = $request->input('type', 'progress'); // progress | students
        $selectedSession = $request->input('session');
        $search = strtolower($request->input('search', ''));

        // Classes assigned
        $classesQuery = DB::table('subject_class_teacher')
            ->where('teacher_id', $teacher->id)
            ->join('classes', 'subject_class_teacher.class_id', '=', 'classes.id')
            ->select('classes.id as class_id', 'classes.name as class_name', 'classes.form_level', 'classes.academic_session')
            ->distinct();

        if ($selectedSession) {
            $classesQuery->where('classes.academic_session', $selectedSession);
        }

        $classes = $classesQuery->get();
        $csvData = [];

        foreach ($classes as $class) {
            // Students only in teacher's subjects
            $studentsQuery = DB::table('class_students')
                ->join('users', 'class_students.student_id', '=', 'users.id')
                ->join('subject_class_teacher as sct', function($join) use ($teacher, $class) {
                    $join->on('class_students.class_id', '=', 'sct.class_id')
                         ->where('sct.teacher_id', $teacher->id)
                         ->where('sct.class_id', $class->class_id);
                })
                ->select('users.id', 'users.name', 'users.email')
                ->distinct();

            if ($search) $studentsQuery->whereRaw('LOWER(users.name) LIKE ?', ["%$search%"]);
            $students = $studentsQuery->get();

            // Subjects teacher teaches
            $subjectsQuery = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->where('class_id', $class->class_id)
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->select('subjects.id as subject_id', 'subjects.name as subject_name');

            if ($search) $subjectsQuery->whereRaw('LOWER(subjects.name) LIKE ?', ["%$search%"]);
            $subjects = $subjectsQuery->get();

            if ($type === 'students') {
                foreach ($students as $student) {
                    $csvData[] = [
                        'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->class_name,
                        'Academic Session' => $class->academic_session,
                        'Student ID' => $student->id,
                        'Student Name' => $student->name,
                        'Student Email' => $student->email,
                    ];
                }

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

            if ($type === 'progress') {
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
            }
        }

        $filename = 'teacher_report_' . $type . '_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://memory', 'w');

        if (!empty($csvData)) {
            fputcsv($handle, array_keys($csvData[0]));
            foreach ($csvData as $row) fputcsv($handle, $row);
        }

        rewind($handle);
        return response()->streamDownload(fn() => fpassthru($handle), $filename);
    }
>>>>>>> parent of 694f6f0 (update)
}
