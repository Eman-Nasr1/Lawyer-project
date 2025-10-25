<?php

namespace App\Modules\Favorites\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'favoritable_type' => 'required|in:lawyer,company',
            'favoritable_id'   => 'required|integer',

        ];
    }
}
