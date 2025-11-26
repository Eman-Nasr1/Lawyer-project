<?php

namespace App\Modules\Sliders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        $sliderId = $this->route('slider');
        $id = $sliderId instanceof \App\Models\Slider ? $sliderId->id : (int) $sliderId;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => $id ? ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'] : ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

