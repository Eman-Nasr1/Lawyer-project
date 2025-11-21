@extends('adminlte::page')

@section('title', 'إضافة موقع جديد')

@section('content_header')
    <h1>إضافة موقع جديد</h1>
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
            <form action="{{ route('admin.sites.store') }}" method="POST">
                @include('admin.sites._form')
            </form>
        </div>
    </div>
@stop

