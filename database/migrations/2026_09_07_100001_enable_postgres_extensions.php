<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Enable the PostgreSQL extensions the schema depends on.
     *
     * btree_gist backs the EXCLUDE constraints that enforce non-overlapping
     * effective-dated salary periods (02_DATABASE_DESIGN.md §18, conflict C-11).
     * An application-level overlap check loses to concurrency: two simultaneous
     * inserts both read "no overlap" and both succeed.
     *
     * docker/postgres/init/01-extensions.sql also creates these, but only on
     * first initialisation of an empty volume — CI and any pre-existing volume
     * never run it, so the migration is the authoritative path.
     *
     * Both extensions are "trusted" in PostgreSQL 13+, so the database owner can
     * create them without superuser rights.
     *
     * This migration is deliberately not guarded by a driver check. ADR-0005
     * makes PostgreSQL the only supported engine; failing loudly on another
     * driver is correct, because silently skipping would let the salary overlap
     * constraint go missing without anyone noticing.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pgcrypto');
    }

    /**
     * Extensions are intentionally not dropped.
     *
     * Dropping btree_gist would cascade to every EXCLUDE constraint built on it,
     * silently removing the salary-overlap guarantee. A rollback that destroys a
     * data-integrity constraint is not an honest rollback.
     */
    public function down(): void
    {
        //
    }
};
