<?php

namespace App\Modules\ImportantSites\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImportantSite;

class ImportantSiteApiController extends Controller
{
    public function index()
    {
       
        $sites = ImportantSite::active()
            ->ordered()
            ->select('id', 'name', 'url', 'description', 'type')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $sites
        ]);
    }
}

