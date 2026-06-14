<?php

namespace Database\Factories;

use App\Modules\Vehicle\Models\VehicleSpec;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleSpecFactory extends Factory
{
    protected $model = VehicleSpec::class;

    public function definition(): array
    {
        return [
            'engine_cc' => $this->faker->randomElement([1000, 1200, 1400, 1600, 1800, 2000, 2500, 3000]),
            'power_hp' => $this->faker->numberBetween(60, 400),
            'torque_nm' => $this->faker->numberBetween(100, 500),
            'transmission' => $this->faker->randomElement(['manual', 'automatico', 'cvt']),
            'drive' => $this->faker->randomElement(['fwd', 'rwd', '4wd', 'awd']),
            'doors' => $this->faker->randomElement([3, 5]),
            'seats' => $this->faker->randomElement([2, 4, 5, 7]),
        ];
    }
}