<?php

namespace App\Modules\Appointments\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Requests\UpdateAppointmentStatusRequest;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentLawyerController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $repo,
        private AppointmentService $service
    ) {}

    // GET /api/lawyer/appointments
    public function index(Request $request)
    {
        // فلتر بالـ status لو حابة: pending / confirmed / completed / cancelled
        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
        ]);
    
        $lawyer = $request->user()->lawyer;
    
        if (!$lawyer) {
            return response()->json([
                'status'  => false,
                'message' => 'No lawyer profile for this user.',
            ], 403);
        }
    
        $perPage = (int) $request->get('per_page', 15);
        $status  = $request->get('status'); // optional
    
        // نجيب المواعيد من الريبو
        $appointments = $this->repo->paginateForLawyer($lawyer->id, $perPage, $status);
    
        // نجهز شكل الريسبونس: نضيف بيانات العميل + الملفات
        $appointments->getCollection()->transform(function ($appointment) {
            $client = $appointment->user;
    
            return [
                'id'         => $appointment->id,
                'status'     => $appointment->status,
                'date'       => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time'   => $appointment->end_time,
    
                // بيانات العميل (اللى حجز)
                'client' => $client ? [
                    'id'         => $client->id,
                    'name'       => $client->name,
                    'email'      => $client->email,
                    'phone'      => $client->phone ?? null,
                    'avatar_url' => $client->avatar_url ?? null,
                ] : null,
    
                // الملفات المرفقة
                'files' => $appointment->files->map(function ($file) {
                    return [
                        'id'   => $file->id,
                        'name' => $file->name,
                        'url'  => Storage::disk('public')->url($file->path),
                    ];
                }),
            ];
        });
    
        return response()->json([
            'status' => true,
            'data'   => $appointments,
        ]);
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
