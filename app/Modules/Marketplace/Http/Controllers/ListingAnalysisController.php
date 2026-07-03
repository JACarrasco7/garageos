<?php

namespace App\Modules\Marketplace\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Actions\AnalyzeListingUrlAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListingAnalysisController extends Controller
{
    public function __construct(
        private AnalyzeListingUrlAction $analyzer
    ) {}

    public function create(): Response
    {
        return Inertia::render('Marketplace/Analyze');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
        ]);

        try {
            $data = $this->analyzer->execute($validated['url']);

            return Inertia::render('Marketplace/Analyze', [
                'analyzed' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo analizar el anuncio: '.$e->getMessage());
        }
    }
}
