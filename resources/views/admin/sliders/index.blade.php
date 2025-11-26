@extends('adminlte::page')

@section('title', 'سلايدرات')       

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>سلايدرات</h1>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة سلايدر
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="Search title or description..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">#</th>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>الرابط</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sliders as $slider)
                    <tr>
                        <td>{{ $slider->id }}</td>
                        <td>        
                            @if($slider->image_url)
                                <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" style="max-width: 80px; max-height: 50px; object-fit: cover;">
                            @else
                                <span class="text-muted">لا يوجد صورة</span>
                            @endif
                        </td>
                        <td>{{ $slider->title }}</td>
                        <td>
                            @if($slider->link_url)
                                <a href="{{ $slider->link_url }}" target="_blank" class="text-primary">
                                    <i class="fas fa-external-link-alt"></i> الرابط
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $slider->sort_order }}</td>
                        <td>
                            @if($slider->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا السلايدر؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center p-4">لا يوجد سلايدرات.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($sliders->hasPages())
            <div class="card-footer">
                {{ $sliders->links() }}
            </div>
        @endif
    </div>
@stop

