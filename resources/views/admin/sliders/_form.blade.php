@csrf
<div class="mb-3">
    <label class="form-label">العنوان <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title ?? '') }}" required>
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الوصف</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $slider->description ?? '') }}</textarea>
    @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الصورة @if(!isset($slider))<span class="text-danger">*</span>@endif</label>
    <input type="file" name="image" class="form-control" accept="image/*" @if(!isset($slider)) required @endif>
    @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
    @if(isset($slider) && $slider->image_url)
        <div class="mt-2">
            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" style="max-width: 200px; max-height: 150px; object-fit: cover;" class="img-thumbnail">
            <p class="text-muted small mt-1">Current image</p>
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">الرابط</label>
    <input type="url" name="link_url" class="form-control" value="{{ old('link_url', $slider->link_url ?? '') }}" placeholder="https://example.com">
    @error('link_url') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">ترتيب العرض</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" min="0">
    @error('sort_order') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <div class="form-check">
        <input type="checkbox" name="is_active" class="form-check-input" value="1" 
               @if(old('is_active', isset($slider) ? $slider->is_active : true)) checked @endif id="is_active">
        <label class="form-check-label" for="is_active">نشط</label>
    </div>
    @error('is_active') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">إلغاء</a> 
