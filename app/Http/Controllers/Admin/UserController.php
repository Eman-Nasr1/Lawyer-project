<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q,$s)=>$q->where(fn($w)=>$w->where('name','like',"%$s%")->orWhere('email','like',"%$s%")))
            ->orderByDesc('id')->paginate((int)$request->get('per_page', 12));

        return view('admin.users.index', ['users'=>$users,'search'=>$request->search]);
    }

    public function create()
    {
        $guard = 'sanctum'; // لو عندك sanctum خليه 'sanctum'
        $roles = Role::where('guard_name',$guard)->orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $d = $request->validated();
        $user = User::create([
            'name'=>$d['name'], 'email'=>$d['email'],
            'password'=>Hash::make($d['password']), 'type'=>$d['type'],
        ]);

        $roleNames = !empty($d['roles']) ? Role::whereIn('id',$d['roles'])->pluck('name')->toArray() : [];
        $user->syncRoles($roleNames);

        return to_route('admin.users.index')->with('success','User created');
    }

    public function edit(User $user)
    {
        $guard = 'sanctum';
        $roles = Role::where('guard_name',$guard)->orderBy('name')->get();
        $userRoleIds = $user->roles()->pluck('id')->toArray();
        return view('admin.users.edit', compact('user','roles','userRoleIds'));
    }

    public function update(UserRequest $request, User $user)
    {
        $d = $request->validated();
        $user->fill(['name'=>$d['name'], 'email'=>$d['email'], 'type'=>$d['type']]);
        if (!empty($d['password'])) $user->password = Hash::make($d['password']);
        $user->save();

        $roleNames = !empty($d['roles']) ? Role::whereIn('id',$d['roles'])->pluck('name')->toArray() : [];
        $user->syncRoles($roleNames);

        return to_route('admin.users.index')->with('success','User updated');
    }

    public function destroy(User $user)
    {
        if (Auth::auth()->id() === $user->id) return back()->with('error','You cannot delete yourself.');
        $user->delete();
        return back()->with('success','User deleted');
    }
}
