@extends('adminlte::page')

@section('title', 'تعديل سلايدر')           

@section('content_header')
    <h1>Edit Slider</h1>
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
            <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.sliders._form', ['slider' => $slider])
            </form>
        </div>
    </div>
@stop

