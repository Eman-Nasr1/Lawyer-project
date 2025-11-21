@extends('adminlte::page')

@section('title', 'تعديل الصفحة')

@section('content_header')
    <h1>تعديل الصفحة</h1>
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
            <form action="{{ route('admin.static-pages.update', $staticPage) }}" method="POST">
                @method('PUT')
                @include('admin.static-pages._form', ['staticPage' => $staticPage])
            </form>
        </div>
    </div>
@stop

