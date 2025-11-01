<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\Specialty;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $lawyers = Lawyer::with(['user', 'specialties'])
            ->when($request->search, function($q, $s) {
                $q->whereHas('user', function($w) use ($s) {
                    $w->where('name', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%");
                });
            })
            ->when($request->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($request->status === 'pending', fn($q) => $q->where('is_approved', false))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.lawyers.index', [
            'lawyers' => $lawyers,
            'search' => $request->search,
            'status' => $request->status,
        ]);
    }

    public function show(Lawyer $lawyer)
    {
        $lawyer->load(['user', 'specialties', 'addresses']);
        return view('admin.lawyers.show', compact('lawyer'));
    }

    public function approve(Lawyer $lawyer)
    {
        $lawyer->update(['is_approved' => true]);
        return back()->with('success', 'Lawyer approved successfully');
    }

    public function reject(Lawyer $lawyer)
    {
        $lawyer->update(['is_approved' => false]);
        return back()->with('success', 'Lawyer rejected');
    }

    public function update(Request $request, Lawyer $lawyer)
    {
        $validated = $request->validate([
            'is_approved' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'years_of_experience' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'bio' => ['sometimes', 'nullable', 'string'],
            'admin_notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $lawyer->update($validated);

        return back()->with('success', 'Lawyer updated successfully');
    }

    public function destroy(Lawyer $lawyer)
    {
        $lawyer->delete();
        return back()->with('success', 'Lawyer deleted successfully');
    }
}

