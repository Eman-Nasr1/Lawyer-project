<?php

namespace App\Http\Requests\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {  return Auth::check() && Auth::user()->type === 'admin';}

    public function rules(): array
    {
        $id = $this->route('role')?->id;
        return [
            'name' => ['required','string','max:255','unique:roles,name'.($id?",$id":'')],
            'guard_name' => ['required','in:web,sanctum'],
            'permissions' => ['array'],
            'permissions.*' => ['integer','exists:permissions,id'],
        ];
    }
}
