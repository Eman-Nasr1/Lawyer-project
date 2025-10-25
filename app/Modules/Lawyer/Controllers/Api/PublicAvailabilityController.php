<?php
namespace App\Modules\Lawyer\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Lawyer\Services\LawyerAvailabilityService;

class PublicAvailabilityController extends Controller
{
    public function __construct(private LawyerAvailabilityService $service){}

    // GET /api/availabilities/slots?lawyer_id=5&date=2025-10-20&slot_minutes=30
    public function slots(Request $request)
    {
        $validated = $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'date'      => 'required|date',
            'slot_minutes' => 'nullable|integer|min:15|max:180',
        ]);

        $slots = $this->service->generateFreeSlots(
            (int)$validated['lawyer_id'], $validated['date'], $validated['slot_minutes'] ?? 30
        );

        return response()->json(['date'=>$validated['date'], 'slots'=>$slots]);
    }
}
