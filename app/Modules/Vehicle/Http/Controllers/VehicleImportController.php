<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use League\Csv\Reader;

class VehicleImportController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Vehicle/Import');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $csv = Reader::createFromPath($request->file('file')->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $garage = Garage::firstOrCreate(['user_id' => auth()->id()]);

        foreach ($csv->getRecords() as $record) {
            Vehicle::create([
                'garage_id' => $garage->id,
                'plate' => $record['plate'] ?? $record['matricula'],
                'brand' => $record['brand'] ?? $record['marca'],
                'model' => $record['model'] ?? $record['modelo'],
                'year' => (int) ($record['year'] ?? $record['año']),
                'fuel_type' => $record['fuel_type'] ?? $record['combustible'] ?? 'gasolina',
                'current_km' => (int) ($record['current_km'] ?? $record['km'] ?? 0),
            ]);
        }

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículos importados correctamente');
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $vehicles = Vehicle::whereHas('garage', fn($q) => $q->where('user_id', auth()->id()))
            ->get(['plate', 'brand', 'model', 'year', 'fuel_type', 'current_km']);

        $headers = ['plate', 'brand', 'model', 'year', 'fuel_type', 'current_km'];

        return response()->stream(function () use ($vehicles, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($vehicles as $vehicle) {
                fputcsv($handle, $vehicle->toArray());
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehiculos.csv"',
        ]);
    }
}