@csrf
<div class="mb-3">
    <label class="form-label">الدولة <span class="text-danger">*</span></label>
    <select name="country_id" class="form-control" required>
        <option value="">اختر الدولة</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}" {{ old('country_id', $city->country_id ?? '') == $country->id ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
    @error('country_id') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الاسم <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $city->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">الحالة <span class="text-danger">*</span></label>
    <select name="status" class="form-control" required>
        <option value="active" {{ old('status', $city->status ?? 'active') === 'active' ? 'selected' : '' }}>نشط</option>
        <option value="inactive" {{ old('status', $city->status ?? '') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
    </select>
    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> حفظ
</button>
<a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">إلغاء</a>


