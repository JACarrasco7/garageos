<?php

namespace Database\Factories\Modules\Vehicle\Models;

use App\Modules\Vehicle\Models\KmHistory;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class KmHistoryFactory extends Factory
{
    protected $model = KmHistory::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'km' => $this->faker->numberBetween(0, 200000),
            'recorded_at' => $this->faker->date(),
            'source' => $this->faker->randomElement(['manual', 'document', 'obd']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}