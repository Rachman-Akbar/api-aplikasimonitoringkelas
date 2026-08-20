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
        Schema::table('jadwals', function (Blueprint $table) {
            // Ubah jam_mulai dan jam_selesai dari TIME ke VARCHAR(20)
            // untuk mendukung format AM/PM seperti "07:00:00 AM"
            $table->string('jam_mulai', 20)->change();
            $table->string('jam_selesai', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Kembalikan ke tipe TIME
            $table->time('jam_mulai')->change();
            $table->time('jam_selesai')->change();
        });
    }
};
