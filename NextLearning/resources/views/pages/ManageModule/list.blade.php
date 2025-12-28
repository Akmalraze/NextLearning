@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                📚 {{ $subject->name }} – Let’s Learn
            </h5>
        </div>

        <div class="card-body">

            @forelse($subject->modules as $module)
                {{-- Module Card --}}
                <div class="card mb-4 border">
                    <div class="card-body">

                        {{-- Module Title --}}
                        <h5 class="fw-bold text-primary mb-2">
                             {{ $module->modules_name }}
                        </h5>

                        @if($module->modules_description)
                            <p class="text-muted mb-3">
                                {{ $module->modules_description }}
                            </p>
                        @endif

                        {{-- Materials --}}
                        <h6 class="fw-semibold mb-2">📂 Learning Materials</h6>

                        @forelse($module->materials as $material)
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="mb-1 fw-semibold">
                                                {{ $material->materials_name }}
                                            </p>

                                            @if($material->materials_notes)
                                                <p class="text-muted mb-0">
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
                            <div class="alert alert-secondary mb-0">
                                No learning materials uploaded for this module.
                            </div>
                        @endforelse

                    </div>
                </div>
            @empty
                <div class="alert alert-secondary text-center">
                    No modules available under this subject.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection
