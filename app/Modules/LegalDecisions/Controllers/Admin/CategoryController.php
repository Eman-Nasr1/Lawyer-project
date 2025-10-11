<?php

namespace App\Modules\LegalDecisions\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDecisionCategory as Category;
use App\Modules\LegalDecisions\Requests\CategoryRequest;
use App\Modules\LegalDecisions\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service) {}

    public function index(Request $request)
    {
        $items = $this->service->paginate(
            perPage: (int) $request->get('per_page', 12),
            search:  $request->get('search')
        );

        return view('admin.legal.categories.index', [
            'categories' => $items,
            'search'     => $request->get('search'),
        ]);
    }

    public function create()
    {
        return view('admin.legal.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $this->service->create($request->validated());
        return to_route('admin.legal.categories.index')->with('success','Created');
    }

    public function edit(Category $category)
    {
        return view('admin.legal.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->service->update($category, $request->validated());
        return to_route('admin.legal.categories.index')->with('success','Updated');
    }

    public function destroy(Category $category)
    {
        $this->service->delete($category);
        return back()->with('success','Deleted');
    }
}
