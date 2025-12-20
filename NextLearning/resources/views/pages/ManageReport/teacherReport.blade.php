@extends('layouts.master')

@section('content')

<div class="card">
    <div class="card-header">
        <h4>My Classes & Subject Assignment</h4>
        <p class="text-muted">
            View the classes and subjects assigned to you for lesson planning and classroom management.
        </p>
    </div>

    <div class="card-body">

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h5>Total Classes Assigned</h5>
                        <h3>{{ $classesCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h5>Total Subjects Taught</h5>
                        <h3>{{ $subjectsCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Class & Subject Assignment --}}
        @forelse($assignments as $className => $subjects)
            <div class="mb-4">
                <h5 class="mb-2">{{ $className }}</h5>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            {{-- Optional column --}}
                            {{-- <th>No. of Students</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $sub)
                        <tr>
                            <td>{{ $sub->subject_name }}</td>

                            {{-- Optional: Student count --}}
                            {{-- <td>{{ $sub->student_count ?? 'N/A' }}</td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="alert alert-warning text-center">
                You have not been assigned to any classes yet.
            </div>
        @endforelse

    </div>
</div>

@endsection
