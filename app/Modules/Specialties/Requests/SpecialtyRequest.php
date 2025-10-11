<?php

namespace App\Modules\Specialties\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Specialty;

class SpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->type === 'admin';
    }

    public function rules(): array
    {
        // مع Route::resource هيكون اسم البراميتر 'specialty'
        $routeParam = $this->route('specialty');
        $id = $routeParam instanceof Specialty ? $routeParam->id : (int) $routeParam;

        $slugRule = Rule::unique('specialties','slug');
        if ($id) { $slugRule = $slugRule->ignore($id); }

        return [
            'name' => ['required','string','max:255'],              // مطلوب في الحالتين
            'slug' => ['nullable','string','max:255', $slugRule],   // unique..ignore(id) في التحديث
        ];
    }
}
