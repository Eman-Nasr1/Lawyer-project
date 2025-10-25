<?php

namespace App\Modules\Appointments\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Requests\UpdateAppointmentStatusRequest;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentLawyerController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $repo,
        private AppointmentService $service
    ) {}

    // GET /api/lawyer/appointments
    public function index(Request $request)
    {
        $lawyerId = $request->user()->lawyer->id; // تأكدي من العلاقة user->lawyer
        return response()->json($this->repo->paginateForLawyer($lawyerId, (int)$request->get('per_page', 15)));
    }

    // PUT /api/lawyer/appointments/{id}/status
    public function updateStatus(UpdateAppointmentStatusRequest $request, int $id)
    {
        $lawyerId = $request->user()->lawyer->id;
        try {
            $updated = $this->service->updateStatusByLawyer($id, $request->validated()['status'], $lawyerId);
            return response()->json($updated);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
