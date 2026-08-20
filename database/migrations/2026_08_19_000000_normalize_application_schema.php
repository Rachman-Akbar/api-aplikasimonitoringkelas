<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('status', 30)->default('aktif')->change();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('status', 30)->default('aktif')->change();
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->string('status', 30)->default('aktif')->change();
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('status', 30)->default('aktif')->change();
            if (!Schema::hasColumn('jadwals', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 20)->nullable()->after('jam_selesai');
            }
        });

        Schema::table('kehadirans', function (Blueprint $table) {
            if (!Schema::hasColumn('kehadirans', 'diinput_oleh')) {
                $table->foreignId('diinput_oleh')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('failed_import_rows', function (Blueprint $table) {
            if (!Schema::hasColumn('failed_import_rows', 'row_number')) {
                $table->unsignedInteger('row_number')->nullable()->after('import_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (Schema::hasColumn('jadwals', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
        });
    }
};
