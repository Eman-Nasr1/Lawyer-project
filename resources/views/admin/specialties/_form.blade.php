@csrf
<div class="mb-3">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $specialty->name ?? '') }}" required id="nameInput">
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $specialty->slug ?? '') }}" id="slugInput" placeholder="auto-generated if empty">
    @error('slug') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> Save
</button>

@push('js')
<script>
    // توليد slug تلقائيًا من الاسم (قابل للتعديل يدويًا)
    const toSlug = s => s.toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g,'')  // remove accents
        .replace(/[^a-z0-9\s\-_.]/g,' ')
        .trim().replace(/\s+/g,'-').replace(/-+/g,'-');

    const nameInput = document.getElementById('nameInput');
    const slugInput = document.getElementById('slugInput');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', e => {
            if (!slugInput.value) slugInput.value = toSlug(e.target.value);
        });
    }
</script>
@endpush
