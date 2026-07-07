<?php

namespace App\Modules\VehicleImport\database\factories;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImportFactory extends Factory
{
    protected $model = VehicleImport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'importer_id' => User::factory(),
            'brand' => $this->faker->randomElement(['BMW', 'Mercedes', 'Audi', 'Volkswagen']),
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2015, 2024),
            'vin' => $this->faker->regexify('[A-Z0-9]{17}'),
            'origin_country' => 'DE',
            'status' => 'pending',
        ];
    }
}
