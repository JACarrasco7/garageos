<?php

namespace App\Modules\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Providers\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::with(['user', 'services', 'reviews'])
            ->verified()
            ->paginate(12);

        return Inertia::render('Providers/Index', [
            'providers' => $providers,
        ]);
    }

    public function show(Provider $provider)
    {
        $provider->load(['user', 'services', 'availability', 'reviews.user']);

        return Inertia::render('Providers/Show', [
            'provider' => $provider,
        ]);
    }

    public function create()
    {
        return Inertia::render('Providers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        $provider = $request->user()->providers()->create($validated);

        return redirect()->route('providers.show', $provider);
    }

    public function edit(Provider $provider)
    {
        $this->authorize('update', $provider);

        return Inertia::render('Providers/Edit', [
            'provider' => $provider,
        ]);
    }

    public function update(Request $request, Provider $provider)
    {
        $this->authorize('update', $provider);

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        $provider->update($validated);

        return back()->with('success', 'Proveedor actualizado correctamente');
    }

    public function storeReview(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
            'service_type' => ['nullable', 'string'],
        ]);

        $provider->reviews()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        $provider->increment('review_count');
        $provider->update([
            'rating' => $provider->reviews()->avg('rating'),
        ]);

        return back()->with('success', 'Reseña enviada correctamente');
    }
}
