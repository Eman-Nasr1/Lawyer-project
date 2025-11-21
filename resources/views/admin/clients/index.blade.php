@extends('adminlte::page')

@section('title', 'العملاء')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>العملاء</h1>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة عميل جديد
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input name="search" class="form-control" placeholder="البحث في الاسم أو البريد الإلكتروني أو الهاتف..." value="{{ $search }}">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
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
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>
                            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
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
        @if($clients->hasPages())
            <div class="card-footer">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
@stop

