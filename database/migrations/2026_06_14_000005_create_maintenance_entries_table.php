<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workshop_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', [
                'aceite', 'filtros', 'neumaticos', 'frenos',
                'distribucion', 'embrague', 'bateria', 'itv',
                'revision_general', 'otro'
            ]);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('km_at_service');
            $table->date('service_date');
            $table->decimal('cost', 10, 2)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'service_date']);
            $table->index(['vehicle_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_entries');
    }
};