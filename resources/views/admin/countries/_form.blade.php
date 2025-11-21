@csrf
<div class="mb-3">
    <label class="form-label">الاسم <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $country->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الكود (3 أحرف) <span class="text-danger">*</span></label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $country->code ?? '') }}" maxlength="3" required>
    @error('code') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الحالة <span class="text-danger">*</span></label>
    <select name="status" class="form-control" required>
        <option value="active" {{ old('status', $country->status ?? 'active') === 'active' ? 'selected' : '' }}>نشط</option>
        <option value="inactive" {{ old('status', $country->status ?? '') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
    </select>
    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">إلغاء</a>

