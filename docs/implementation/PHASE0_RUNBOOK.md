# Phase 0 Runbook

Commands to run **on the Mac**. The assisting shell is a Linux VM with no PHP and no network egress
(see `REPOSITORY_AUDIT.md` §1.5), so every step here needs a human at a terminal.

Run one step at a time and check its verification block before continuing. Steps are ordered so that
a failure never leaves the repository in a state the next step depends on.

---

## Step 1 — Start the services

```bash
cd ~/Projects/project-hr
docker compose up -d
docker compose ps
```

**Verify** — both containers `healthy`, and the extension present:

```bash
docker compose exec postgres psql -U absensi -d absensi -c "select version();"
docker compose exec postgres psql -U absensi -d absensi -c "\dx"
docker compose exec redis redis-cli ping        # PONG
```

Expect PostgreSQL **17.x** and `btree_gist` in the extension list.

Ports are deliberately **5433** (PostgreSQL) and **6380** (Redis) on the host, because your Homebrew
PostgreSQL 14 and Redis 8 may already hold 5432 and 6379. Nothing in this project touches those.

If `docker` is not installed: `brew install --cask docker`, then launch Docker Desktop once.

---

## Step 2 — Install Laravel

**Read this before running it.** `composer create-project laravel/laravel .` **will fail here.**
Composer requires the target directory to be empty, and this repository already contains `docs/`,
`docker/`, `docker-compose.yml` and `.gitignore`. Installing into a temporary directory and moving
the contents across is the supported way to add Laravel to an existing repository:

```bash
cd ~/Projects/project-hr

# Sanity: the working tree must be clean before this step, so the Laravel
# skeleton arrives as one reviewable diff.
git status --short          # expect no output

composer create-project laravel/laravel .laravel-tmp

# Move everything across, including dotfiles, without clobbering what exists.
shopt -s dotglob
for f in .laravel-tmp/*; do
  base=$(basename "$f")
  if [ -e "$base" ]; then
    echo "SKIP (already exists): $base"
  else
    mv "$f" .
  fi
done
shopt -u dotglob
rm -rf .laravel-tmp
```

`.gitignore` is the one file expected to be reported as `SKIP` — ours is already in place and is
deliberately kept, since it was written ahead of this step precisely so `vendor/` and `.env` are
never committed. If anything **else** is skipped, stop and report it rather than overwriting.

**Verify**:

```bash
php artisan --version           # record this exact string
git status --short | head -30   # vendor/ and .env must NOT appear
```

If `vendor/`, `node_modules/` or `.env` appear in `git status`, the `.gitignore` did not survive the
move. Stop — do not commit — and say so.

**Record the resolved Laravel version.** It was not pinned in advance because the assisting shell
cannot reach Packagist to check what `create-project` resolves against PHP 8.4. Paste the
`php artisan --version` output back and it will be written into the status document and the
`composer.json` constraint.

---

## Step 3 — Confirm PHP extensions  *(outstanding P0-3b)*

```bash
php -m | grep -Ei '^(pdo_pgsql|pgsql|redis|bcmath|intl|zip|gd|mbstring|openssl|fileinfo)$'
```

| Extension | Needed for | If missing |
|---|---|---|
| `pdo_pgsql`, `pgsql` | all database access | `brew install php` normally includes it; otherwise the build lacks PostgreSQL support and must be reinstalled |
| `bcmath` | **money.** `numeric(18,2)` salary arithmetic (`02` §1 forbids floats) | required, not optional |
| `mbstring`, `openssl`, `fileinfo` | Laravel core | required |
| `intl` | date/number formatting for `Asia/Jakarta` display | required |
| `zip` | Phase 8 Excel export | needed by Phase 8 |
| `gd` | Phase 2 ID card rendering | needed by Phase 2 |
| `redis` | Redis driver | **optional** — if absent, use `composer require predis/predis` and set `REDIS_CLIENT=predis` |

Report which of these are missing. Only `bcmath` and the PostgreSQL pair block Phase 0.

---

## Step 4 — Configure the environment

Applied after Step 2 succeeds. `.env` values (and the same keys in `.env.example`, minus secrets):

```dotenv
APP_NAME=Absensi
APP_TIMEZONE=UTC

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=absensi
DB_USERNAME=absensi
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1
REDIS_PORT=6380

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

**`APP_TIMEZONE=UTC` is deliberate and must not be changed to `Asia/Jakarta`.** `02` §1 stores all
business timestamps in UTC and resolves the display timezone from company settings. Setting the
application timezone to Jakarta silently writes local times into columns the rest of the system reads
as UTC — a corruption that only becomes visible in attendance reports, long after the cause.

**Verify**:

```bash
php artisan migrate
php artisan tinker --execute="echo config('database.default');"   # pgsql
```

---

## Step 5 — Report back

Paste:

1. `php artisan --version`
2. the `php -m` extension results from Step 3
3. `php artisan migrate` output
4. `git status --short`

The remaining Phase 0 work — RBAC, seeders, the scope resolver, audit foundation, enums, tests,
Pint/Larastan, CI — will then be authored directly into the repository. Those files can be written
without a running PHP, but they **cannot be verified** here: `php artisan test`, `pint` and `phpstan`
have to run on the Mac, and per `05_IMPLEMENTATION_RULES.md` §14 nothing is marked DONE until that
output exists.
