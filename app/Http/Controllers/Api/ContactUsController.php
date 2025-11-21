<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactUsRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactUsController extends Controller
{
    public function store(ContactUsRequest $request): JsonResponse
    {
        ContactMessage::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.',
        ], 201);
    }
}
