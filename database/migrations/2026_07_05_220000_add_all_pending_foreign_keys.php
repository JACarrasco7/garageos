<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pgHasPostgis = false;
        if (DB::connection()->getDriverName() === 'pgsql') {
            $result = DB::selectOne("SELECT 1 FROM pg_available_extensions WHERE name = 'postgis'");
            $pgHasPostgis = (bool) $result;
        }

        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('set null');
        });

        Schema::table('maintenance_entries', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');
            $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_entries', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['document_id']);
            $table->dropForeign(['workshop_id']);
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['garage_id']);
        });
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
        });
    }
};
