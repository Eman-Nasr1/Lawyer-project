<?php

namespace App\Modules\Appointments\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Requests\StoreAppointmentRequest;
use App\Modules\Appointments\Requests\UploadAppointmentFilesRequest;
use App\Modules\Appointments\Requests\CancelAppointmentRequest;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentClientController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $repo,
        private AppointmentService $service
    ) {}

    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
        ]);
    
        $user    = $request->user();
        $perPage = (int) $request->get('per_page', 15);
        $status  = $request->get('status'); // pending / confirmed / ...
    
        $appointments = $this->repo->paginateForClient($user->id, $perPage, $status);
    
        $transformedAppointments = $appointments->through(function ($appointment) {
            $lawyer  = $appointment->lawyer;
            $company = $appointment->company;
    
            $providerType   = null;
            $providerName   = null;
            $providerAvatar = null;
            $providerTitle  = null;
            $address        = null;
            $userId         = null;
            $lawyerId       = null;
            $companyId      = null;
    
            if ($lawyer && $lawyer->user) {
                $providerType   = 'lawyer';
                $providerName   = $lawyer->user->name;
                $providerAvatar = $lawyer->user->avatar_url;
                $providerTitle  = optional($lawyer->specialties->first())->name;
                $userId         = $lawyer->user->id;
                $lawyerId       = $lawyer->id;
    
                $primaryAddress = $lawyer->primaryAddress;
                if ($primaryAddress) {
                    $address = [
                        'city'         => $primaryAddress->city,
                        'area'         => $primaryAddress->address_line,
                        'full_address' => "{$primaryAddress->address_line}, {$primaryAddress->city}",
                    ];
                }
            } elseif ($company) {
                $providerType   = 'company';
                $providerName   = $company->owner->name;
                $providerAvatar = $company->owner->avatar_url;
                $providerTitle  = optional($company->specialties->first())->name;
                $userId         = $company->owner ? $company->owner->id : null;
                $companyId      = $company->id;
    
                $primaryAddress = $company->primaryAddress;
                if ($primaryAddress) {
                    $address = [
                        'city'         => $primaryAddress->city,
                        'area'         => $primaryAddress->address_line,
                        'full_address' => "{$primaryAddress->address_line}, {$primaryAddress->city}",
                    ];
                }
            }
    
            return [
                'id'         => $appointment->id,
                'status'     => $appointment->status,
                'date'       => $appointment->date,
                'start_time' => $appointment->start_time,
                'end_time'   => $appointment->end_time,
                'case_type'  => $appointment->case_type,
                'notes'      => $appointment->notes,
                'provider'   => [
                    'type'       => $providerType,
                    'user_id'    => $userId,
                    'lawyer_id'  => $lawyerId,
                    'company_id' => $companyId,
                    'name'       => $providerName,
                    'title'      => $providerTitle,
                    'avatar_url' => $providerAvatar,
                    'address'    => $address,
                ],
            ];
        });
    
        return response()->json([
            'status' => true,
            'data'   => $transformedAppointments,
        ]);
    }
    
    public function reschedule(Request $request, int $id)
    {
        $user = $request->user();

        $validated = $request->validate([
            'date'       => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // نجيب الموعد
        $appointment = $this->repo->find($id);

        // نتأكد إن الميعاد بتاع نفس الـ client
        if ($appointment->user_id !== $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'You are not allowed to modify this appointment.',
            ], 403);
        }

        // نمنع تعديل مواعيد مكتملة أو ملغية لو حابة
        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This appointment cannot be rescheduled.',
            ], 422);
        }

        // تعديل البيانات
        $appointment->date       = $validated['date'];
        $appointment->start_time = $validated['start_time'];
        $appointment->end_time   = $validated['end_time'];
        $appointment->status     = 'pending'; // أو خليها زى ما تحبي
        $appointment->save();

        return response()->json([
            'status' => true,
            'data'   => $appointment->fresh(),
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $userId = $request->user()->id;
        try {
            $validated = $request->validated();
            $files = $request->file('files');
            $names = $request->input('names', null);
         //   dd($request->file('files'));
            // Remove files and names from validated data before creating appointment
            unset($validated['files'], $validated['names']);

            $created = $this->service->createForClient($validated, $userId);

            // Attach files if provided
            if ($files && count($files) > 0) {
                $this->service->attachFiles($created->id, $userId, $files, $names);
                $created->refresh()->load('files'); // Refresh and load files relationship
            }

            return response()->json($created, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // رفع ملفات للميعاد
    public function uploadFiles(UploadAppointmentFilesRequest $request, int $id)
    {
        $userId = $request->user()->id;
        $files  = $request->file('files');
        $names  = $request->input('names', null);

        $list = $this->service->attachFiles($id, $userId, $files, $names);
        return response()->json(['files' => $list]);
    }

    // إلغاء ميعاد + سبب
    public function cancel(CancelAppointmentRequest $request, int $id)
    {
        $userId = $request->user()->id;
        try {
            $updated = $this->service->cancel($id, $userId, $request->input('reason'));
            return response()->json($updated);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // حذف (soft delete) لو محتاجاه
    public function destroy(int $id)
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'deleted']);
    }
}
