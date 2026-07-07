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
        Schema::create('import_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_import_id')->constrained()->onDelete('cascade');

            // Datos del Vendedor
            $table->string('seller_full_name');
            $table->string('seller_document_id'); // DNI/NIE/Pasaporte
            $table->string('seller_address');
            $table->string('seller_city');
            $table->string('seller_country');
            $table->string('seller_email')->nullable();
            $table->string('seller_phone')->nullable();

            // Datos del Comprador
            $table->string('buyer_full_name');
            $table->string('buyer_document_id'); // DNI/NIE/Pasaporte
            $table->string('buyer_address');
            $table->string('buyer_city');
            $table->string('buyer_country');
            $table->string('buyer_email')->nullable();
            $table->string('buyer_phone')->nullable();

            // Detalles del Contrato
            $table->decimal('agreed_price', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('contract_date');
            $table->string('status')->default('draft'); // draft, signed, verified, completed
            $table->string('pdf_path')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_contracts');
    }
};
