<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->timestamp('delivery_confirmed_at')->nullable()->after('itv_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->dropColumn('delivery_confirmed_at');
        });
    }
};
