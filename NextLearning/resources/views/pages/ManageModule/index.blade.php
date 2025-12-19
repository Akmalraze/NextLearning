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
      <th scope="row">BAB 1</th>
      <td>Kedaulatan Negara</td>
      <td>Memahami konsep kedaulatan dan usaha mempertahankannya.</td>
      <td>Cikgu zubaidah</td>
    </tr>
    <tr>
      <th scope="row">BAB 2</th>
      <td>Perlembagaan Persekutuan</td>
      <td>Thornton</td>
      <td>Cikgu zubaidah</td>
    </tr>
    <tr>
      <th scope="row">BAB 1</th>
      <td>Raja Berperlembagaan & Demokrasi Berparlimen</td>
      <td>Peranan Raja dalam sistem Raja Berperlembagaan dan ciri-ciri demokrasi berparlimen di Malaysia.</td>
      <td>Cikgu zubaidah</td>
    </tr>
    <tr>
      <th scope="row">BAB 4</th>
      <td>Perlembagaan Persekutuan</td>
      <td>Struktur pentadbiran persekutuan, pembahagian kuasa antara Kerajaan Persekutuan dan Negeri.</td>
      <td>Cikgu zubaidah</td>
    </tr>
  </tbody>
</table>
    
    <a class="btn btn-primary" href="{{ route('modules-create') }}" role="button">Add New Module</a>
    <a class="btn btn-primary" href="{{ route('modules-list') }}" role="button">list</a>
</div>
@endsection

