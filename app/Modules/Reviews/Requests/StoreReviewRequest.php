<?php
namespace App\Modules\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'target_type'    => 'required|in:lawyer,company',
            'target_id'      => 'required|integer',
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string|max:2000',
          ];
    }
}
