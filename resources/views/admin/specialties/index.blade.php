@extends('adminlte::page')

@section('title', 'Specialties')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Specialties</h1>
        <a href="{{ route('admin.specialties.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Specialty
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="Search name or slug..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-dark">Reset</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($specialties as $sp)
                    <tr>
                        <td>{{ $sp->id }}</td>
                        <td>{{ $sp->name }}</td>
                        <td><code>{{ $sp->slug }}</code></td>
                        <td>
                            <a href="{{ route('admin.specialties.edit',$sp) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.specialties.destroy',$sp) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this specialty?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center p-4">No data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($specialties->hasPages())
            <div class="card-footer">
                {{ $specialties->links() }}
            </div>
        @endif
    </div>
@stop
