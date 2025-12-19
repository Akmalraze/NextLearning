<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ModuleController extends Controller
{
    public function index()
    {

        return view('pages.ManageModule.index');
    }

    public function list()
    {

        return view('pages.ManageModule.list');
    }

    public function view()
    {
        return view('pages.ManageModule.view');
    }

    public function create()
    {
        abort_if(Gate::denies('create modules'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('pages.ManageModule.create');
    }


    public function store(Request $request)
    {
        abort_if(Gate::denies('create modules'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'modules_name' => 'required|string|max:255',
            'modules_code' => 'required|numeric|min:0',
            'modules_description' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',


        ]);

        // Create the module
        $modules = Modules::create([
            'modules_name' => $request->modules_name,
            'modules_code' => $request->modules_code,
            'modules_description' => $request->modules_description,
            'subject_id' => $request->subject_id,

        ]);


        return view('pages.ManageModule.create');
    }

    public function adminReport()
    {
        // User & Role Distribution
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins   = User::role('admin')->count();

    // Teacher Workload
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

    return view('pages.ManageReport.adminreport', compact(
        'totalStudents', 'totalTeachers', 'totalAdmins', 'workload'
    ));
    }






    // public function updated(Request $request)
    // {
    //      $validated = $request->validate([
    //         'modules_name'        => 'required|string|max:255',
    //         'farms_size_ha'     => 'required|numeric|min:0',
    //         'farms_description' => 'required|string|max:255',
    //         'locations_id'      => 'required|exists:locations,locations_id',
    //         // kalau memang ada kolum ini pada jadual farms:
    //         'tasks_priority'    => 'nullable|in:Normal,Penting,Sangat Penting',
    //     ]);

    //     // Kemaskini
    //     $modules->update($validated);

    //     // Kembali ke halaman view atau index
    //     return redirect()->route('module-view', $modules->$modules_id)
    //         ->with('success', 'Farm updated successfully.');
    // }
}
