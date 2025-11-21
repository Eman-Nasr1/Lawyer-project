@csrf
<div class="mb-3">
    <label class="form-label">الاسم <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $client->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}" required>
    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الهاتف <span class="text-danger">*</span></label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone ?? '') }}" required>
    @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">كلمة المرور @if(!isset($client))<span class="text-danger">*</span>@endif</label>
    <input type="password" name="password" class="form-control" {{ !isset($client) ? 'required' : '' }} placeholder="{{ isset($client) ? 'اتركه فارغاً إذا لم تريد تغييره' : '' }}">
    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
</div>


<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">إلغاء</a>

