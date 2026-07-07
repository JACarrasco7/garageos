<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use App\Modules\VehicleImport\Models\VehicleImportRating;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImportRatingFactory extends Factory
{
    protected $model = VehicleImportRating::class;

    public function definition(): array
    {
        return [
            'vehicle_import_offer_id' => VehicleImportOffer::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
