<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add indexes for frequently queried columns if they don't already exist
            if (!Schema::hasIndex('users', 'idx_users_role')) {
                $table->index(['role'], 'idx_users_role');
            }
            if (!Schema::hasIndex('users', 'idx_users_guru_id')) {
                $table->index(['guru_id'], 'idx_users_guru_id');
            }
            if (!Schema::hasIndex('users', 'idx_users_kelas_id')) {
                $table->index(['kelas_id'], 'idx_users_kelas_id');
            }
            // Avoid composite index that might be too long, use separate indexes instead
            if (!Schema::hasIndex('users', 'idx_users_email')) {
                $table->index(['email'], 'idx_users_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes if they exist
            if (Schema::hasIndex('users', 'idx_users_role')) {
                $table->dropIndex(['idx_users_role']);
            }
            if (Schema::hasIndex('users', 'idx_users_guru_id')) {
                $table->dropIndex(['idx_users_guru_id']);
            }
            if (Schema::hasIndex('users', 'idx_users_kelas_id')) {
                $table->dropIndex(['idx_users_kelas_id']);
            }
            if (Schema::hasIndex('users', 'idx_users_email')) {
                $table->dropIndex(['idx_users_email']);
            }
        });
    }
};
