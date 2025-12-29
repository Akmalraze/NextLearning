@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        Module Management
    </div>

    <div class="card-body">
        <form action="" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title">Modules Name*</label>
                <input type="text" id="title" name="name" class="form-control @error('name') is-invalid @enderror"  required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email">Modules Description*</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"  required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div>
                <button class="btn btn-primary me-2" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

