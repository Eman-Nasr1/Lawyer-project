@extends('adminlte::page')

@section('title', 'تفاصيل الرسالة')

@section('content_header')
    <h1>تفاصيل الرسالة</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">معلومات المرسل</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الاسم:</strong> {{ $contactMessage->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>البريد الإلكتروني:</strong> <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
                </div>
                <div class="col-md-6">
                    <p><strong>الهاتف:</strong> <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a></p>
                </div>
                <div class="col-md-6">
                    <p><strong>التاريخ:</strong> {{ $contactMessage->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">الرسالة</h3>
        </div>
        <div class="card-body">
            <p>{{ $contactMessage->message }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">العودة</a>
        <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" class="d-inline"
              onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">حذف</button>
        </form>
    </div>
@stop


