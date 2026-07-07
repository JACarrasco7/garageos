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
        Schema::create('import_payment_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_import_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_intent_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('milestone', ['H1_reserva', 'H2_compra', 'H3_entrega']);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'authorized', 'paid', 'released', 'refunded'])->default('pending');
            $table->timestamp('released_at')->nullable();
            $table->text('release_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_payment_milestones');
    }
};
