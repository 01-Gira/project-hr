-- Extensions required by the Absensi schema.
--
-- btree_gist is needed by the EXCLUDE constraints that enforce non-overlapping
-- effective-dated salary periods (02_DATABASE_DESIGN.md §18, conflict C-11).
-- An application-level overlap check loses to concurrency: two simultaneous
-- inserts both read "no overlap" and both succeed. 05_IMPLEMENTATION_RULES.md §5
-- requires database-level protection on concurrency-sensitive paths.
--
-- This runs only on first initialisation of an empty data volume. The Phase 0
-- Laravel migration must also issue CREATE EXTENSION IF NOT EXISTS, because CI
-- and any already-created database will not have run this file.

CREATE EXTENSION IF NOT EXISTS btree_gist;
CREATE EXTENSION IF NOT EXISTS pgcrypto;
