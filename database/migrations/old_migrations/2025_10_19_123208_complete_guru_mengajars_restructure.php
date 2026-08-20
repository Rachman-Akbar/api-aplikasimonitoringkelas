<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear existing data
        DB::table('guru_mengajars')->truncate();

        // Add tanggal column
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->date('tanggal')->after('jadwal_id');
        });

        // Make jadwal_id not nullable and add foreign key
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable(false)->change();
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('cascade');
        });

        // Drop old columns
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->dropColumn(['guru_id', 'mata_pelajaran_id', 'kelas_id', 'tahun_ajaran', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old structure
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('tahun_ajaran', 10);
            $table->enum('semester', ['ganjil', 'genap']);

            $table->dropForeign(['jadwal_id']);
            $table->dropColumn(['tanggal']);
        });
    }
};
