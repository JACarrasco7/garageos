<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null')->after('user_id');
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('set null')->after('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_imports', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['listing_id']);
        });
    }
};
