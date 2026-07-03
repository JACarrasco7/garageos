<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id', 255)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status', 50);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('platform_fee_amount', 10, 2)->nullable();
            $table->decimal('platform_fee_percent', 5, 2)->default(8.00);
            $table->string('connected_account_id', 255)->nullable();
            $table->decimal('application_fee_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('stripe_payment_intent_id');
            $table->index('connected_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};
