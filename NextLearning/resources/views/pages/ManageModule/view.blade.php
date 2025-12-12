@extends('layouts.master')

@section('content')

<style>
.clickable-card {
    cursor: pointer;
    transition: 0.2s;
}
.clickable-card:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}
</style>

<div class="card clickable-card" onclick="window.location='{{ route('modules-view') }}'">
    <div class="card-header">
        Modules Information 
    </div>

    <div class="card-body">
        <h5 class="card-title">Zaman Purba</h5>
        <h6 class="card-subtitle mb-2 text-body-secondary">Module 1</h6>
        <p class="card-text">
            
            This is it - your very first Subtopic 🎊 Things are starting to get real - and this time, we will dive into the Zaman Purba!
            <br>Before class, please do some online research on:
                </br>
            <br>
            Sources, key benefits and drawbacks of Zaman Purba  
            Zaman Purba trajectory in Malaysia  
            <br>
            Download the pre-class work template in the next section to note down your thoughts.  
            <br>
            If you have any questions, please do not hesitate to reach out to your Tas!
        </p>
         <a class="btn btn-primary" href="{{ route('modules-edit') }}" role="button">Edit</a>
         <a class="btn btn-primary" href="{{ route('modules-material') }}" role="button">Upload Material</a>
         
    </div>
</div>
@endsection
