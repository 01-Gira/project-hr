<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The audit trail defined in 02_DATABASE_DESIGN.md §17.
     *
     * There is no updated_at: the table is append-only, and a mutable audit log
     * is not an audit log. AuditLog enforces the same invariant at the model
     * level.
     *
     * ip_address uses PostgreSQL's native inet type rather than a string, which
     * is one of the reasons ADR-0005 rules out SQLite for the test suite.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();

            // Null on delete rather than cascade: removing a user must never
            // erase the record of what they did.
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');

            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->text('reason')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id')->nullable();

            $table->timestamp('created_at')->nullable();

            // 02_DATABASE_DESIGN.md §19. Both are composite and lead with the
            // column the query filters on, so neither duplicates the other.
            $table->index(['subject_type', 'subject_id', 'created_at']);
            $table->index(['actor_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
