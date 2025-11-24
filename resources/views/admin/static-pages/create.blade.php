@extends('adminlte::page')

@section('title', 'إضافة صفحة جديدة')

@section('content_header')
    <h1>إضافة صفحة جديدة</h1>
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
            <form action="{{ route('admin.static-pages.store') }}" method="POST">
                @include('admin.static-pages._form')
            </form>
        </div>
    </div>
@stop


