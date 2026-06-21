<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('engine_cc')->nullable();
            $table->integer('power_hp')->nullable();
            $table->integer('torque_nm')->nullable();
            $table->enum('transmission', ['manual', 'automatico', 'cvt'])->nullable();
            $table->enum('drive', ['fwd', 'rwd', '4wd', 'awd'])->nullable();
            $table->tinyInteger('doors')->nullable();
            $table->tinyInteger('seats')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_specs');
    }
};
