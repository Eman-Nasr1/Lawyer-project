@csrf
<div class="mb-3">
    <label class="form-label">Category <span class="text-danger">*</span></label>
    <select name="category_id" class="form-select" required>
        <option value="">-- Select Category --</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $decision->category_id ?? null) == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    @error('category_id') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $decision->title ?? '') }}" required>
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Body <span class="text-danger">*</span></label>
    <textarea name="body" rows="6" class="form-control" required>{{ old('body', $decision->body ?? '') }}</textarea>
    @error('body') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Source URL</label>
        <input type="url" name="source_url" class="form-control" value="{{ old('source_url', $decision->source_url ?? '') }}">
        @error('source_url') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Publish Date</label>
        <input type="date" name="published_at" class="form-control"
               value="{{ old('published_at', optional($decision->published_at ?? null)->format('Y-m-d')) }}">
        @error('published_at') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> Save
</button>
