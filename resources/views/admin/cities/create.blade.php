@extends('adminlte::page')

@section('title', 'إضافة مدينة جديدة')

@section('content_header')
    <h1>إضافة مدينة جديدة</h1>
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
            <form action="{{ route('admin.cities.store') }}" method="POST">
                @include('admin.cities._form', ['countries' => $countries])
            </form>
        </div>
    </div>
@stop

