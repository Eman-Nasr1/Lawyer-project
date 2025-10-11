<?php

namespace App\Modules\LegalDecisions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required','integer', Rule::exists('legal_decision_categories','id')],
            'title'       => ['required','string','max:255'],
            'body'        => ['required','string'],
            'source_url'  => ['nullable','url','max:255'],
            'published_at'=> ['nullable','date'],
        ];
    }
}
