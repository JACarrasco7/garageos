<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Importvertrag</title>
</head>
<body>
    <h1>KAUFVERTRAG FÜR Fahrzeugimport</h1>

    <p>Am {{ $date }}</p>

    <h2>Vertragsparteien</h2>
    <p><strong>Käufer:</strong> {{ $client->name }}</p>
    <p><strong>Anbieter:</strong> {{ $provider->name }}</p>

    <h2>Fahrzeugdetails</h2>
    <p><strong>Marke:</strong> {{ $request->brand }}</p>
    <p><strong>Modell:</strong> {{ $request->model }}</p>
    <p><strong>Jahr:</strong> {{ $request->year }}</p>

    <h2>Angebotsbedingungen</h2>
    <p><strong>Preis:</strong> {{ $offer->price }}€</p>
    <p><strong>Lieferzeit:</strong> {{ $offer->delivery_time_days }} Tage</p>
    <p><strong>Garantie:</strong> {{ $offer->warranty_months }} Monate</p>

    <h2>Beschreibung</h2>
    <p>{{ $offer->description }}</p>
</body>
</html>
