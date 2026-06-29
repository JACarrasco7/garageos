<?php

namespace Database\Factories\Modules\Maintenance\Models;

use App\Modules\Maintenance\Models\ServicePack;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePackFactory extends Factory
{
    protected $model = ServicePack::class;

    public function definition(): array
    {
        return [
            'maintenance_type' => $this->faker->randomElement(['aceite', 'revision', 'itv']),
            'name' => 'Pack ' . $this->faker->word(),
            'items' => $this->faker->words(5),
        ];
    }
}