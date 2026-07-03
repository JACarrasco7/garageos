<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Database\Seeder;

class VehicleImportSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->create(['role' => 'owner']);
        }

        VehicleImport::create([
            'user_id' => $user->id,
            'plate_original' => 'B-AB-1234',
            'plate_new' => '1234-ABC',
            'brand' => 'Volkswagen',
            'model' => 'Golf',
            'year' => 2020,
            'engine_cc' => 1500,
            'power_kw' => 110,
            'status' => 'pending',
        ]);

        VehicleImport::create([
            'user_id' => $user->id,
            'plate_original' => 'MÜ-NC-567',
            'plate_new' => '5678-XYZ',
            'brand' => 'BMW',
            'model' => 'Serie 3',
            'year' => 2019,
            'engine_cc' => 2000,
            'power_kw' => 140,
            'status' => 'approved',
        ]);
    }
}
