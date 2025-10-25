<?php
namespace App\Modules\Lawyer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id'           => 'nullable|exists:lawyer_availabilities,id',

            // اختاري واحد فقط: day_of_week أو date
            'day_of_week'  => 'nullable|required_without:date|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'date'         => 'nullable|required_without:day_of_week|date',

            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',

            'is_active'    => 'sometimes|boolean',
        ];
    }

    /**
     * منع الجمع بين date و day_of_week على كل نسخ لارافيل
     */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            if ($this->filled('date') && $this->filled('day_of_week')) {
                $v->errors()->add('date', 'اختاري date أو day_of_week — مش الاتنين معًا.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'day_of_week.required_without' => 'اختاري day_of_week أو date.',
            'date.required_without'        => 'اختاري date أو day_of_week.',
        ];
    }
}
