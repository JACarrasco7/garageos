<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato de Importación</title>
</head>
<body>
    <h1>CONTRATO DE IMPORTACIÓN DE VEHÍCULO</h1>

    <p>En {{ $date }}</p>

    <h2>Partes Contratantes</h2>
    <p><strong>Cliente:</strong> {{ $client->name }}</p>
    <p><strong>Proveedor:</strong> {{ $provider->name }}</p>

    <h2>Detalles del Vehículo</h2>
    <p><strong>Marca:</strong> {{ $request->brand }}</p>
    <p><strong>Modelo:</strong> {{ $request->model }}</p>
    <p><strong>Año:</strong> {{ $request->year }}</p>

    <h2>Condiciones de la Oferta</h2>
    <p><strong>Precio:</strong> {{ $offer->price }}€</p>
    <p><strong>Tiempo de entrega:</strong> {{ $offer->delivery_time_days }} días</p>
    <p><strong>Garantía:</strong> {{ $offer->warranty_months }} meses</p>

    <h2>Descripción</h2>
    <p>{{ $offer->description }}</p>
</body>
</html>
