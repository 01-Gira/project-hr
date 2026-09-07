<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

/**
 * Seeds the 52 permissions from 02_DATABASE_DESIGN.md §2.
 *
 * Idempotent and additive: re-running creates only what is missing and never
 * removes a permission. Permissions are referenced by policies and by seeded
 * role assignments, so deleting one on a re-seed would silently revoke access.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionName::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        Artisan::call('permission:cache-reset');
    }
}
