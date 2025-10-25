<?php

namespace App\Modules\Appointments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAppointmentFilesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
            // اسم اختياري يظهر للمستخدم (لو هترسليه من الموبايل مثلاً)
            'names'   => 'nullable|array|size:' . count($this->input('files', [])),
            'names.*' => 'nullable|string|max:255',
        ];
    }
}
