@extends('adminlte::page')
@section('title','Users')
@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Users</h1>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</a>
</div>
@stop
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<form method="GET" class="mb-3">
  <div class="input-group">
    <input name="search" class="form-control" placeholder="Search name/email..." value="{{ $search }}">
    <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
    @if($search)<a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark">Reset</a>@endif
  </div>
</form>
<div class="card"><div class="card-body p-0">
<table class="table table-hover mb-0">
<thead><tr>
  <th style="width:70px">#</th><th>Name</th><th>Email</th><th>Type</th><th>Roles</th><th style="width:180px">Actions</th>
</tr></thead>
<tbody>
@forelse($users as $u)
<tr>
  <td>{{ $u->id }}</td>
  <td>{{ $u->name }}</td>
  <td>{{ $u->email }}</td>
  <td><span class="badge bg-secondary">{{ $u->type }}</span></td>
  <td>@foreach($u->roles as $r)<span class="badge bg-info">{{ $r->name }}</span>@endforeach</td>
  <td>
    <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
    <form action="{{ route('admin.users.destroy',$u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
      @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
    </form>
  </td>
</tr>
@empty <tr><td colspan="6" class="text-center p-4">No data.</td></tr> @endforelse
</tbody></table>
</div>
@if($users->hasPages())<div class="card-footer">{{ $users->links() }}</div>@endif
</div>
@stop
