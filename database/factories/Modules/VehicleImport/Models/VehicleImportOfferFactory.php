<?php

namespace Database\Factories\Modules\VehicleImport\Models;

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
            'price' => $this->faker->randomFloat(2, 5000, 20000),
            'delivery_time_days' => $this->faker->numberBetween(14, 60),
            'warranty_months' => $this->faker->numberBetween(6, 24),
            'description' => $this->faker->sentence,
            'status' => 'pending',
        ];
    }
}
