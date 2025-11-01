<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with(['owner', 'specialties'])
            ->when($request->search, function($q, $s) {
                $q->whereHas('owner', function($w) use ($s) {
                    $w->where('name', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%");
                });
            })
            ->when($request->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($request->status === 'pending', fn($q) => $q->where('is_approved', false))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.companies.index', [
            'companies' => $companies,
            'search' => $request->search,
            'status' => $request->status,
        ]);
    }

    public function show(Company $company)
    {
        $company->load(['owner', 'specialties', 'addresses']);
        return view('admin.companies.show', compact('company'));
    }

    public function approve(Company $company)
    {
        $company->update(['is_approved' => true]);
        return back()->with('success', 'Company approved successfully');
    }

    public function reject(Company $company)
    {
        $company->update(['is_approved' => false]);
        return back()->with('success', 'Company rejected');
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'is_approved' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'years_of_experience' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $company->update($validated);

        return back()->with('success', 'Company updated successfully');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return back()->with('success', 'Company deleted successfully');
    }
}

