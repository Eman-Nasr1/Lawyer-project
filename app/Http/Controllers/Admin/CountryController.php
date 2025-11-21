<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CountryRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.countries.index', [
            'countries' => $countries,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(CountryRequest $request)
    {
        Country::create($request->validated());
        return to_route('admin.countries.index')->with('success', 'تم إضافة الدولة بنجاح');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(CountryRequest $request, Country $country)
    {
        $country->update($request->validated());
        return to_route('admin.countries.index')->with('success', 'تم تحديث الدولة بنجاح');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return back()->with('success', 'تم حذف الدولة بنجاح');
    }
}
