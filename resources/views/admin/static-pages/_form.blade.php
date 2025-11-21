@csrf
<div class="mb-3">
    <label class="form-label">العنوان <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $staticPage->title ?? '') }}" required>
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الرابط (Slug)</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $staticPage->slug ?? '') }}" placeholder="سيتم توليده تلقائياً إذا كان فارغاً">
    @error('slug') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">المحتوى <span class="text-danger">*</span></label>
    <textarea name="content" class="form-control" rows="10" required>{{ old('content', $staticPage->content ?? '') }}</textarea>
    @error('content') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الحالة <span class="text-danger">*</span></label>
    <select name="status" class="form-control" required>
        <option value="active" {{ old('status', $staticPage->status ?? 'active') === 'active' ? 'selected' : '' }}>نشط</option>
        <option value="inactive" {{ old('status', $staticPage->status ?? '') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
    </select>
    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.static-pages.index') }}" class="btn btn-secondary">إلغاء</a>

