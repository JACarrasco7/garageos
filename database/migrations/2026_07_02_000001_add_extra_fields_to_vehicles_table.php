<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('registration_date')->nullable()->after('year')->comment('Fecha de matriculación');
            $table->enum('eco_label', ['ECO', 'C', 'B', 'Zero', null])->nullable()->after('fuel_type')->comment('Etiqueta medioambiental DGT');
            $table->unsignedInteger('emissions_co2')->nullable()->after('eco_label')->comment('Emisiones CO2 en g/km');
            $table->decimal('official_consumption', 5, 1)->nullable()->after('emissions_co2')->comment('Consumo oficial l/100km');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['registration_date', 'eco_label', 'emissions_co2', 'official_consumption']);
        });
    }
};
