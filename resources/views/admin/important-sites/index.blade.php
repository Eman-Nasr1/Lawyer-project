@extends('adminlte::page')

@section('title', 'مواقع مهمة')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>مواقع مهمة</h1>
        <a href="{{ route('admin.important-sites.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة موقع
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="Search name, URL or description..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.important-sites.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
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
                        <th>Type</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sites as $site)
                    <tr>
                        <td>{{ $site->id }}</td>
                        <td>{{ $site->name }}</td>
                        <td>
                            <a href="{{ $site->url }}" target="_blank" class="text-primary">
                                <i class="fas fa-external-link-alt"></i> {{ Str::limit($site->url, 40) }}
                            </a>
                        </td>
                        <td>
                            @if($site->type)
                                <span class="badge bg-info">{{ $site->type }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $site->sort_order }}</td>
                        <td>
                            @if($site->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.important-sites.edit', $site) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.important-sites.destroy', $site) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الموقع؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                        <tr><td colspan="7" class="text-center p-4">لا يوجد مواقع مهمة.</td></tr>
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

