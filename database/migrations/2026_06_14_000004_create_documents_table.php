<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['factura', 'itv', 'seguro', 'impuesto', 'otro']);
            $table->string('title', 150)->nullable();
            $table->string('file_path', 255);
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->unsignedInteger('km_at_time')->nullable();
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->json('parsed_data')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'type']);
            $table->index(['expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};