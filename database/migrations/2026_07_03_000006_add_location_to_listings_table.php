<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE listings ADD COLUMN location geography(Point, 4326)');
        DB::statement('CREATE INDEX listings_location_idx ON listings USING GIST (location)');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS listings_location_idx');
        DB::statement('ALTER TABLE listings DROP COLUMN IF EXISTS location');
    }
};