<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalDecision;
use App\Models\LegalDecisionCategory;
use Illuminate\Http\Request;

class LegalDecisionsController extends Controller
{
    /**
     * GET /api/legal-decisions
     * Get all categories with their legal decisions
     * Supports filtering by category_id
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:legal_decision_categories,id'],
        ]);

        $query = LegalDecisionCategory::with(['decisions' => function ($q) {
            $q->select('id', 'category_id', 'title', 'body', 'source_url', 'published_at', 'created_at', 'updated_at')
              ->orderByDesc('published_at')
              ->orderByDesc('id');
        }])
        ->select('id', 'name', 'slug')
        ->orderBy('name');

        // Filter by category if provided
        if (!empty($validated['category_id'])) {
            $query->where('id', $validated['category_id']);
        }

        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'decisions' => $category->decisions->map(function ($decision) {
                    return [
                        'id' => $decision->id,
                        'title' => $decision->title,
                        'body' => $decision->body,
                        'source_url' => $decision->source_url,
                        'published_at' => $decision->published_at ? $decision->published_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $decision->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $decision->updated_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
}

