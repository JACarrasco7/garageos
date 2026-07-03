<?php

use App\Modules\VehicleImport\Actions\ValidateGermanPlateAction;

test('valid german plate formats', function () {
    $validator = new ValidateGermanPlateAction;

    expect($validator->execute('B-AB-1234'))->toBeTrue();
    expect($validator->execute('MÜ-NC-123'))->toBeTrue();
    expect($validator->execute('K-FF-999'))->toBeTrue();
});

test('invalid german plate formats', function () {
    $validator = new ValidateGermanPlateAction;

    expect($validator->execute('1234-ABC'))->toBeFalse();
    expect($validator->execute('INVALID'))->toBeFalse();
    expect($validator->execute(''))->toBeFalse();
});
