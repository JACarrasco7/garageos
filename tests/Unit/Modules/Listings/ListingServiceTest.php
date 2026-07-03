<?php

use App\Models\User;
use App\Modules\Listings\Enums\ListingPortal;
use App\Modules\Listings\Models\Listing;
use App\Modules\Listings\Services\ListingService;
use App\Modules\Listings\Services\Parsers\JsonLdParser;
use App\Modules\Listings\Services\Parsers\OpenGraphParser;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = new ListingService([new OpenGraphParser, new JsonLdParser]);
});

test('it can create a listing', function () {
    $data = [
        'created_by_user_id' => $this->user->id,
        'source_url' => 'https://example.com/car',
        'source_portal' => ListingPortal::AUTOSCOUT24->value,
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'extraction_status' => 'pending',
        'extraction_method' => 'manual',
    ];

    $listing = $this->service->create($data);

    expect($listing)->toBeInstanceOf(Listing::class);
    expect($listing->source_url)->toBe($data['source_url']);
    expect($listing->brand)->toBe('Toyota');
});

test('it can find a listing', function () {
    $listing = Listing::create([
        'created_by_user_id' => $this->user->id,
        'source_url' => 'https://example.com/car',
        'source_portal' => ListingPortal::AUTOSCOUT24->value,
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'extraction_status' => 'pending',
        'extraction_method' => 'manual',
    ]);

    $found = $this->service->find($listing->id);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($listing->id);
});

test('it can update a listing', function () {
    $listing = Listing::create([
        'created_by_user_id' => $this->user->id,
        'source_url' => 'https://example.com/car',
        'source_portal' => ListingPortal::AUTOSCOUT24->value,
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'extraction_status' => 'pending',
        'extraction_method' => 'manual',
    ]);

    $this->service->update($listing->id, ['title' => 'Updated Title']);

    $this->assertDatabaseHas('listings', [
        'id' => $listing->id,
        'title' => 'Updated Title',
    ]);
});

test('it can delete a listing', function () {
    $listing = Listing::create([
        'created_by_user_id' => $this->user->id,
        'source_url' => 'https://example.com/car',
        'source_portal' => ListingPortal::AUTOSCOUT24->value,
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'extraction_status' => 'pending',
        'extraction_method' => 'manual',
    ]);

    $result = $this->service->delete($listing->id);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
});

test('it extracts from url using open graph parser', function () {
    $url = 'https://example.com/car';
    $html = '<html><head><meta property="og:title" content="Test Car"><meta property="og:description" content="Test Description"><meta property="og:image" content="https://example.com/image.jpg"></head></html>';

    Http::fake([
        $url => Http::response($html, 200),
    ]);

    $listing = $this->service->extractFromUrl($url, ListingPortal::AUTOSCOUT24, $this->user->id);

    // Debug
    dump($listing->extraction_status, $listing->extraction_error, $listing->extraction_method);

    expect($listing->extraction_status)->toBe('completed');
    expect($listing->extraction_method)->toBe('opengraph');
    expect($listing->title)->toBe('Test Car');
    expect($listing->description)->toBe('Test Description');
    expect($listing->image)->toBe('https://example.com/image.jpg');
});

test('it fails when no parser matches', function () {
    $url = 'https://example.com/no-og';
    $html = '<html><head><title>Just a title</title></head></html>';

    Http::fake([
        $url => Http::response($html, 200),
    ]);

    $listing = $this->service->extractFromUrl($url, ListingPortal::AUTOSCOUT24, $this->user->id);

    expect($listing->extraction_status)->toBe('failed');
    expect($listing->extraction_error)->toMatch('/No suitable parser found/');
});

test('it fails when http request fails', function () {
    $url = 'https://example.com/error';

    Http::fake([
        $url => Http::response([], 500),
    ]);

    $listing = $this->service->extractFromUrl($url, ListingPortal::AUTOSCOUT24, 1);

    expect($listing->extraction_status)->toBe('failed');
    expect($listing->extraction_error)->toContain('Failed to fetch content');
});

test('it searches listings with full-text scope', function () {
    Listing::factory()->create([
        'title' => 'BMW Serie 3 automático diesel',
        'description' => 'Coche en perfecto estado, poco kilómetros',
        'brand' => 'BMW',
        'model' => '320d',
        'is_active' => true,
    ]);
    Listing::factory()->create([
        'title' => 'Audi A4 gasolina',
        'description' => 'Vehículo deportivo color rojo',
        'brand' => 'Audi',
        'model' => 'A4',
        'is_active' => true,
    ]);
    Listing::factory()->create([
        'title' => 'Mercedes Clase C',
        'description' => 'Sin descripción relevante',
        'is_active' => false,
    ]);

    $results = $this->service->search('BMW', 15);

    expect($results->total())->toBe(1);
    expect($results->items()[0]->brand)->toBe('BMW');
});

test('it extracts from url using json-ld parser', function () {
    $url = 'https://example.com/car-jsonld';
    $html = '<html><head><script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Vehicle",
      "brand": "Honda",
      "model": "Civic",
      "description": "A great car",
      "image": "https://example.com/image.jpg",
      "offers": {
        "@type": "Offer",
        "price": "20000.00",
        "priceCurrency": "EUR"
      }
    }
    </script></head></html>';

    Http::fake([
        $url => Http::response($html, 200),
    ]);

    $listing = $this->service->extractFromUrl($url, ListingPortal::AUTOSCOUT24, 1);

    if ($listing->extraction_status !== 'completed') {
        dump($listing->extraction_error);
    }

    expect($listing->extraction_status)->toBe('completed');
    expect($listing->extraction_method)->toBe('json_ld');
    expect($listing->brand)->toBe('Honda');
    expect($listing->model)->toBe('Civic');
    expect($listing->description)->toBe('A great car');
    expect($listing->image)->toBe('https://example.com/image.jpg');
    expect($listing->price_eur)->toBeNumeric();
});
