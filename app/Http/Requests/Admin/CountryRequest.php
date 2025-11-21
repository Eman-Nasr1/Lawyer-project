<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('country')?->id;
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'size:3', 'unique:countries,code' . ($id ? ",$id" : '')],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
