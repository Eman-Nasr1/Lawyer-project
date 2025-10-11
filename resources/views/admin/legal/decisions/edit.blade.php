@extends('adminlte::page')

@section('title', 'Edit Decision')

@section('content_header')
    <h1>Edit Decision</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.legal.decisions.update', $decision) }}" method="POST">
                @method('PUT')
                @include('admin.legal.decisions._form', ['decision'=>$decision])
            </form>
        </div>
    </div>
@stop
