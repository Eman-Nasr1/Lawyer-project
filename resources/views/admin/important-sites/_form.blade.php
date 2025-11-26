@csrf
<div class="mb-3">
    <label class="form-label">الاسم<span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $site->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الرابط<span class="text-danger">*</span></label>
    <input type="url" name="url" class="form-control" value="{{ old('url', $site->url ?? '') }}" required placeholder="https://example.com">
    @error('url') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الوصف</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $site->description ?? '') }}</textarea>
    @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">النوع</label>
    <select name="type" class="form-control">
        <option value="">Select type...</option>
        <option value="lawyer" @if(old('type', $site->type ?? '') === 'lawyer') selected @endif>Lawyer</option>
        <option value="client" @if(old('type', $site->type ?? '') === 'client') selected @endif>Client</option>
        <option value="company" @if(old('type', $site->type ?? '') === 'company') selected @endif>Company</option>
        <option value="general" @if(old('type', $site->type ?? '') === 'general') selected @endif>General</option>
    </select>
    @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">ترتيب العرض</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $site->sort_order ?? 0) }}" min="0">
    @error('sort_order') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" name="is_active" class="form-check-input" value="1" 
               @if(old('is_active', isset($site) ? $site->is_active : true)) checked @endif id="is_active">
        <label class="form-check-label" for="is_active">نشط</label>
    </div>
    @error('is_active') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.important-sites.index') }}" class="btn btn-secondary">إلغاء</a>

