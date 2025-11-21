<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticPageRequest;
use App\Models\StaticPage;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function index(Request $request)
    {
        $pages = StaticPage::query()
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%")->orWhere('slug', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int)$request->get('per_page', 12));

        return view('admin.static-pages.index', [
            'pages' => $pages,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return view('admin.static-pages.create');
    }

    public function store(StaticPageRequest $request)
    {
        StaticPage::create($request->validated());
        return to_route('admin.static-pages.index')->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    public function show(StaticPage $staticPage)
    {
        return view('admin.static-pages.show', compact('staticPage'));
    }

    public function edit(StaticPage $staticPage)
    {
        return view('admin.static-pages.edit', compact('staticPage'));
    }

    public function update(StaticPageRequest $request, StaticPage $staticPage)
    {
        $staticPage->update($request->validated());
        return to_route('admin.static-pages.index')->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(StaticPage $staticPage)
    {
        $staticPage->delete();
        return back()->with('success', 'تم حذف الصفحة بنجاح');
    }
}
