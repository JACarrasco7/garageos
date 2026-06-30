<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // 'autodoc', 'amazon', 'recambiosviaweb'
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('affiliate_url');
            $table->string('image_url')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['provider', 'is_active']);
            $table->index('product_sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
