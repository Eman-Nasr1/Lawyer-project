<?php

namespace App\Modules\Appointments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lawyer_id'  => 'required|exists:lawyers,id',
            'company_id' => 'nullable|exists:companies,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'notes'      => 'nullable|string|max:2000',
            'attachments'=> 'nullable|string',
        ];
    }
}
