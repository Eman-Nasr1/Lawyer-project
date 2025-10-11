<?php

namespace App\Http\Requests\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    { return Auth::check() && Auth::user()->type === 'admin'; }

    public function rules(): array
    {
        $id = $this->route('user')?->id;
        $pass = $this->isMethod('post') ? ['required','string','min:8'] : ['nullable','string','min:8'];

        return [
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'.($id?",$id":'')],
            'password' => $pass,
            'type' => ['required','in:admin,lawyer,client'],
            'roles' => ['array'],
            'roles.*' => ['integer','exists:roles,id'],
        ];
    }
}
