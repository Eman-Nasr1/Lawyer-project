@extends('adminlte::page')

@section('title', 'رسائل التواصل')

@section('content_header')
    <h1>رسائل التواصل</h1>
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
                <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-dark">إعادة تعيين</a>
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
                        <th>التاريخ</th>
                        <th style="width:180px">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td>{{ $message->id }}</td>
                        <td>{{ $message->name }}</td>
                        <td>{{ $message->email }}</td>
                        <td>{{ $message->phone }}</td>
                        <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center p-4">لا توجد بيانات.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($messages->hasPages())
            <div class="card-footer">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
@stop

