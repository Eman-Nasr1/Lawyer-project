@extends('adminlte::page')

@section('title', 'تعديل الموقع')

@section('content_header')
    <h1>تعديل الموقع</h1>
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
            <form action="{{ route('admin.sites.update', $site) }}" method="POST">
                @method('PUT')
                @include('admin.sites._form', ['site' => $site])
            </form>
        </div>
    </div>
@stop


