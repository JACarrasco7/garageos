<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Actions\ValidateGermanPlateAction;
use App\Modules\VehicleImport\Actions\GenerateSpanishPlateAction;
use App\Modules\VehicleImport\Actions\ProcessImportAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleImportController extends Controller
{
    public function index(): Response
    {
        $imports = VehicleImport::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('VehicleImport/Index', [
            'imports' => $imports,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('VehicleImport/Create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'plate_original' => ['required', 'string', 'max:20'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . now()->year + 1],
            'engine_cc' => ['nullable', 'integer'],
            'power_kw' => ['nullable', 'integer'],
        ]);

        $validator = new ValidateGermanPlateAction();
        if (!$validator->execute($validated['plate_original'])) {
            return back()->withErrors(['plate_original' => 'Formato de matrícula alemán no válido']);
        }

        $plateGenerator = new GenerateSpanishPlateAction();
        $validated['plate_new'] = $plateGenerator->execute();
        $validated['user_id'] = auth()->id();

        VehicleImport::create($validated);

        return redirect()->route('imports.index')
            ->with('success', 'Solicitud de importación creada. Matrícula asignada: ' . $validated['plate_new']);
    }

    public function show(VehicleImport $import): Response
    {
        return Inertia::render('VehicleImport/Show', [
            'import' => $import,
        ]);
    }

    public function process(VehicleImport $import): \Illuminate\Http\RedirectResponse
    {
        $processor = new ProcessImportAction();
        $processor->execute($import);

        return redirect()->route('vehicles.show', $import->vehicle)
            ->with('success', 'Vehículo importado correctamente');
    }
}