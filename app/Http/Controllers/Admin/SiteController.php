<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteRequest;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $sites = Site::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('url', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.sites.index', [
            'sites' => $sites,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(SiteRequest $request)
    {
        Site::create($request->validated());
        return to_route('admin.sites.index')->with('success', 'تم إضافة الموقع بنجاح');
    }

    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(SiteRequest $request, Site $site)
    {
        $site->update($request->validated());
        return to_route('admin.sites.index')->with('success', 'تم تحديث الموقع بنجاح');
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return back()->with('success', 'تم حذف الموقع بنجاح');
    }
}
