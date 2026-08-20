<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Add indexes to speed up common WHERE clauses
        Schema::table('kehadirans', function (Blueprint $table) {
            if (!Schema::hasColumn('kehadirans', 'tanggal')) return;
            $table->index(['tanggal'], 'idx_kehadiran_tanggal');
            if (Schema::hasColumn('kehadirans', 'jadwal_id')) {
                $table->index(['jadwal_id'], 'idx_kehadiran_jadwal');
            }
        });

        Schema::table('izin_gurus', function (Blueprint $table) {
            if (!Schema::hasColumn('izin_gurus', 'tanggal_mulai')) return;
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_izin_tanggal_range');
            if (Schema::hasColumn('izin_gurus', 'guru_id')) {
                $table->index(['guru_id'], 'idx_izin_guru_id');
            }
        });

        Schema::table('guru_pengganties', function (Blueprint $table) {
            // table name may vary; try to index tanggal if exists
            if (Schema::hasColumn('guru_pengganties', 'tanggal')) {
                $table->index(['tanggal'], 'idx_guru_pengganti_tanggal');
            }
        });
    }

    public function down()
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropIndex('idx_kehadiran_tanggal');
            $table->dropIndex('idx_kehadiran_jadwal');
        });

        Schema::table('izin_gurus', function (Blueprint $table) {
            $table->dropIndex('idx_izin_tanggal_range');
            $table->dropIndex('idx_izin_guru_id');
        });

        Schema::table('guru_pengganties', function (Blueprint $table) {
            $table->dropIndex('idx_guru_pengganti_tanggal');
        });
    }
}
