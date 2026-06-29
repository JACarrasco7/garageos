<?php

namespace Database\Factories\Modules\Marketplace\Models;

use App\Modules\Marketplace\Models\MarketValue;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketValueFactory extends Factory
{
    protected $model = MarketValue::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'estimated_value' => $this->faker->randomFloat(2, 5000, 50000),
            'min_value' => $this->faker->optional()->randomFloat(2, 4000, 45000),
            'max_value' => $this->faker->optional()->randomFloat(2, 5500, 55000),
            'source' => 'scraper',
            'recorded_at' => now(),
        ];
    }
}
