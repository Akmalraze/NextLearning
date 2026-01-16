@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        Module: {{ $module->modules_name }}
    </div>

    <div class="card-body">
        <h5>Materials Available for Download</h5>

        
            @foreach($module->materials as $material)  <!-- Looping through the module's materials -->
                <div>
                    <p>{{ $material->materials_name }}</p>
                    <p>{{ $material->materials_notes }}</p>
                    <!-- Link to download the material -->
                    <a href="{{ asset('storage/' . $material->file_path) }}" download>Download</a>
                </div>
            @endforeach
    

        <hr>
        @if(!auth()->user()->hasRole('Student')) 
        <a href="{{ route('materials-create', $module->id) }}" class="btn btn-primary">Add Material</a>
        @endif
    </div>
</div>
@endsection
