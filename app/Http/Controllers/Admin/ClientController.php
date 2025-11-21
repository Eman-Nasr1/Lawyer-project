<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clientRole = Role::where('name', 'client')->first();
        $clientRoleId = $clientRole ? $clientRole->id : null;

        $clients = User::query()
            ->when($clientRoleId, fn($q) => $q->whereHas('roles', fn($r) => $r->where('roles.id', $clientRoleId)))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.clients.index', [
            'clients' => $clients,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'type' => 'client',
        ]);

        // Assign client role
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'sanctum']);
        $user->assignRole($clientRole);

        return to_route('admin.clients.index')->with('success', 'تم إنشاء العميل بنجاح');
    }

    public function edit(User $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, User $client)
    {
        $data = $request->validated();
        $client->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (!empty($data['password'])) {
            $client->password = Hash::make($data['password']);
        }

        $client->save();

        return to_route('admin.clients.index')->with('success', 'تم تحديث العميل بنجاح');
    }

    public function destroy(User $client)
    {
        $client->delete();
        return back()->with('success', 'تم حذف العميل بنجاح');
    }
}
