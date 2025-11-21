@extends('adminlte::page')

@section('title', 'المدن')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>المدن</h1>
        <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة مدينة جديدة
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="البحث في الاسم أو الدولة..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.cities.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">#</th>
                        <th>الاسم</th>
                        <th>الدولة</th>
                        <th>الحالة</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cities as $city)
                    <tr>
                        <td>{{ $city->id }}</td>
                        <td>{{ $city->name }}</td>
                        <td>{{ $city->country->name }}</td>
                        <td>
                            <span class="badge badge-{{ $city->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $city->status === 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المدينة؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center p-4">لا توجد بيانات.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($cities->hasPages())
            <div class="card-footer">
                {{ $cities->links() }}
            </div>
        @endif
    </div>
@stop

