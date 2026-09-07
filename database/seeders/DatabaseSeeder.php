<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * WithoutModelEvents was deliberately removed. It wraps the seeder in
     * Model::withoutEvents(), which suppresses the `creating` hook that HasUuid
     * relies on — every seeded user would be written with a null uuid. The
     * column is nullable so nothing would fail loudly; the rows would simply
     * have no public identifier. A test asserts seeded users have one.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Local convenience account only. Seeding a known-address user into a
        // production database is an avoidable risk, so it is environment-gated
        // rather than unconditional.
        if (app()->environment('local')) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
