<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\VehicleImport\Enums\ImportStep;
use App\Modules\VehicleImport\Events\ImportStepCompleted;
use App\Modules\VehicleImport\Models\VehicleImport;

class AdvanceImportStepAction
{
    public function execute(VehicleImport $import, ImportStep $targetStep): VehicleImport
    {
        $currentOrder = $import->current_step->getOrder();
        $targetOrder = $targetStep->getOrder();

        if ($targetOrder <= $currentOrder) {
            throw new \InvalidArgumentException('No se puede retroceder pasos');
        }

        if (! $import->canAdvanceToStep($targetStep)) {
            throw new \InvalidArgumentException('Faltan documentos requeridos para avanzar a este paso');
        }

        $import->update([
            'current_step' => $targetStep,
        ]);

        event(new ImportStepCompleted($import, $targetStep));

        return $import->fresh();
    }

    public function autoAdvanceIfReady(VehicleImport $import): ?VehicleImport
    {
        $nextStep = $import->current_step->getNext();

        if (! $nextStep) {
            return null;
        }

        if ($import->canAdvanceToStep($nextStep)) {
            return $this->execute($import, $nextStep);
        }

        return null;
    }

    public function completeImport(VehicleImport $import): VehicleImport
    {
        if ($import->current_step !== ImportStep::PLATES) {
            throw new \InvalidArgumentException('Solo se puede completar si el paso actual es PLATES');
        }

        if (! $import->isStepCompleted(ImportStep::PLATES)) {
            throw new \InvalidArgumentException('Faltan documentos requeridos para completar');
        }

        $import->update([
            'current_step' => ImportStep::COMPLETED,
            'status' => 'completed',
        ]);

        event(new ImportStepCompleted($import, ImportStep::COMPLETED));

        $this->syncToGarage($import);

        return $import->fresh();
    }

    protected function syncToGarage(VehicleImport $import): void
    {
        $vehicle = Vehicle::updateOrCreate(
            [
                'plate' => $import->plate_new,
            ],
            [
                'garage_id' => $import->user->garages()->first()?->id,
                'brand' => $import->brand,
                'model' => $import->model,
                'year' => $import->year,
                'engine_cc' => $import->engine_cc,
                'power_kw' => $import->power_kw,
                'imported_from' => $import->origin_country,
                'import_date' => $import->purchase_date,
            ]
        );

        $import->update([
            'vehicle_id' => $vehicle->id,
        ]);
    }
}
