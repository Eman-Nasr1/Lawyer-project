<?php
namespace App\Modules\Lawyer\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Lawyer\Services\LawyerAvailabilityService;
use App\Modules\Company\Services\CompanyAvailabilityService;

class PublicAvailabilityController extends Controller
{
    public function __construct(
        private LawyerAvailabilityService $lawyerService,
        private CompanyAvailabilityService $companyService
    ){}

    // GET /api/availabilities/slots?lawyer_id=5&date=2025-10-20&slot_minutes=30
    // GET /api/availabilities/slots?company_id=3&date=2025-10-20&slot_minutes=30
    public function slots(Request $request)
    {
        $validated = $request->validate([
            'lawyer_id' => 'required_without:company_id|exists:lawyers,id',
            'company_id' => 'required_without:lawyer_id|exists:companies,id',
            'date'      => 'required|date',
            'slot_minutes' => 'nullable|integer|min:15|max:180',
        ]);

        $date = $validated['date'];
        $slotMinutes = $validated['slot_minutes'] ?? 30;

        if (!empty($validated['lawyer_id'])) {
            $slots = $this->lawyerService->generateFreeSlots(
                (int)$validated['lawyer_id'], $date, $slotMinutes
            );
            $type = 'lawyer';
            $id = $validated['lawyer_id'];
        } else {
            $slots = $this->companyService->generateFreeSlots(
                (int)$validated['company_id'], $date, $slotMinutes
            );
            $type = 'company';
            $id = $validated['company_id'];
        }

        return response()->json([
            'type' => $type,
            'id' => $id,
            'date' => $date,
            'slots' => $slots
        ]);
    }
}
