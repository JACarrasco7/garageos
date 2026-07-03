<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'itv', 'seguro', 'aceite', 'neumaticos',
                'revision', 'impuesto', 'bateria', 'custom',
            ]);
            $table->unsignedInteger('trigger_km')->nullable();
            $table->date('trigger_date')->nullable();
            $table->tinyInteger('advance_days')->default(30);
            $table->integer('advance_km')->default(1000);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
