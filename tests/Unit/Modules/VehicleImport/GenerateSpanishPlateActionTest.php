<?php

use App\Modules\VehicleImport\Actions\GenerateSpanishPlateAction;

test('generates valid spanish plate format', function () {
    $generator = new GenerateSpanishPlateAction();
    $plate = $generator->execute();
    
    expect($plate)->toMatch('/^\d{4}-[A-Z]{3}$/');
});

test('generates unique plates', function () {
    $generator = new GenerateSpanishPlateAction();
    $plates = collect(range(1, 100))->map(fn() => $generator->execute());
    
    expect($plates->unique()->count())->toBe(100);
});