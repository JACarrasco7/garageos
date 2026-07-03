<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporary_plates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('vehicle_imports')->onDelete('cascade');
            $table->string('plate_number', 20);
            $table->date('issued_at');
            $table->date('expires_at')->comment('normalmente issued_at + 2 meses');
            $table->boolean('is_extended')->default(false);
            $table->timestamps();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_plates');
    }
};
