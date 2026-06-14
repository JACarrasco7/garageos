<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garage_id')->constrained()->cascadeOnDelete();
            $table->string('plate', 10)->comment('matrícula');
            $table->string('vin', 17)->unique()->nullable()->comment('bastidor');
            $table->string('qr_token', 64)->unique()->comment('token del QR físico');
            $table->string('brand', 50);
            $table->string('model', 80);
            $table->year('year');
            $table->enum('fuel_type', ['gasolina', 'diesel', 'hibrido', 'electrico', 'glp']);
            $table->string('color', 40)->nullable();
            $table->unsignedInteger('current_km')->default(0);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('photo', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['plate']);
            $table->index(['vin']);
            $table->index(['qr_token']);
            $table->index(['garage_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};