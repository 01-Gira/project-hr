#!/bin/bash
# Creates the database the Pest/PHPUnit suite uses (ADR-0005, phpunit.xml).
#
# Runs only on first initialisation of an empty data volume. If the volume
# already exists, create it by hand:
#   docker compose exec postgres createdb -U absensi absensi_test
set -euo pipefail

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-SQL
    CREATE DATABASE absensi_test OWNER $POSTGRES_USER;
SQL

# btree_gist and pgcrypto are "trusted" extensions in PostgreSQL 13+, so the
# database owner can create them; the Phase 0 migration does this too, which is
# what covers CI and any pre-existing volume.
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname absensi_test <<-SQL
    CREATE EXTENSION IF NOT EXISTS btree_gist;
    CREATE EXTENSION IF NOT EXISTS pgcrypto;
SQL
