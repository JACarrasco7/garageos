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
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->foreignId('vehicle_import_offer_id')->nullable()->after('id')->constrained('vehicle_import_offers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->dropForeign(['vehicle_import_offer_id']);
            $table->dropColumn('vehicle_import_offer_id');
        });
    }
};
