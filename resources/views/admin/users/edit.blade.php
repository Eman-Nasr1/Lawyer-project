@extends('adminlte::page')
@section('title','Edit User')
@section('content_header') <h1>Edit User</h1> @stop
@section('content')
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-body">
<form action="{{ route('admin.users.update',$user) }}" method="POST">@method('PUT') @include('admin.users._form',['user'=>$user])</form>
</div></div>
@stop
