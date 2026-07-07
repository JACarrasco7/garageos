<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use App\Modules\VehicleImport\Models\VehicleImportRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImportOfferFactory extends Factory
{
    protected $model = VehicleImportOffer::class;

    public function definition(): array
    {
        return [
            'vehicle_import_request_id' => VehicleImportRequest::factory(),
            'user_id' => User::factory(),
            'price' => $this->faker->randomFloat(2, 15000, 50000),
            'delivery_time_days' => $this->faker->numberBetween(7, 30),
            'warranty_months' => $this->faker->numberBetween(0, 24),
            'description' => $this->faker->optional()->sentence(),
            'status' => 'pending',
            'is_boosted' => false,
            'boosted_until' => null,
        ];
    }
}
