@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        Module Management
    </div>

    <div class="card-body">
        <table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">No</th>
      <th scope="col">Modules Name</th>
      <th scope="col">Modules Description</th>
    <th scope="col">Handle</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
  </tbody>
</table>
    
    <a class="btn btn-primary" href="{{ route('modules-create') }}" role="button">Add New Module</a>
    <a class="btn btn-primary" href="{{ route('modules-list') }}" role="button">list</a>
</div>
@endsection

