<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_fees', function (Blueprint $table) {
            $table->string('invoice_path')->nullable()->after('processed_at');
        });
    }

    public function down(): void
    {
        Schema::table('platform_fees', function (Blueprint $table) {
            $table->dropColumn('invoice_path');
        });
    }
};
