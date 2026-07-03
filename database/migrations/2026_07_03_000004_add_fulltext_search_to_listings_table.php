<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("
            ALTER TABLE listings
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('spanish', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('spanish', coalesce(description, '')), 'B') ||
                setweight(to_tsvector('spanish', coalesce(brand, '') || ' ' || coalesce(model, '')), 'C') ||
                setweight(to_tsvector('spanish', coalesce(seller_location, '')), 'D')
            ) STORED
        ");

        DB::statement('CREATE INDEX listings_search_vector_idx ON listings USING GIN (search_vector)');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS listings_search_vector_idx');
        DB::statement('ALTER TABLE listings DROP COLUMN IF EXISTS search_vector');
    }
};
