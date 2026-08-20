<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign key constraints
        Schema::table('kelas', function (Blueprint $table) {
            $table->foreign('wali_kelas_id')->references('id')->on('gurus')->onDelete('set null');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
        });

        Schema::table('kehadirans', function (Blueprint $table) {
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['wali_kelas_id']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropForeign(['guru_id']);
        });

        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropForeign(['jadwal_id']);
        });
    }
};
