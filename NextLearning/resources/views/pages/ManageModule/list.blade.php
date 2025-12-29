@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <strong>{{ $subject->name }}</strong> – Let’s Learn
    </div>

    <div class="card-body">

        @forelse($subject->modules as $module)
            <div class="mb-4">

                {{-- Module Title --}}
                <h5 class="fw-bold text-primary">
                    {{ $module->modules_name }}
                </h5>

                {{-- Materials --}}
                @forelse($module->materials as $material)
                    <div class="border rounded p-2 mb-2">
                        <p class="mb-1 fw-semibold">
                            {{ $material->materials_name }}
                        </p>

                        <p class="text-muted mb-1">
                            {{ $material->materials_notes }}
                        </p>

                        <a href="{{ asset('storage/' . $material->file_path) }}"
                           class="btn btn-sm btn-outline-primary"
                           download>
                            Download
                        </a>
                    </div>
                @empty
                    <p class="text-muted ms-3">
                        No materials available for this module.
                    </p>
                @endforelse

            </div>
            <hr>
        @empty
            <p class="text-muted">
                No modules available under this subject.
            </p>
        @endforelse

    </div>
</div>
@endsection
