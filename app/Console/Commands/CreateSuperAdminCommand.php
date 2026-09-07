<?php

namespace App\Console\Commands;

use App\Actions\Audit\RecordAuditLog;
use App\Enums\AuditAction;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Bootstraps the first super-admin account.
 *
 * PHASE 0 requires a repeatable super-admin bootstrap. Deliberately a command
 * rather than a seeder: seeders run unattended in bulk, and an account holding
 * every permission should be created by a person who supplies its credential.
 *
 * The password is never accepted as a command-line option. Options are visible
 * in shell history and in the process list to every user on the machine, so it
 * is read from ABSENSI_SUPER_ADMIN_PASSWORD or prompted for without echo.
 */
class CreateSuperAdminCommand extends Command
{
    protected $signature = 'absensi:create-super-admin
                            {--email= : Email address of the super-admin}
                            {--name= : Display name, used only when creating a new account}';

    protected $description = 'Create or promote a user to the super-admin role';

    public function handle(RecordAuditLog $audit): int
    {
        $email = $this->option('email') ?: $this->ask('Super-admin email address');

        if (! is_string($email) || Validator::make(['email' => $email], ['email' => ['required', 'email']])->fails()) {
            $this->error('A valid email address is required.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user === null) {
            $user = $this->createUser($email);

            if ($user === null) {
                return self::FAILURE;
            }
        } else {
            // Promoting an existing account must never silently reset its
            // password; that would be a privilege-escalation footgun.
            $this->line("Existing account found for {$email}; promoting without altering its password.");
        }

        if ($user->hasRole(RoleName::SuperAdmin->value)) {
            $this->info("{$email} already holds the super-admin role. Nothing to do.");

            return self::SUCCESS;
        }

        $user->assignRole(RoleName::SuperAdmin->value);

        $audit->handle(
            action: AuditAction::RolePermissionChanged,
            subject: $user,
            newValues: ['roles' => [RoleName::SuperAdmin->value]],
            reason: 'Super-admin bootstrap via absensi:create-super-admin',
        );

        $this->info("{$email} was granted the super-admin role.");

        return self::SUCCESS;
    }

    private function createUser(string $email): ?User
    {
        $name = $this->option('name') ?: $this->ask('Display name');

        $password = config('absensi.super_admin_password') ?: $this->secret('Password (input hidden)');

        $validator = Validator::make(
            ['name' => $name, 'password' => $password],
            ['name' => ['required', 'string', 'max:255'], 'password' => ['required', 'string', Password::default()]],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return null;
        }

        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
    }
}
