<?php

namespace App\Modules\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Listings\Models\Listing;
use App\Modules\Listings\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    public function __construct(protected ListingService $service) {}

    /**
     * Display a listing index.
     */
    public function index(): Response
    {
        return Inertia::render('Listings/Index');
    }

    /**
     * Full-text search API endpoint.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        if ($query === '') {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0, 'query' => ''],
            ]);
        }

        $results = $this->service->search($query, 15);

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'query' => $query,
            ],
        ]);
    }
}
