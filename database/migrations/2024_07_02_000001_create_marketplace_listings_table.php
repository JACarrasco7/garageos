<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('user_id');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->boolean('is_negotiable')->default(true);
            $table->string('location_city', 100);
            $table->string('location_region', 100);
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->enum('status', ['draft', 'active', 'sold', 'reserved', 'expired'])->default('draft');
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('price');
            $table->index('location_city');
            $table->index('location_region');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_listings');
    }
};
