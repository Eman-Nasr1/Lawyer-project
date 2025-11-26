@extends('adminlte::page')

@section('title', 'Edit Important Site')

@section('content_header')
    <h1>Edit Important Site</h1>
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
            <form action="{{ route('admin.important-sites.update', $importantSite) }}" method="POST">
                @method('PUT')
                @include('admin.important-sites._form', ['site' => $importantSite])
            </form>
        </div>
    </div>
@stop

