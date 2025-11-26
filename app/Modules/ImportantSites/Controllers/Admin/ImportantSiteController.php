<?php

namespace App\Modules\ImportantSites\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportantSite;
use App\Modules\ImportantSites\Requests\ImportantSiteRequest;
use App\Modules\ImportantSites\Services\ImportantSiteService;
use Illuminate\Http\Request;

class ImportantSiteController extends Controller
{
    public function __construct(private ImportantSiteService $service) {}

    public function index(Request $request)
    {
        $items = $this->service->list(
            perPage: (int)$request->get('per_page', 12),
            search: $request->get('search')
        );
        return view('admin.important-sites.index', [
            'sites' => $items,
            'search' => $request->get('search'),
        ]);
    }

    public function create()
    {
        return view('admin.important-sites.create');
    }

    public function store(ImportantSiteRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.important-sites.index')->with('success', 'Created');
    }

    public function edit(ImportantSite $importantSite)
    {
        return view('admin.important-sites.edit', compact('importantSite'));
    }

    public function update(ImportantSiteRequest $request, ImportantSite $importantSite)
    {
        $this->service->update($importantSite->id, $request->validated());
        return redirect()->route('admin.important-sites.index')->with('success', 'Updated');
    }

    public function destroy(ImportantSite $importantSite)
    {
        $this->service->delete($importantSite->id);
        return back()->with('success', 'Deleted');
    }
}

