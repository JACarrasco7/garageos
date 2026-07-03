<?php

namespace Database\Factories\Modules\Vehicle\Models;

use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'garage_id' => Garage::factory(),
            'plate' => strtoupper($this->faker->regexify('[0-9]{4}[A-Z]{3}')),
            'vin' => $this->faker->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'qr_token' => Str::random(64),
            'brand' => $this->faker->randomElement(['Seat', 'Volkswagen', 'Renault', 'Peugeot', 'Citroën', 'BMW', 'Audi', 'Toyota', 'Honda']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2010, 2024),
            'fuel_type' => $this->faker->randomElement(['gasolina', 'diesel', 'hibrido', 'electrico']),
            'color' => $this->faker->colorName(),
            'current_km' => $this->faker->numberBetween(0, 200000),
            'purchase_date' => $this->faker->optional()->date(),
            'purchase_price' => $this->faker->optional()->randomFloat(2, 5000, 50000),
            'is_active' => true,
        ];
    }
}
