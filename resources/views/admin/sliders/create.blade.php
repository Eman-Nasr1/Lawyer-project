@extends('adminlte::page')

@section('title', 'إضافة سلايدر')

@section('content_header')
    <h1>إضافة سلايدر</h1>
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
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @include('admin.sliders._form')
            </form>
        </div>
    </div>
@stop

