<?php

namespace Database\Factories\Modules\Listings\Models;

use App\Models\User;
use App\Modules\Listings\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        $brands = ['BMW', 'Audi', 'Mercedes', 'Volkswagen', 'Seat', 'Renault', 'Peugeot'];
        $models = ['320d', 'A4', 'Clase C', 'Golf', 'Ibiza', 'Clio', '208'];
        $brand = $this->faker->randomElement($brands);
        $model = $this->faker->randomElement($models);

        return [
            'created_by_user_id' => User::factory(),
            'title' => "$brand $model",
            'description' => $this->faker->sentence(),
            'source_url' => $this->faker->url(),
            'source_portal' => 'other',
            'brand' => $brand,
            'model' => $model,
            'year' => $this->faker->numberBetween(2010, 2024),
            'mileage_km' => $this->faker->numberBetween(10000, 200000),
            'fuel_type' => 'diesel',
            'power_hp' => $this->faker->numberBetween(90, 300),
            'gearbox' => 'manual',
            'price_eur' => $this->faker->numberBetween(5000, 50000),
            'country' => 'DE',
            'seller_type' => 'dealer',
            'extraction_method' => 'manual',
            'extraction_status' => 'completed',
            'is_active' => true,
        ];
    }
}
