@extends('layouts.app')

@section('styles')

<link href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
    
@endsection

@section('content')
<div class="container ">
    @if(session()->has('listo'))
    <div class="alert alert-success">
        {{ session()->get('listo') }}
    </div>
    @endif
        
    <patients-component></patients-component>
        
</div>


@endsection
@section('js')
@endsection
