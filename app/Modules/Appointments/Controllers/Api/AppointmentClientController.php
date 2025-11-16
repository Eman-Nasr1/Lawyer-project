<?php

namespace App\Modules\Appointments\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Appointments\Requests\StoreAppointmentRequest;
use App\Modules\Appointments\Requests\UploadAppointmentFilesRequest;
use App\Modules\Appointments\Requests\CancelAppointmentRequest;
use App\Modules\Appointments\Repositories\AppointmentRepositoryInterface;
use App\Modules\Appointments\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentClientController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $repo,
        private AppointmentService $service
    ) {}

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        return response()->json($this->repo->paginateForClient($userId, (int)$request->get('per_page', 15)));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $userId = $request->user()->id;
        try {
            $validated = $request->validated();
            $files = $request->file('files');
            $names = $request->input('names', null);
            
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
