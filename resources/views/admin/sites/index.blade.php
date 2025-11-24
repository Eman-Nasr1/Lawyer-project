@extends('adminlte::page')

@section('title', 'المواقع المهمة')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>المواقع المهمة</h1>
        <a href="{{ route('admin.sites.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة موقع جديد
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="البحث في الاسم أو الرابط..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.sites.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
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
                        <th>الرابط</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sites as $site)
                    <tr>
                        <td>{{ $site->id }}</td>
                        <td>{{ $site->name }}</td>
                        <td><a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></td>
                        <td>
                            <a href="{{ route('admin.sites.edit', $site) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الموقع؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center p-4">لا توجد بيانات.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($sites->hasPages())
            <div class="card-footer">
                {{ $sites->links() }}
            </div>
        @endif
    </div>
@stop


