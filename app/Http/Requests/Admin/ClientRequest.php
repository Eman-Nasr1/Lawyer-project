<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('client')?->id;
        $pass = $this->isMethod('post') ? ['required', 'string', 'min:8'] : ['nullable', 'string', 'min:8'];
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email' . ($id ? ",$id" : '')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => $pass,
        ];
    }
}
