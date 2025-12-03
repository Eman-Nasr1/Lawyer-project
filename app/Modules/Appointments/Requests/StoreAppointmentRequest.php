<?php

namespace App\Modules\Appointments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lawyer_id'  => [
                'nullable',
                'integer',
                'exists:lawyers,id',
                'required_without:company_id',
            ],
            'company_id' => [
                'nullable',
                'integer',
                'exists:companies,id',
                'required_without:lawyer_id',
            ],
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'notes'      => 'nullable|string|max:2000',
            'attachments' => 'nullable|string',
            'files'      => 'nullable|array|max:10',
            'files.*'    => 'file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
            'names'      => 'nullable|array',
            'names.*'    => 'nullable|string|max:255',
        ];
    }
}
