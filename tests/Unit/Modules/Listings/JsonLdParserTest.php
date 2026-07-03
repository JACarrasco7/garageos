<?php

use App\Modules\Listings\Services\Parsers\JsonLdParser;

test('it can parse vehicle json-ld', function () {
    $parser = new JsonLdParser();

    $html = '<html><head><script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Vehicle",
      "brand": "Toyota",
      "model": "Corolla",
      "description": "A great car",
      "image": "https://example.com/image.jpg",
      "offers": {
        "@type": "Offer",
        "price": "25000.00",
        "priceCurrency": "EUR"
      }
    }
    </script></head></html>';

    $result = $parser->parse($html);

    expect($result)->toBeArray();
    expect($result['brand'])->toBe('Toyota');
    expect($result['model'])->toBe('Corolla');
    expect($result['description'])->toBe('A great car');
    expect($result['image'])->toBe('https://example.com/image.jpg');
    expect($result['price_eur'])->toBe(25000.0);
    expect($result['currency'])->toBe('EUR');
});

test('it returns empty array if no json-ld found', function () {
    $parser = new JsonLdParser();
    $html = '<html><body>No json-ld here</body></html>';

    $result = $parser->parse($html);

    expect($result)->toBeArray()->toBeEmpty();
});

test('it returns empty array if json-ld is not a vehicle', function () {
    $parser = new JsonLdParser();
    $html = '<html><head><script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Person",
      "name": "John Doe"
    }
    </script></head></html>';

    $result = $parser->parse($html);

    expect($result)->toBeArray()->toBeEmpty();
});

test('it can parse multiple json-ld blocks and pick the first vehicle', function () {
    $parser = new JsonLdParser();
    $html = '<html><head>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Person",
      "name": "John Doe"
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Vehicle",
      "brand": "Honda",
      "model": "Civic"
    }
    </script>
    </head></html>';

    $result = $parser->parse($html);

    expect($result)->toBeArray();
    expect($result['brand'])->toBe('Honda');
    expect($result['model'])->toBe('Civic');
});
