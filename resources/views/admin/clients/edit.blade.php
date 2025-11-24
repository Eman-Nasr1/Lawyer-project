@extends('adminlte::page')

@section('title', 'تعديل العميل')

@section('content_header')
    <h1>تعديل العميل</h1>
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
            <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                @method('PUT')
                @include('admin.clients._form', ['client' => $client])
            </form>
        </div>
    </div>
@stop


