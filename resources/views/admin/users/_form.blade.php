@csrf
<div class="mb-3">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Email <span class="text-danger">*</span></label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="row">
  <div class="col-md-6 mb-3">
      <label class="form-label">Password {{ isset($user)?'(leave blank to keep)':'' }}</label>
      <input type="password" name="password" class="form-control" {{ isset($user)?'':'required' }}>
      @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-6 mb-3">
      <label class="form-label">Type <span class="text-danger">*</span></label>
      <select name="type" class="form-select" required>
          @foreach(['admin'=>'Admin','lawyer'=>'Lawyer','client'=>'Client','company'=>'Company'] as $k=>$v)
              <option value="{{ $k }}" @selected(old('type', $user->type ?? '')==$k)>{{ $v }}</option>
          @endforeach
      </select>
      @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
  </div>
</div>
<div class="mb-3">
    <label class="form-label">Roles</label>
    <select name="roles[]" class="form-select" multiple>
        @foreach($roles as $r)
            <option value="{{ $r->id }}" @selected(collect(old('roles', $userRoleIds ?? []))->contains($r->id))>
                {{ $r->name }}
            </option>
        @endforeach
    </select>
</div>
<button class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
