<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'guru_id')) {
                $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'guru_id')) {
                $table->dropConstrainedForeignId('guru_id');
            }
            if (Schema::hasColumn('users', 'kelas_id')) {
                $table->dropConstrainedForeignId('kelas_id');
            }
            if (Schema::hasColumn('users', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};
