@extends('adminlte::page')

@section('title', 'Company Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Company Details</h1>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
@stop

@section('content')
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.companies.update', $company) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $company->owner->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $company->owner->email }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Years of Experience</label>
                            <input type="number" name="years_of_experience" class="form-control" 
                                   value="{{ $company->years_of_experience }}" min="0" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ $company->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_approved" class="form-check-input" 
                                       id="is_approved" value="1" {{ $company->is_approved ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_approved">Approved</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_featured" class="form-check-input" 
                                       id="is_featured" value="1" {{ $company->is_featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </form>
                </div>
            </div>

            @if($company->specialties->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Specialties</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($company->specialties as $specialty)
                            <li class="list-group-item">{{ $specialty->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    @if(!$company->is_approved)
                        <form action="{{ route('admin.companies.approve', $company) }}" method="POST" class="mb-2">
                            @csrf @method('PUT')
                            <button class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.companies.reject', $company) }}" method="POST" class="mb-2">
                            @csrf @method('PUT')
                            <button class="btn btn-warning w-100">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this company?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <p><strong>Rating:</strong> {{ number_format($company->avg_rating, 2) }} / 5.00</p>
                    <p><strong>Reviews:</strong> {{ $company->reviews_count }}</p>
                    <p><strong>Created:</strong> {{ $company->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop








