<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_import_id')->constrained()->onDelete('cascade');

            // Checks de Verificación
            $table->boolean('vin_verified')->default(false);
            $table->boolean('ownership_verified')->default(false); // DNI y Títulos
            $table->boolean('technical_data_verified')->default(false); // CoC y Ficha
            $table->boolean('itv_verified')->default(false);
            $table->boolean('legal_status_verified')->default(false); // Sin cargas/embargos

            // Datos del Auditor (Importador)
            $table->foreignId('verified_by')->constrained('users');
            $table->timestamp('verified_at')->nullable();

            // Resultado Final
            $table->string('overall_status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable();
            $table->string('report_pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_verifications');
    }
};
