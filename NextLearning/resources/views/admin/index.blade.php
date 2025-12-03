@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row text-white py-3 rounded">
        <div class="col-12 text-center">
            <img src="images/canvas.png" class="img-fluid" alt="...">
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row my-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3>5</h3>
                    <p>Subject Enrolled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3>0</h3>
                    <p>Subject Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3>70</h3>
                    <p>Activity Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3>25</h3>
                    <p>Activity Due</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Cards Section -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Mathematics : Form 1</h5>
                    <p class="card-text">Cg Zubaidah Saad</p>
                    <a href="#" class="btn btn-primary">View Subject</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Sejarah : Form 1</h5>
                    <p class="card-text">Cg Sazali Zamali</p>
                    <a href="#" class="btn btn-primary">View Subject</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Science : Form 1</h5>
                    <p class="card-text">Cg Zainal Selamat</p>
                    <a href="#" class="btn btn-primary">View Subject</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
