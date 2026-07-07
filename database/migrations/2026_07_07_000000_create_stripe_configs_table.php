<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('secret')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->string('account_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_configs');
    }
};
