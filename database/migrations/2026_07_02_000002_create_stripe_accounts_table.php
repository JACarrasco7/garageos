<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_account_id', 255)->unique();
            $table->boolean('charges_enabled')->default(false);
            $table->boolean('payouts_enabled')->default(false);
            $table->string('country', 2);
            $table->string('business_type', 50)->nullable();
            $table->json('business_profile')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamp('tos_acceptance_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'onboarding_completed']);
            $table->index('stripe_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_accounts');
    }
};
