<?php
namespace App\Modules\Company\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id'           => 'nullable|exists:company_availabilities,id',

            // Choose one only: day_of_week or date
            'day_of_week'  => 'nullable|required_without:date|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'date'         => 'nullable|required_without:day_of_week|date',

            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',

            'is_active'    => 'sometimes|boolean',
        ];
    }

    /**
     * Prevent combining date and day_of_week
     */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if ($this->filled('date') && $this->filled('day_of_week')) {
                $v->errors()->add('date', 'Choose either date or day_of_week — not both.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'day_of_week.required_without' => 'Choose day_of_week or date.',
            'date.required_without'        => 'Choose date or day_of_week.',
        ];
    }
}

