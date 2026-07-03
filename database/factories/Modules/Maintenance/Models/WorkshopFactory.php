<?php

namespace Database\Factories\Modules\Maintenance\Models;

use App\Models\User;
use App\Modules\Maintenance\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkshopFactory extends Factory
{
    protected $model = Workshop::class;

    public function definition(): array
    {
        // Coordenadas de ciudades españolas
        $cities = [
            ['name' => 'Madrid', 'lat' => 40.4168, 'lng' => -3.7038],
            ['name' => 'Barcelona', 'lat' => 41.3851, 'lng' => 2.1734],
            ['name' => 'Valencia', 'lat' => 39.4699, 'lng' => -0.3763],
            ['name' => 'Sevilla', 'lat' => 37.3891, 'lng' => -5.9845],
            ['name' => 'Bilbao', 'lat' => 43.2630, 'lng' => -2.9350],
        ];

        $city = $this->faker->randomElement($cities);

        return [
            'user_id' => User::factory(),
            'name' => 'Taller '.$this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'city' => $city['name'],
            'lat' => $city['lat'] + ($this->faker->randomFloat(4, -0.05, 0.05)),
            'lng' => $city['lng'] + ($this->faker->randomFloat(4, -0.05, 0.05)),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'is_verified' => true,
            'rating' => $this->faker->numberBetween(3, 5),
            'description' => $this->faker->sentence(),
        ];
    }
}