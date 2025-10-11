@extends('adminlte::page')

@section('title', 'Legal Decisions')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Legal Decisions</h1>
        <a href="{{ route('admin.legal.decisions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Decision
        </a>
    </div>
@stop

@section('content')
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input name="search" class="form-control" placeholder="Search title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Published</th>
                    <th style="width:180px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($decisions as $d)
                    <tr>
                        <td>{{ $d->title }}</td>
                        <td>{{ $d->category?->name ?? '-' }}</td>
                        <td>{{ optional($d->published_at)->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.legal.decisions.edit',$d) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.legal.decisions.destroy',$d) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this decision?')">
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
        @if($decisions->hasPages())
            <div class="card-footer">
                {{ $decisions->links() }}
            </div>
        @endif
    </div>
@stop
