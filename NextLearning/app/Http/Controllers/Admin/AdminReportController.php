<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classes;
use App\Http\Controllers\Controller;

class AdminReportController extends Controller
{
    /* =========================
       ADMIN REPORT
    ========================== */
    public function adminReport(Request $request)
    {
        $search = strtolower($request->input('search', ''));

        /* ROLE COUNTS */
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins   = User::role('admin')->count();

        /* TEACHER WORKLOAD */
        $teachers = User::role('teacher')->get();
        $workload = $teachers->map(function ($teacher) use ($search) {
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

            if ($search && !str_contains(strtolower($teacher->name), $search)) {
                return null;
            }

            return [
                'teacher' => $teacher->name,
                'classes' => $classes,
                'subjects' => $subjects,
                'totalAssignments' => $totalAssignments,
            ];
        })->filter();

        /* CLASS SUBJECT REPORT */
        $allClasses = Classes::orderBy('form_level')->orderBy('name')->get();
        $selectedClassId = $request->input('class_id');
        $classSubjectAssignment = null;

        if ($selectedClassId) {
            $class = Classes::findOrFail($selectedClassId);

            $subjects = DB::table('subjects')
                ->leftJoin('subject_class_teacher as sct', function ($join) use ($selectedClassId) {
                    $join->on('subjects.id', '=', 'sct.subject_id')
                         ->where('sct.class_id', $selectedClassId);
                })
                ->leftJoin('users', 'sct.teacher_id', '=', 'users.id')
                ->select(
                    'subjects.name as subject_name',
                    'users.name as teacher_name'
                )
                ->get();

            $classSubjectAssignment = [
                'class_name' => 'Form ' . $class->form_level . ' - ' . $class->name,
                'subjects' => $subjects
            ];
        }

        return view('pages.ManageReport.adminreport', compact(
            'totalStudents',
            'totalTeachers',
            'totalAdmins',
            'workload',
            'allClasses',
            'selectedClassId',
            'classSubjectAssignment'
        ));
    }

    /* =========================
   ADMIN REPORT EXPORT
   (JSON for JS-triggered download)
========================== */
public function adminReportExport(Request $request)
{
    $type = $request->input('type'); // workload | class-subject
    $search = strtolower($request->input('search', ''));
    $csvData = [];

    // EXPORT TEACHER WORKLOAD
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

    // EXPORT CLASS-SUBJECT ASSIGNMENT (Filtered by class + search)
    if ($type === 'class-subject') {
        $selectedClassId = $request->input('class_id');

        if ($selectedClassId) {
            $class = Classes::find($selectedClassId);
            if ($class) {
                $subjects = DB::table('subject_class_teacher')
                    ->leftJoin('subjects', 'subject_class_teacher.subject_id', '=', 'subjects.id')
                    ->leftJoin('users', 'subject_class_teacher.teacher_id', '=', 'users.id')
                    ->where('subject_class_teacher.class_id', $class->id)
                    ->select('subjects.name as subject_name', 'users.name as teacher_name')
                    ->get();

                // If no subjects assigned, still include class
                if ($subjects->isEmpty()) {
                    if ($search && !str_contains(strtolower('Form ' . $class->form_level . ' - ' . $class->name), $search)) {
                        // Skip if search term doesn't match class name
                    } else {
                        $csvData[] = [
                            'Class' => 'Form ' . $class->form_level . ' - ' . $class->name,
                            'Subject' => '',
                            'Teacher' => '',
                            'Status' => 'No subjects assigned',
                        ];
                    }
                }

                foreach ($subjects as $subject) {
                    $className = 'Form ' . $class->form_level . ' - ' . $class->name;
                    $subjectName = $subject->subject_name;
                    $teacherName = $subject->teacher_name ?? '';

                    // Apply search filter (class, subject, or teacher)
                    if ($search && !(
                        str_contains(strtolower($className), $search) ||
                        str_contains(strtolower($subjectName), $search) ||
                        str_contains(strtolower($teacherName), $search)
                    )) continue;

                    $csvData[] = [
                        'Class' => $className,
                        'Subject' => $subjectName,
                        'Teacher' => $teacherName,
                        'Status' => $teacherName ? 'Assigned' : 'Unassigned',
                    ];
                }
            }
        }
    }

    // Convert CSV array to string
    $csvString = '';
    if (!empty($csvData)) {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($csvData[0]));
        foreach ($csvData as $row) fputcsv($handle, $row);
        rewind($handle);
        $csvString = stream_get_contents($handle);
        fclose($handle);
    }

    return response()->json([
        'success' => true,
        'csv' => $csvString,
        'filename' => 'admin_report_' . $type . '_' . now()->format('Ymd_His') . '.csv'
    ]);
}

}
