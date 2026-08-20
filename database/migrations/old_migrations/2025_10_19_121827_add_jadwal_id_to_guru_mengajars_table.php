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
        // Step 1: Add jadwal_id and tanggal columns as nullable
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable()->after('id');
            $table->date('tanggal')->nullable()->after('jadwal_id');
        });

        // Step 2: Populate jadwal_id for existing records
        // For now, we'll just delete existing records since they don't have proper data
        DB::table('guru_mengajars')->truncate();

        // Step 3: Make jadwal_id required and add foreign key
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable(false)->change();
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('cascade');
        });

        // Step 4: Drop old columns that are now redundant
        Schema::table('guru_mengajars', function (Blueprint $table) {
            $table->dropColumn(['guru_id', 'mata_pelajaran_id', 'kelas_id', 'tahun_ajaran', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_mengajars', function (Blueprint $table) {
            // Restore old columns
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('tahun_ajaran', 10);
            $table->enum('semester', ['ganjil', 'genap']);

            // Drop new columns
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn(['jadwal_id', 'tanggal']);
        });
    }
};
