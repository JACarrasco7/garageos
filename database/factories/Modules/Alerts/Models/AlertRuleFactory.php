<?php

namespace Database\Factories\Modules\Alerts\Models;

use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertRuleFactory extends Factory
{
    protected $model = AlertRule::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'type' => $this->faker->randomElement(['itv', 'seguro', 'aceite', 'revision']),
            'trigger_km' => $this->faker->optional()->numberBetween(10000, 30000),
            'trigger_date' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'advance_days' => 30,
            'advance_km' => 1000,
            'is_active' => true,
        ];
    }
}
