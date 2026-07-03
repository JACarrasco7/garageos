<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use Illuminate\Database\Eloquent\Factories\Factory;

class GarageFactory extends Factory
{
    protected $model = Garage::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Mi garaje',
        ];
    }
}
