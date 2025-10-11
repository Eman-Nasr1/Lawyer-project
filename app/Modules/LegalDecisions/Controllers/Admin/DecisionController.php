<?php

namespace App\Modules\LegalDecisions\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDecision;
use App\Models\LegalDecisionCategory as Category;
use App\Modules\LegalDecisions\Requests\DecisionRequest;
use App\Modules\LegalDecisions\Services\DecisionService;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function __construct(private DecisionService $service) {}

    public function index(Request $request)
    {
        $items = $this->service->paginate(
            perPage: (int) $request->get('per_page', 12),
            search:  $request->get('search'),
            categoryId: $request->get('category_id')
        );

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('admin.legal.decisions.index', [
            'decisions'  => $items,
            'categories' => $categories,
            'filters'    => $request->only(['search','category_id']),
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        return view('admin.legal.decisions.create', compact('categories'));
    }

    public function store(DecisionRequest $request)
    {
        $this->service->create($request->validated());
        return to_route('admin.legal.decisions.index')->with('success','Created');
    }

    public function edit(LegalDecision $decision)
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        return view('admin.legal.decisions.edit', compact('decision','categories'));
    }

    public function update(DecisionRequest $request, LegalDecision $decision)
    {
        $this->service->update($decision, $request->validated());
        return to_route('admin.legal.decisions.index')->with('success','Updated');
    }

    public function destroy(LegalDecision $decision)
    {
        $this->service->delete($decision);
        return back()->with('success','Deleted');
    }
}
