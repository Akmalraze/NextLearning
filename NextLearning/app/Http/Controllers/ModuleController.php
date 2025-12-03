<?php

namespace App\Http\Controllers;
 use App\Models\Modules;
 use App\Models\Subjects;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Modules::with('subject')->get();
        return view('pages.ManageModule.index');
    }
    
    public function view()
    { 
        return view('pages.ManageModule.view');
    }

    public function create()
    {
        return view('pages.ManageModule.create');
    }


    public function store(Request $request)
    {
    
         $request->validate([
            'modules_name' => 'required|string|max:255',
            'modules_code' => 'required|numeric|min:0',
            'modules_description' => 'required|string|max:255',
            'subjects_id' => 'required|exists:subjects,subjects_id', 
        
        
         ]);

         // Create the farm
         $modules = Modules::create([
             'modules_name' => $request->modules_name,
             'modules_code' => $request->modules_code,
             'modules_description' => $request->modules_description,
             'subjects_id' => $request->subjects_id,

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
