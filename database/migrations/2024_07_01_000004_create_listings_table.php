<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('source_url', 500);
            $table->enum('source_portal', ['mobile_de', 'autoscout24', 'other'])->default('other');
            $table->string('source_listing_id', 100)->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('model_description', 200)->nullable();
            $table->year('year')->nullable();
            $table->integer('mileage_km')->unsigned()->nullable();
            $table->enum('fuel_type', ['gasolina', 'diesel', 'hibrido', 'electrico', 'glp', 'otro'])->nullable();
            $table->integer('power_hp')->nullable();
            $table->integer('co2_emissions')->nullable()->comment('g/km, clave para el tasador');
            $table->enum('gearbox', ['manual', 'automatico'])->nullable();
            $table->decimal('price_eur', 10, 2)->nullable();
            $table->string('country', 2)->default('DE')->comment('ISO-3166-1 alpha-2 del país de origen');
            $table->enum('seller_type', ['dealer', 'private'])->nullable();
            $table->string('seller_name', 150)->nullable();
            $table->string('seller_location', 150)->nullable();
            $table->json('photos')->nullable();
            $table->json('raw_extracted_data')->nullable()->comment('payload completo de OpenGraph/JSON-LD/parser específico');
            $table->enum('extraction_method', ['opengraph', 'json_ld', 'portal_parser', 'manual'])->default('manual');
            $table->enum('extraction_status', ['pending', 'success', 'partial', 'failed', 'completed'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['brand', 'model']);
            $table->index('price_eur');
            $table->index('source_url');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
