<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valuations', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->integer('mileage_km')->default(0);
            $table->string('fuel_type')->default('gasoline');
            $table->integer('power_hp')->default(100);
            $table->decimal('estimated_value', 10, 2);
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('data_source')->default('calculated');
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();

            $table->index(['brand', 'model', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valuations');
    }
};