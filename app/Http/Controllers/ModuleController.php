<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

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
