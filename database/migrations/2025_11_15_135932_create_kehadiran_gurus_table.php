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
        Schema::create('kehadiran_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status_kehadiran', ['hadir', 'telat', 'tidak_hadir', 'izin', 'sakit'])->default('hadir');
            $table->string('waktu_datang', 20)->nullable(); // Format: "HH:MM:SS AM/PM"
            $table->integer('durasi_keterlambatan')->nullable()->comment('dalam menit');
            $table->text('keterangan')->nullable();
            $table->foreignId('diinput_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_gurus');
    }
};
