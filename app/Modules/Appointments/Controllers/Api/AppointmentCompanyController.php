<?php

namespace App\Modules\Appointments\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Services\AppointmentService;
use App\Modules\Appointments\Requests\UpdateAppointmentStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentCompanyController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $repo,
        private AppointmentService $service
    ) {}

    // GET /api/company/appointments
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
        ]);
    
        $user = $request->user();
    
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
    
        $companyId = $user->company->id;
        $perPage   = (int) $request->get('per_page', 15);
    
        $status  = $request->get('status');   // string | null
        $appointments = $this->repo->paginateForCompany($companyId, $perPage, $status);
    
        $appointments->getCollection()->transform(function ($appointment) {
            $client = $appointment->user;
            $lawyer = $appointment->lawyer;
    
            return [
                'id'         => $appointment->id,
                'status'     => $appointment->status,
                'date'       => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time'   => $appointment->end_time,
    
                'client' => $client ? [
                    'id'         => $client->id,
                    'name'       => $client->name,
                    'email'      => $client->email,
                    'phone'      => $client->phone ?? null,
                    'avatar_url' => $client->avatar_url ?? null,
                ] : null,
    
                'lawyer' => ($lawyer && $lawyer->user) ? [
                    'id'         => $lawyer->id,
                    'name'       => $lawyer->user->name,
                    'avatar_url' => $lawyer->user->avatar_url ?? null,
                    'title'      => optional($lawyer->specialties->first())->name,
                    'address'    => $lawyer->primaryAddress ? [
                        'city'         => $lawyer->primaryAddress->city,
                        'area'         => $lawyer->primaryAddress->address_line,
                        'full_address' => "{$lawyer->primaryAddress->address_line}, {$lawyer->primaryAddress->city}",
                    ] : null,
                ] : null,
    
                'files' => $appointment->files->map(function ($file) {
                    return [
                        'id'   => $file->id,
                        'name' => $file->name,
                        'url'  => \Storage::disk('public')->url($file->path),
                    ];
                }),
            ];
        });
    
        return response()->json([
            'status' => true,
            'data'   => $appointments,
        ]);
    }
    

    // POST /api/company/appointments/{id}/assign-lawyer
    public function assignToLawyer(Request $request, int $id)
    {
        $validated = $request->validate([
            'lawyer_id' => ['required', 'integer', 'exists:lawyers,id'],
        ]);

        $user = $request->user();

        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }

        $companyId = $user->company->id;

        try {
            $appointment = $this->service->assignToLawyerByCompany(
                $id,
                $validated['lawyer_id'],
                $companyId
            );

            return response()->json([
                'status' => true,
                'data'   => $appointment,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, int $id)
    {
        $user = $request->user();
    
        // لازم يكون عنده شركة
        if (!$user->company) {
            return response()->json(['message' => 'No company profile for this user'], 403);
        }
    
        $companyId = $user->company->id;
    
        try {
            $updated = $this->service->updateStatusByCompany(
                $id,
                $request->validated()['status'],
                $companyId
            );
    
            return response()->json([
                'status' => true,
                'data'   => $updated
            ]);
    
        } catch (\RuntimeException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }
    
    }
