@extends('adminlte::page')

@section('title', 'Add Specialty')

@section('content_header')
    <h1>Add Specialty</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.specialties.store') }}" method="POST">
                @include('admin.specialties._form')
            </form>
        </div>
    </div>
@stop
