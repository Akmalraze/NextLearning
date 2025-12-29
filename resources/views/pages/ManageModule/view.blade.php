@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                 Module: {{ $module->modules_name }}
            </h5>
        </div>

        <div class="card-body">
            <p class="text-muted">
                {{ $module->modules_description }}
            </p>

            <hr>

            {{-- Materials Section --}}
            <h6 class="mb-3 fw-bold">📂 Learning Materials</h6>

            @forelse($module->materials as $material)
                <div class="card mb-3 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-semibold mb-1">
                                    {{ $material->materials_name }}
                                </h6>

                                @if($material->materials_notes)
                                    <p class="text-muted mb-2">
                                        {{ $material->materials_notes }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <a href="{{ asset('storage/' . $material->file_path) }}"
                                   class="btn btn-sm btn-outline-success"
                                   download>
                                    ⬇ Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary text-center">
                    No learning materials have been uploaded for this module yet.
                </div>
            @endforelse

            {{-- Teacher / Admin Action --}}
            @if(!auth()->user()->hasRole('Student'))
                <div class="text-end mt-4">
                    <a href="{{ route('materials-create', $module->id) }}"
                       class="btn btn-primary">
                         Add New Material
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
