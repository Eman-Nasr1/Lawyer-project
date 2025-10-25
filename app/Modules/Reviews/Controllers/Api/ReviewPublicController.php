<?php
namespace App\Modules\Reviews\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Reviews\Repositories\ReviewRepositoryInterface;
use Illuminate\Http\Request;

class ReviewPublicController extends Controller
{
    public function __construct(private ReviewRepositoryInterface $repo) {}

    // GET /api/lawyer/{lawyer}/reviews
    public function index(Request $r, int $lawyer)
    {
      return response()->json($this->repo->forTarget('lawyer', $lawyer, (int)$r->get('per_page', 15)));
    }
}
