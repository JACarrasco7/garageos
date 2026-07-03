<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->enum('category', [
                'principal',
                'frontal',
                'lateral',
                'trasero',
                'interior',
                'motor',
                'averia',
                'daño',
                'documento',
                'antes_reparacion',
                'despues_reparacion',
            ])->default('principal');
            $table->string('caption', 200)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('file_type', 20)->default('image/jpeg');
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'category', 'sort_order']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_photos');
    }
};
