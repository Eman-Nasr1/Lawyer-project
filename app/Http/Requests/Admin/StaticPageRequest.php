<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StaticPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        $id = $this->route('static_page')?->id;
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:static_pages,slug' . ($id ? ",$id" : '')],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
