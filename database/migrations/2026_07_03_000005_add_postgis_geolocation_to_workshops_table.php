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

        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        DB::statement("
            ALTER TABLE workshops
            ADD COLUMN location geography(Point, 4326)
            GENERATED ALWAYS AS (
                ST_SetSRID(ST_MakePoint(lng, lat), 4326)::geography
            ) STORED
        ");

        DB::statement('CREATE INDEX workshops_location_idx ON workshops USING GIST (location)');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS workshops_location_idx');
        DB::statement('ALTER TABLE workshops DROP COLUMN IF EXISTS location');
    }
};
