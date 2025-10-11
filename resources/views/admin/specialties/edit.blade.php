@extends('adminlte::page')

@section('title', 'Edit Specialty')

@section('content_header')
    <h1>Edit Specialty</h1>
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
            <form action="{{ route('admin.specialties.update', $specialty) }}" method="POST">
                @method('PUT')
                @include('admin.specialties._form', ['specialty'=>$specialty])
            </form>
        </div>
    </div>
@stop
