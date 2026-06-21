<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_intervals', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 50)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('type', 50);
            $table->unsignedInteger('interval_km')->nullable();
            $table->unsignedTinyInteger('interval_months')->nullable();
            $table->string('description', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_intervals');
    }
};
