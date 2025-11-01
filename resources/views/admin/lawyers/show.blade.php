@extends('adminlte::page')

@section('title', 'Lawyer Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lawyer Details</h1>
        <a href="{{ route('admin.lawyers.index') }}" class="btn btn-secondary">
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
                    <form action="{{ route('admin.lawyers.update', $lawyer) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $lawyer->user->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $lawyer->user->email }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Years of Experience</label>
                            <input type="number" name="years_of_experience" class="form-control" 
                                   value="{{ $lawyer->years_of_experience }}" min="0" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="4">{{ $lawyer->bio }}</textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_approved" class="form-check-input" 
                                       id="is_approved" value="1" {{ $lawyer->is_approved ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_approved">Approved</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_featured" class="form-check-input" 
                                       id="is_featured" value="1" {{ $lawyer->is_featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </form>
                </div>
            </div>

            @if($lawyer->specialties->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Specialties</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($lawyer->specialties as $specialty)
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
                    @if(!$lawyer->is_approved)
                        <form action="{{ route('admin.lawyers.approve', $lawyer) }}" method="POST" class="mb-2">
                            @csrf @method('PUT')
                            <button class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.lawyers.reject', $lawyer) }}" method="POST" class="mb-2">
                            @csrf @method('PUT')
                            <button class="btn btn-warning w-100">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.lawyers.destroy', $lawyer) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this lawyer?')">
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
                    <p><strong>Rating:</strong> {{ number_format($lawyer->avg_rating, 2) }} / 5.00</p>
                    <p><strong>Reviews:</strong> {{ $lawyer->reviews_count }}</p>
                    <p><strong>Created:</strong> {{ $lawyer->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop

