<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $guard = 'sanctum';
        $roles = Role::where('guard_name',$guard)
            ->when($request->search, fn($q,$s)=>$q->where('name','like',"%$s%"))
            ->orderBy('name')->paginate((int)$request->get('per_page',12));

        return view('admin.roles.index', ['roles'=>$roles,'search'=>$request->search]);
    }

    public function create()
    {
        $guard = config('auth.defaults.guard','web');
        $permissions = Permission::where('guard_name',$guard)->orderBy('name')->get();
        return view('admin.roles.create', compact('permissions','guard'));
    }

    public function store(RoleRequest $request)
    {
        $d = $request->validated();
        $role = Role::create(['name'=>$d['name'], 'guard_name' => 'sanctum']);
        $role->syncPermissions($d['permissions'] ?? []);
        return to_route('admin.roles.index')->with('success','Role created');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name',$role->guard_name)->orderBy('name')->get();
        $rolePermIds = $role->permissions()->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role','permissions','rolePermIds'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $d = $request->validated();
        $role->update(['name'=>$d['name'], 'guard_name' => 'sanctum']);
        $role->syncPermissions($d['permissions'] ?? []);
        return to_route('admin.roles.index')->with('success','Role updated');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') return back()->with('error','Cannot delete core admin role.');
        $role->delete();
        return back()->with('success','Role deleted');
    }
}
