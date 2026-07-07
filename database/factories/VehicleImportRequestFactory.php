<?php

namespace Database\Factories;

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
            'brand' => $this->faker->randomElement(['BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Porsche']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2015, 2024),
            'fuel_type' => $this->faker->randomElement(['gasolina', 'diesel', 'hibrido', 'electrico']),
            'mileage' => $this->faker->numberBetween(0, 150000),
            'budget_min' => $this->faker->optional()->randomFloat(2, 10000, 30000),
            'budget_max' => $this->faker->optional()->randomFloat(2, 30000, 80000),
            'url_link' => $this->faker->optional()->url(),
            'description' => $this->faker->optional()->sentence(),
            'status' => 'open',
        ];
    }
}
