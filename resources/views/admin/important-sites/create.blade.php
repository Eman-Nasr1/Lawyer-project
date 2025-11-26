@extends('adminlte::page')

@section('title', 'إضافة موقع مهم')

@section('content_header')
    <h1>إضافة موقع مهم</h1>
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
            <form action="{{ route('admin.important-sites.store') }}" method="POST">
                @include('admin.important-sites._form')
            </form>
        </div>
    </div>
@stop

