<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::with('country')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhereHas('country', fn($c) => $c->where('name', 'like', "%$s%")))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.cities.index', [
            'cities' => $cities,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        $countries = Country::where('status', 'active')->orderBy('name')->get();
        return view('admin.cities.create', compact('countries'));
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());
        return to_route('admin.cities.index')->with('success', 'تم إضافة المدينة بنجاح');
    }

    public function edit(City $city)
    {
        $countries = Country::where('status', 'active')->orderBy('name')->get();
        return view('admin.cities.edit', compact('city', 'countries'));
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());
        return to_route('admin.cities.index')->with('success', 'تم تحديث المدينة بنجاح');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'تم حذف المدينة بنجاح');
    }
}
