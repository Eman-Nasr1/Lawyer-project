@extends('adminlte::page')
@section('title','Edit Role')
@section('content_header') <h1>Edit Role</h1> @stop
@section('content')
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-body">
<form action="{{ route('admin.roles.update',$role) }}" method="POST">@method('PUT') @include('admin.roles._form',['role'=>$role])</form>
</div></div>
@stop
