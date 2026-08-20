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
        Schema::create('guru_pengganties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('guru_asli_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('guru_pengganti_id')->constrained('gurus')->onDelete('cascade');
            $table->text('alasan_penggantian')->nullable();
            $table->enum('status_penggantian', ['dijadwalkan', 'selesai', 'dibatalkan'])->default('dijadwalkan');
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_pengganties');
    }
};
