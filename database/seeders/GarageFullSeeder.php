<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Identity\Models\Garage;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehicleSpec;
use App\Modules\Vehicle\Models\KmHistory;
use App\Modules\Documents\Models\Document;
use App\Modules\Maintenance\Models\Workshop;
use App\Modules\Maintenance\Models\MaintenanceEntry;
use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Marketplace\Models\SaleReport;
use App\Modules\Marketplace\Models\MarketValue;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GarageFullSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuarios
        $owner = User::factory()->create([
            'name' => 'Carlos García',
            'email' => 'carlos@garageos.com',
            'role' => 'owner',
            'password' => bcrypt('password'),
        ]);

        $workshop = User::factory()->create([
            'name' => 'Taller Mecánico SL',
            'email' => 'taller@garageos.com',
            'role' => 'workshop',
            'password' => bcrypt('password'),
        ]);

        // 2. Garajes
        $garage = Garage::create([
            'user_id' => $owner->id,
            'name' => 'Garaje Principal',
        ]);

        // 3. Vehículos
        $vehicles = [
            [
                'plate' => '1234-ABC',
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2020,
                'fuel_type' => 'gasolina',
                'current_km' => 45000,
                'color' => 'Blanco',
                'specs' => ['engine_cc' => 1500, 'power_hp' => 150],
            ],
            [
                'plate' => '5678-XYZ',
                'brand' => 'BMW',
                'model' => 'Serie 3',
                'year' => 2019,
                'fuel_type' => 'diesel',
                'current_km' => 78000,
                'color' => 'Negro',
                'specs' => ['engine_cc' => 2000, 'power_hp' => 190],
            ],
            [
                'plate' => '9012-DEF',
                'brand' => 'Seat',
                'model' => 'León',
                'year' => 2022,
                'fuel_type' => 'hibrido',
                'current_km' => 12000,
                'color' => 'Rojo',
                'specs' => ['engine_cc' => 1400, 'power_hp' => 150],
            ],
        ];

        foreach ($vehicles as $v) {
            $specs = $v['specs'];
            unset($v['specs']);

            $vehicle = Vehicle::create(array_merge($v, [
                'garage_id' => $garage->id,
                'qr_token' => Str::random(64),
                'purchase_date' => now()->subYears(3),
                'purchase_price' => rand(15000, 35000),
            ]));

            VehicleSpec::create(array_merge(['vehicle_id' => $vehicle->id], $specs));

            // Km history
            KmHistory::create([
                'vehicle_id' => $vehicle->id,
                'km' => $vehicle->current_km - 10000,
                'recorded_at' => now()->subMonths(6),
                'source' => 'manual',
            ]);
            KmHistory::create([
                'vehicle_id' => $vehicle->id,
                'km' => $vehicle->current_km,
                'recorded_at' => now(),
                'source' => 'manual',
            ]);
        }

        // 4. Talleres
        $workshop1 = Workshop::create([
            'user_id' => $workshop->id,
            'name' => 'Taller Central Madrid',
            'address' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'phone' => '912345678',
            'is_verified' => true,
        ]);

        $workshop2 = Workshop::create([
            'user_id' => $workshop->id,
            'name' => 'Taller Rápido Barcelona',
            'address' => 'Av. Diagonal 100',
            'city' => 'Barcelona',
            'phone' => '987654321',
            'is_verified' => true,
        ]);

        // 5. Mantenimiento
        $vehicle = Vehicle::first();

        MaintenanceEntry::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop1->id,
            'type' => 'aceite',
            'title' => 'Cambio de aceite y filtros',
            'description' => 'Cambio aceite 5W30 + filtro aceite + filtro aire',
            'km_at_service' => 40000,
            'service_date' => now()->subMonths(3),
            'cost' => 85.50,
            'is_verified' => true,
        ]);

        MaintenanceEntry::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop1->id,
            'type' => 'neumaticos',
            'title' => 'Cambio de neumáticos delanteros',
            'description' => '2 neumáticos Michelin Primacy 4',
            'km_at_service' => 42000,
            'service_date' => now()->subMonths(2),
            'cost' => 320.00,
            'is_verified' => true,
        ]);

        MaintenanceEntry::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop2->id,
            'type' => 'itv',
            'title' => 'Revisión ITV',
            'description' => 'ITV favorable',
            'km_at_service' => 44000,
            'service_date' => now()->subMonth(),
            'cost' => 45.00,
            'is_verified' => true,
        ]);

        // 6. Alertas
        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'itv',
            'trigger_date' => now()->addMonths(2),
            'is_active' => true,
        ]);

        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'aceite',
            'trigger_km' => 50000,
            'advance_km' => 1000,
            'is_active' => true,
        ]);

        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'seguro',
            'trigger_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        // 7. Documentos
        Document::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'itv',
            'title' => 'Ficha ITV',
            'file_path' => 'documents/itv-ficha.pdf',
            'file_size' => 102400,
            'mime_type' => 'application/pdf',
            'km_at_time' => 44000,
            'document_date' => now()->subMonth(),
            'expiry_date' => now()->addMonths(2),
            'is_verified' => true,
        ]);

        Document::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'seguro',
            'title' => 'Póliza de seguro',
            'file_path' => 'documents/seguro-poliza.pdf',
            'file_size' => 204800,
            'mime_type' => 'application/pdf',
            'document_date' => now()->subMonths(6),
            'expiry_date' => now()->addMonth(),
            'is_verified' => true,
        ]);

        Document::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'factura',
            'title' => 'Factura compra',
            'file_path' => 'documents/factura-compra.pdf',
            'file_size' => 51200,
            'mime_type' => 'application/pdf',
            'document_date' => now()->subYears(3),
            'amount' => 25000.00,
            'is_verified' => true,
        ]);

        // 8. Marketplace
        MarketValue::create([
            'vehicle_id' => $vehicle->id,
            'estimated_value' => 18500.00,
            'min_value' => 15725.00,
            'max_value' => 21275.00,
            'source' => 'estimated',
        ]);

        SaleReport::create([
            'vehicle_id' => $vehicle->id,
            'token' => Str::random(64),
            'score' => 85,
            'pdf_path' => 'reports/sale-report-' . $vehicle->id . '.pdf',
            'expires_at' => now()->addDays(30),
        ]);

        // 9. Importaciones
        VehicleImport::create([
            'user_id' => $owner->id,
            'plate_original' => 'B-AB-1234',
            'plate_new' => '3456-GHI',
            'brand' => 'Mercedes',
            'model' => 'Clase C',
            'year' => 2021,
            'engine_cc' => 2000,
            'power_kw' => 150,
            'status' => 'pending',
        ]);

        VehicleImport::create([
            'user_id' => $owner->id,
            'plate_original' => 'K-XY-999',
            'plate_new' => '7890-JKL',
            'brand' => 'Audi',
            'model' => 'A4',
            'year' => 2020,
            'engine_cc' => 2000,
            'power_kw' => 140,
            'status' => 'approved',
        ]);
    }
}