<?php

namespace Database\Factories\Modules\Vehicle\Models;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehiclePhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiclePhotoFactory extends Factory
{
    protected $model = VehiclePhoto::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'file_path' => 'vehicles/test/fake.jpg',
            'category' => $this->faker->randomElement([
                'principal',
                'frontal',
                'lateral',
                'trasero',
                'interior',
                'motor',
                'averia',
                'daño',
                'documento',
                'antes_reparacion',
                'despues_reparacion',
            ]),
            'caption' => $this->faker->optional()->sentence(),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'file_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(100000, 5000000),
        ];
    }
}
