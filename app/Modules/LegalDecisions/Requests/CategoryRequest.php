<?php

namespace App\Modules\LegalDecisions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        $id = (int) optional($this->route('category'))->id;

        return [
            'name' => ['required','string','max:255'],
            'slug' => [
                'nullable','string','max:255',
                Rule::unique('legal_decision_categories','slug')->ignore($id)
            ],
        ];
    }
}
