<?php

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Add the columns 02_DATABASE_DESIGN.md §2 requires on users.
     *
     * Staged per the project's migration conventions: add the column nullable,
     * backfill existing rows in bounded chunks, then apply the unique index.
     * Adding a unique column to a populated table in one step is not safe.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('status')->default(UserStatus::Active->value)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });

        DB::table('users')
            ->whereNull('uuid')
            ->orderBy('id')
            ->chunkById(500, function ($users): void {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['uuid' => (string) Str::uuid7()]);
                }
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('uuid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['uuid']);
            $table->dropIndex(['status']);
            $table->dropColumn(['uuid', 'status', 'last_login_at']);
        });
    }
};
