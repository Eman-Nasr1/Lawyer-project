@csrf
<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $role->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Guard <span class="text-danger">*</span></label>
    <select name="guard_name" class="form-select" required>
      @foreach(['web'=>'web','sanctum'=>'sanctum'] as $g=>$label)
        <option value="{{ $g }}" @selected(old('guard_name', $role->guard_name ?? ($guard ?? 'web'))==$g)>{{ $label }}</option>
      @endforeach
    </select>
    @error('guard_name') <div class="text-danger small">{{ $message }}</div> @enderror
  </div>
</div>

<div class="mb-2"><strong>Permissions</strong></div>
<div class="row">
  @foreach($permissions as $p)
    <div class="col-md-3 mb-2">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $p->id }}"
               @checked(collect(old('permissions', $rolePermIds ?? []))->contains($p->id))>
        {{ $p->name }}
      </label>
    </div>
  @endforeach
</div>

<button class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
