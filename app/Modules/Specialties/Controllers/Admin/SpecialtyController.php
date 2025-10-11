<?php

namespace App\Modules\Specialties\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use App\Modules\Specialties\Requests\SpecialtyRequest;
use App\Modules\Specialties\Services\SpecialtyService;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function __construct(private SpecialtyService $service) {}

    public function index(Request $request)
    {
      // dd('Inside Specialties index');
        $items = $this->service->list(
            perPage: (int)$request->get('per_page', 12),
            search: $request->get('search')
        );
        return view('admin.specialties.index', [
            'specialties' => $items,
            'search' => $request->get('search'),
        ]);
    }

    public function create() { return view('admin.specialties.create'); }

    public function store(SpecialtyRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.specialties.index')->with('success','Created');
    }

    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    public function update(SpecialtyRequest $request, Specialty $specialty)
    {
        $this->service->update($specialty->id, $request->validated());
        return redirect()->route('admin.specialties.index')->with('success','Updated');
    }

    public function destroy(Specialty $specialty)
    {
        $this->service->delete($specialty->id);
        return back()->with('success','Deleted');
    }
}
