@csrf
<div class="mb-3">
    <label class="form-label">الاسم <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $site->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الرابط <span class="text-danger">*</span></label>
    <input type="url" name="url" class="form-control" value="{{ old('url', $site->url ?? '') }}" placeholder="https://example.com" required>
    @error('url') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.sites.index') }}" class="btn btn-secondary">إلغاء</a>

