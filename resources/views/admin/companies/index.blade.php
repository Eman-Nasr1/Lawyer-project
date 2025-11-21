@extends('adminlte::page')

@section('title', 'Companies Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Companies Management</h1>
    </div>
@stop

@section('content')
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input name="search" class="form-control" placeholder="Search by name or email..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>
        @if($search || $status)
            <div class="mt-2">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-dark btn-sm">Reset</a>
            </div>
        @endif
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Experience</th>
                        <th>Specialties</th>
                        <th>Status</th>
                        <th style="width:200px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>{{ $company->id }}</td>
                        <td>{{ $company->owner->name }}</td>
                        <td>{{ $company->owner->email }}</td>
                        <td>{{ $company->years_of_experience }} years</td>
                        <td>
                            @if($company->specialties->count() > 0)
                                {{ $company->specialties->pluck('name')->join(', ') }}
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            @if($company->is_approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                            @if($company->is_featured)
                                <span class="badge bg-info">Featured</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$company->is_approved)
                                <form action="{{ route('admin.companies.approve', $company) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-success" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.companies.reject', $company) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-warning" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center p-4">No companies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($companies->hasPages())
            <div class="card-footer">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
@stop






