@extends('adminlte::page')

@section('title', 'Add Decision')

@section('content_header')
    <h1>Add Decision</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.legal.decisions.store') }}" method="POST">
                @include('admin.legal.decisions._form')
            </form>
        </div>
    </div>
@stop
