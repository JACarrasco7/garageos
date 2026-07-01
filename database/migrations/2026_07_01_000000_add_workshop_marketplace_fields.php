<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->integer('rating')->default(0);
            $table->text('description')->nullable();
            $table->string('logo', 255)->nullable();
            $table->json('services')->nullable();
        });

        Schema::create('workshop_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_reviews');
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['rating', 'description', 'logo', 'services']);
        });
    }
};