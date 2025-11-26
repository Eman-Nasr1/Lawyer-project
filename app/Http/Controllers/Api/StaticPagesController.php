<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    /**
     * GET /api/static-pages
     * Get all active static pages
     */
    public function index(Request $request)
    {
        $pages = StaticPage::where('status', 'active')
            ->select('id', 'title', 'slug', 'content', 'created_at', 'updated_at')
            ->orderBy('title')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $pages
        ]);
    }

    /**
     * GET /api/static-pages/{slug}
     * Get a single static page by slug
     */
    public function show($id)
    {
      
        $page = StaticPage::where('status', 'active')
            ->where('id', $id)
            ->select('id', 'title', 'slug', 'content', 'created_at', 'updated_at')
            ->firstOrFail();
  
        return response()->json([
            'status' => true,
            'data' => $page
        ]);
    }
}

