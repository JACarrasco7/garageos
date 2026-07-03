<?php

namespace App\Modules\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Listings\Models\Listing;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    /**
     * Display a listing index.
     */
    public function index(): Response
    {
        return Inertia::render('Listings/Index');
    }
}
