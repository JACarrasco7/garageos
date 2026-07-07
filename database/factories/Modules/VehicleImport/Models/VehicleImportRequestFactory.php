<?php

namespace Database\Factories\Modules\VehicleImport\Models;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImportRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImportRequestFactory extends Factory
{
    protected $model = VehicleImportRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand' => $this->faker->randomElement(['BMW', 'Mercedes', 'Audi']),
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2015, 2024),
            'status' => 'open',
        ];
    }
}
