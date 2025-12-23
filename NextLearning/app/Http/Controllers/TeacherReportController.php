<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TeacherReportController extends Controller
{
    /* =========================
       TEACHER REPORT
    ========================== */
    public function teacherReport(Request $request)
    {
        $teacher = auth()->user();
        $selectedSession = $request->input('session');
        $search = strtolower($request->input('search', ''));

        $allSessions = DB::table('classes')->distinct()->pluck('academic_session');

        $classesQuery = DB::table('subject_class_teacher')
            ->where('teacher_id', $teacher->id)
            ->join('classes', 'subject_class_teacher.class_id', '=', 'classes.id')
            ->select(
                'classes.id',
                'classes.name',
                'classes.form_level',
                'classes.academic_session'
            )
            ->distinct();

        if ($selectedSession) {
            $classesQuery->where('classes.academic_session', $selectedSession);
        }

        $classes = $classesQuery->get();
        $classReports = [];

        foreach ($classes as $class) {
            // Students
            $studentsQuery = DB::table('class_students')
                ->join('users', 'class_students.student_id', '=', 'users.id')
                ->where('class_students.class_id', $class->id)
                ->select('users.id', 'users.name', 'users.email')
                ->distinct();

            if ($search) {
                $studentsQuery->whereRaw('LOWER(users.name) LIKE ?', ["%$search%"]);
            }

            $students = $studentsQuery->get();

            // Subjects
            $subjectsQuery = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->where('class_id', $class->id)
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->select('subjects.id as subject_id', 'subjects.name as subject_name');

            if ($search) {
                $subjectsQuery->whereRaw('LOWER(subjects.name) LIKE ?', ["%$search%"]);
            }

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
                        ->where('class_id', $class->id)
                        ->count();

                    $completedSubmissions = DB::table('assessment_submissions')
                        ->join('assessments', 'assessment_submissions.assessment_id', '=', 'assessments.id')
                        ->where('assessments.subject_id', $subject->subject_id)
                        ->where('assessments.class_id', $class->id)
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
                'class_id' => $class->id,
                'class_name' => 'Form ' . $class->form_level . ' - ' . $class->name,
                'subjects' => $subjectsArray,
                'students' => $students,
                'academic_session' => $class->academic_session
            ];
        }

        return view('pages.ManageReport.teacherreport', compact(
            'teacher',
            'classReports',
            'allSessions',
            'selectedSession'
        ));
    }

    /* =========================
       TEACHER REPORT EXPORT
       (CSV MATCHING TABS)
    ========================== */
    public function teacherReportExport(Request $request)
    {
        $teacher = auth()->user();
        $type = $request->input('type', 'progress'); // progress | students
        $classIds = $request->input('class_ids', []);
        $search = strtolower($request->input('search', ''));

        if (empty($classIds)) {
            return redirect()->back()->with('error', 'No class selected for export.');
        }

        $csvData = [];

        foreach ($classIds as $classId) {
            $class = DB::table('classes')->where('id', $classId)->first();
            if (!$class) continue;

            // Students
            $students = DB::table('class_students')
                ->join('users', 'class_students.student_id', '=', 'users.id')
                ->where('class_students.class_id', $classId)
                ->when($search, fn($q) => $q->whereRaw('LOWER(users.name) LIKE ?', ["%$search%"]))
                ->select('users.id', 'users.name', 'users.email')
                ->distinct()
                ->get();

            // Subjects
            $subjects = DB::table('subject_class_teacher')
                ->where('teacher_id', $teacher->id)
                ->where('class_id', $classId)
                ->join('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                ->when($search, fn($q) => $q->whereRaw('LOWER(subjects.name) LIKE ?', ["%$search%"]))
                ->select('subjects.id as subject_id', 'subjects.name as subject_name')
                ->get();

            // Export students
            if ($type === 'students') {
                if ($students->isEmpty()) {
                    $csvData[] = [
                        'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->name,
                        'Academic Session' => $class->academic_session,
                        'Student ID' => '',
                        'Student Name' => '',
                        'Student Email' => '',
                    ];
                } else {
                    foreach ($students as $student) {
                        $csvData[] = [
                            'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->name,
                            'Academic Session' => $class->academic_session,
                            'Student ID' => $student->id,
                            'Student Name' => $student->name,
                            'Student Email' => $student->email,
                        ];
                    }
                }
            }

            // Export progress
            if ($type === 'progress') {
                if ($subjects->isEmpty()) {
                    $csvData[] = [
                        'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->name,
                        'Academic Session' => $class->academic_session,
                        'Subject' => 'No subjects assigned',
                        'Total Assessments' => 0,
                        'Completed Submissions' => 0,
                        'Progress %' => 0,
                    ];
                } else {
                    foreach ($subjects as $subject) {
                        $totalAssessments = DB::table('assessments')
                            ->where('subject_id', $subject->subject_id)
                            ->where('class_id', $classId)
                            ->count();

                        $completedSubmissions = DB::table('assessment_submissions')
                            ->join('assessments', 'assessment_submissions.assessment_id', '=', 'assessments.id')
                            ->where('assessments.subject_id', $subject->subject_id)
                            ->where('assessments.class_id', $classId)
                            ->whereNotNull('assessment_submissions.submitted_at')
                            ->count();

                        $totalPossible = $students->count() * $totalAssessments;
                        $progress = $totalPossible > 0 ? round(($completedSubmissions / $totalPossible) * 100) : 0;

                        $csvData[] = [
                            'Form/Class' => 'Form ' . $class->form_level . ' - ' . $class->name,
                            'Academic Session' => $class->academic_session,
                            'Subject' => $subject->subject_name,
                            'Total Assessments' => $totalAssessments,
                            'Completed Submissions' => $completedSubmissions,
                            'Progress %' => $progress,
                        ];
                    }
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
}
