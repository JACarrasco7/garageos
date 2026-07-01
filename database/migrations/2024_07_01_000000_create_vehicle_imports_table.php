<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('set null');
            $table->string('plate_original', 20)->nullable()->comment('matrícula alemana');
            $table->string('plate_new', 10)->nullable()->comment('matrícula española definitiva');
            $table->string('brand')->nullable();
            $table->string('model', 80)->nullable();
            $table->year('year')->nullable();
            $table->integer('engine_cc')->nullable();
            $table->integer('power_kw')->nullable();
            $table->integer('co2_emissions')->nullable()->comment('g/km, clave para IEDMT');
            $table->string('origin_country', 2)->default('DE');
            $table->date('purchase_date')->nullable();
            $table->date('arrival_date')->nullable()->comment('dispara plazo 30 días ITV');
            $table->date('itv_deadline')->nullable()->comment('arrival_date + 30 días');
            $table->enum('current_step', [
                'purchase',
                'transport',
                'itv_inspection',
                'taxes',
                'dgt_registration',
                'plates',
                'completed'
            ])->default('purchase');
            $table->boolean('needs_homologation')->default(false);
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected'])->default('pending');
            $table->json('documents')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'current_step']);
            $table->index('plate_original');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_imports');
    }
};