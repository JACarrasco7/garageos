<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_packs', function (Blueprint $table) {
            $table->json('affiliate_link_ids')->nullable()->after('items');
        });
    }

    public function down(): void
    {
        Schema::table('service_packs', function (Blueprint $table) {
            $table->dropColumn('affiliate_link_ids');
        });
    }
};
