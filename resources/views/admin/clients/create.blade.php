@extends('adminlte::page')

@section('title', 'إضافة عميل جديد')

@section('content_header')
    <h1>إضافة عميل جديد</h1>
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
            <form action="{{ route('admin.clients.store') }}" method="POST">
                @include('admin.clients._form')
            </form>
        </div>
    </div>
@stop

