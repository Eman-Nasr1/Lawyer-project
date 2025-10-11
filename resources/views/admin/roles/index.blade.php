@extends('adminlte::page')
@section('title','Roles')
@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Roles</h1>
  <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Role</a>
</div>
@stop
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<form method="GET" class="mb-3">
  <div class="input-group">
    <input name="search" class="form-control" placeholder="Search roles..." value="{{ $search }}">
    <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
    @if($search)<a href="{{ route('admin.roles.index') }}" class="btn btn-outline-dark">Reset</a>@endif
  </div>
</form>
<div class="card"><div class="card-body p-0">
<table class="table table-hover mb-0">
<thead><tr><th style="width:70px">#</th><th>Name</th><th>Guard</th><th>Permissions</th><th style="width:180px">Actions</th></tr></thead>
<tbody>
@forelse($roles as $r)
<tr>
  <td>{{ $r->id }}</td><td>{{ $r->name }}</td><td><code>{{ $r->guard_name }}</code></td>
  <td>@foreach($r->permissions as $p)<span class="badge bg-info">{{ $p->name }}</span>@endforeach</td>
  <td>
    <a href="{{ route('admin.roles.edit',$r) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
    <form action="{{ route('admin.roles.destroy',$r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
      @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
    </form>
  </td>
</tr>
@empty <tr><td colspan="5" class="text-center p-4">No data.</td></tr> @endforelse
</tbody></table>
</div>
@if($roles->hasPages())<div class="card-footer">{{ $roles->links() }}</div>@endif
</div>
@stop
