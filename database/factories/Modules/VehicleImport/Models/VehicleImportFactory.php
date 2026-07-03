<?php

namespace Database\Factories\Modules\VehicleImport\Models;

use App\Models\User;
use App\Modules\VehicleImport\Enums\ImportStep;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImportFactory extends Factory
{
    protected $model = VehicleImport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plate_original' => $this->faker->regexify('[A-Z]{1,3}-[A-Z]{1,2}-\d{1,4}'),
            'plate_new' => null,
            'brand' => $this->faker->randomElement(['BMW', 'Audi', 'Volkswagen', 'Seat', 'Toyota']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2015, 2023),
            'engine_cc' => $this->faker->numberBetween(1000, 3000),
            'power_kw' => $this->faker->numberBetween(50, 200),
            'co2_emissions' => $this->faker->numberBetween(80, 200),
            'origin_country' => 'DE',
            'purchase_date' => $this->faker->optional()->date(),
            'arrival_date' => null,
            'itv_deadline' => null,
            'current_step' => ImportStep::PURCHASE,
            'needs_homologation' => false,
            'status' => 'pending',
            'documents' => null,
            'rejection_reason' => null,
        ];
    }
}
